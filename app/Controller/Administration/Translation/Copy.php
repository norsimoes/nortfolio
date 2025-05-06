<?php

namespace Controller\Administration\Translation;

use Lib\App;

/**
 * Copy
 *
 * Clone existing translation records.
 */
class Copy extends App
{
    protected ?object $_activeModule = null;
    protected array $_moduleData = [];
    protected array $_i18n = [];

    /**
     * Class constructor
     *
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();

		// Active module
        $this->_activeModule = (new \Model\Core\Module())->getByRoute($this->router->getDbRoute());

        // Check login
        $this->requiresAuthentication();

        // Check permissions
        $this->checkPermission();

        // Get modules data
        $this->_moduleData = (new \Controller\Administration\Translation\Dashboard())->getModuleData();

        // Load vocabulary
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
        $this->_i18n = (new \Model\Core\I18nFile())->get('Translation');
    }

    /**
     * Index
     *
     * Default class method.
     *
     * @throws \Exception
     */
    public function index(): void
    {
        $this->url->redirect(APP_URL);
    }

    /**
     * Clone translation form
     *
     * Show a modal with the clone translation form.
     *
     * @throws \Exception
     */
    public function cloneTranslationForm(): void
    {
        // Load class
        $translationModel = new \Model\Core\Translation();

        /*
         * Set data
         */
        $data['i18n-core'] = $this->i18nCore;
        $data['i18n'] = $this->_i18n;
        $data['translation-data'] = $translationModel->getTranslationData();

        $returnArr = [
            'status' => 'success',
            'message' => '',
            'data' => (new \Lib\Loader())->view('Administration/Translation/ModalCloneTranslation', $data, true)
        ];

        /*
         * Return data
         */
        echo json_encode($returnArr);
        exit();
    }

