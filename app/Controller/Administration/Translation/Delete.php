<?php

namespace Controller\Administration\Translation;

use Lib\App;

/**
 * Delete
 *
 * Delete an existing translation record.
 */
class Delete extends App
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
        $this->_i18n = (new \Model\Core\I18nFile())->get('Translation');
    }

    /**
     * Index
     *
     * Deletes a translation and all nested groups and items from the database.
     *
     * @throws \Exception
     */
    public function index(int $translationId = 0): void
    {
        /*
         * Load classes
         */
        $translationModel = new \Model\Core\Translation();

        /*
         * Check if row exists
         */
        $deleteObj = $translationModel->getById($translationId);

        if (!$deleteObj) {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][10]);

            // Redirect
            $this->goToRoute($this->_moduleData['manage']->route);
        }

        $errors = 0;

        /*
         * Delete groups and items
         */
        $groupModel = new \Model\Core\TranslationGroup($translationId);

        $translationGroupsArr = $translationModel->getTranslationGroups($translationId);

        if (!empty($translationGroupsArr)) {

            foreach ($translationGroupsArr as $translationGroup) {

                $itemModel = new \Model\Core\TranslationItem($translationId, $translationGroup->translation_group_id);

                $groupItemsArr = $groupModel->getGroupItems($translationGroup->translation_group_id);

                if (!empty($groupItemsArr)) {

                    foreach ($groupItemsArr as $groupItem) {

                        if (!$itemModel->del($groupItem->translation_item_id)) $errors ++;
                    }
                }

                if (!$groupModel->del($translationGroup->translation_group_id)) $errors ++;
            }
        }

        // Delete translation record
        if (!$translationModel->del($translationId)) $errors ++;

        if (!$errors) {

            // Set message
            $this->session->setMessage('success', $this->_i18n['MsgSuccess'][3]);

        } else {

            // Set error message
            $this->session->setMessage('error', $this->_i18n['MsgError'][3]);
        }

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route);
    }

}