    /**
     * Clone translation
     *
     * Call clone processor, set message and redirect.
     *
     * @throws \Exception
     */
    public function cloneTranslation(int $translationId = 0): void
    {
        // Check if POST exists
        $this->checkPost($this->_moduleData['manage']->route, [$translationId]);

        // Retrieve POST values
        $callSign = $this->input->post('call-sign') ?? '';

        /*
         * Process translation fields
         */
        $translation = new \Lib\Html\TranslationField();
        $nameArr = $translation->processPost('name', true);

        // Process translation clone
        $status = $this->processTranslationClone($translationId, $callSign, $nameArr);

        /*
         * Set message
         */
        if ($status > 0) {
            $this->session->setMessage('error', $this->_i18n['MsgError'][11]);
        } else {
            $this->session->setMessage('success', $this->_i18n['MsgSuccess'][10]);
        }

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route);
    }

    /**
     * Process translation clone
     *
     * Clone an existing translation, its groups and items.
     *
     * @throws \Exception
     */
    public function processTranslationClone(int $translationId = 0, string $callSign = '', array $nameArr = []): int
    {
        $errorCount = 0;

        // Load class
        $translationModel = new \Model\Core\Translation();

        // Check duplicate translation call sign
        if ($translationModel->isDuplicated($callSign, 'call_sign')) {

            // Set a message
            $msg = $this->_i18n['MsgInformation'][1];
            $this->session->setMessage('information', str_replace('{txt}', $callSign, $msg));

            // Redirect
            $this->goToRoute($this->_moduleData['manage']->route);
        }

        // Create translation record
        $newTranslationId = $translationModel->add($callSign, $nameArr);
        if (!$newTranslationId) $errorCount ++;

        // Get groups
        $groupArr = $translationModel->getTranslationGroups($translationId);

        if (!empty($groupArr)) {

            foreach ($groupArr as $group) {

                // Get group data
                $oldGroupModel = new \Model\Core\TranslationGroup($translationId);
                $oldGroupObj = $oldGroupModel->getGroupData($group->translation_group_id);

                // Process name translations
                $newGroupNames = [];

                foreach ($oldGroupObj->translations as $translation) {
                    $newGroupNames[$translation->language_id] = $translation->value;
                }

                // Create group records
                $newGroupModel = new \Model\Core\TranslationGroup($newTranslationId);
                $newGroupId = $newGroupModel->add($oldGroupObj->call_sign, $newGroupNames);
                if (!$newGroupId) $errorCount ++;

                // Get items
                $itemArr = $oldGroupModel->getGroupItems($group->translation_group_id);

                if (!empty($itemArr)) {

                    foreach ($itemArr as $item) {

                        // Get item data
                        $oldItemModel = new \Model\Core\TranslationItem($translationId, $group->translation_group_id);
                        $oldItemObj = $oldItemModel->getItemData($item->translation_item_id);

                        // Process name translations
                        $newItemNames = [];

                        foreach ($oldItemObj->translations as $translation) {
                            $newItemNames[$translation->language_id] = $translation->value;
                        }

                        // Create item records
                        $newItemModel = new \Model\Core\TranslationItem($newTranslationId, $newGroupId);
                        $newItemModelId = $newItemModel->add($oldItemObj->array_key, $newItemNames);
                        if (!$newItemModelId) $errorCount ++;
                    }
                }
            }
        }

        return $errorCount;
    }

    /**
     * Clone group form
     *
     * Show a modal with the clone group form.
     *
     * @throws \Exception
     */
    public function cloneGroupForm(): void
    {
        // Load class
        $translationModel = new \Model\Core\Translation();

        /*
         * Set data
         */
        $data['i18n-core'] = $this->i18nCore;
        $data['i18n'] = $this->_i18n;
        $data['translations'] = $translationModel->getAllForSelect();

        $returnArr = [
            'status' => 'success',
            'message' => '',
            'data' => (new \Lib\Loader())->view('Administration/Translation/Group/ModalCloneGroup', $data, true)
        ];

        /*
         * Return data
         */
        echo json_encode($returnArr);
        exit();
    }

    /**
     * Clone group
     *
     * Clone an existing group and its items to an existing translation.
     *
     * @throws \Exception
     */
    public function cloneGroup(int $oldTranslationId = 0, int $oldGroupId = 0): void
    {
        // Check if POST exists
        $this->checkPost($this->_moduleData['manage']->route, [$oldTranslationId, $oldGroupId]);

        // Retrieve POST values
        $newTranslationId = $this->input->post('translation-id') ?? '';

        // Process translation clone
        $status = $this->processGroupClone($oldTranslationId, $oldGroupId, $newTranslationId);

        /*
         * Set message
         */
        if ($status) {
            $this->session->setMessage('error', $this->_i18n['MsgError'][12]);

        } else {
            $this->session->setMessage('success', $this->_i18n['MsgSuccess'][11]);
        }

        // Redirect
        $this->goToRoute($this->_moduleData['group']->route, [$newTranslationId]);
    }

    /**
     * Process translation group
     *
     * Clone an existing group and its items to an existing translation.
     *
     * @throws \Exception
     */
    public function processGroupClone(int $oldTranslationId = 0, int $oldGroupId = 0, int $newTranslationId = 0): int
    {
        $errorCount = 0;

        // Get group data
        $oldGroupModel = new \Model\Core\TranslationGroup($oldTranslationId);
        $oldGroupObj = $oldGroupModel->getGroupData($oldGroupId);

        // Check for duplicate group
        $newTranslationModel = new \Model\Core\Translation();
        $newTranslationGroups = $newTranslationModel->getTranslationGroupsCallSigns($newTranslationId);

        if (in_array($oldGroupObj->call_sign, $newTranslationGroups)) {

            // Set a message
            $msg = $this->_i18n['MsgInformation'][1];
            $this->session->setMessage('information', str_replace('{txt}', $oldGroupObj->call_sign, $msg));

            // Redirect
            $this->goToRoute($this->_moduleData['group']->route, [$oldTranslationId]);
        }

        // Process name translations
        $newGroupNames = [];

        foreach ($oldGroupObj->translations as $translation) {
            $newGroupNames[$translation->language_id] = $translation->value;
        }

        // Create group record
        $newGroupModel = new \Model\Core\TranslationGroup($newTranslationId);
        $newGroupId = $newGroupModel->add($oldGroupObj->call_sign, $newGroupNames);
        if (!$newGroupId) $errorCount ++;

        // Get items
        $itemArr = $oldGroupModel->getGroupItems($oldGroupObj->translation_group_id);

        if (!empty($itemArr)) {

            foreach ($itemArr as $item) {

                // Get item data
                $oldItemModel = new \Model\Core\TranslationItem($oldTranslationId, $oldGroupObj->translation_group_id);
                $oldItemObj = $oldItemModel->getItemData($item->translation_item_id);

                // Process name translations
                $newItemNames = [];

                foreach ($oldItemObj->translations as $translation) {
                    $newItemNames[$translation->language_id] = $translation->value;
                }

                // Create item records
                $newItemModel = new \Model\Core\TranslationItem($newTranslationId, $newGroupId);
                $newItemModelId = $newItemModel->add($oldItemObj->array_key, $newItemNames);
                if (!$newItemModelId) $errorCount ++;
            }
        }

        return $errorCount;
    }

}
