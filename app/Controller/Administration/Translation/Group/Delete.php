<?php

namespace Controller\Administration\Translation\Group;

use Lib\App;

/**
 * Delete
 *
 * Delete an existing translation group record.
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
        $this->_moduleData = (new \Controller\Administration\Translation\Group\Dashboard())->getModuleData();

        // Load vocabulary
        $this->_i18n = (new \Model\Core\I18nFile())->get('Translation');
    }

    /**
     * Index
     *
     * Deletes a translation group and all nested items from the database.
     *
     * @throws \Exception
     */
    public function index(int $translationId = 0, int $groupId = 0): void
    {
        /*
         * Load classes
         */
        $groupModel = new \Model\Core\TranslationGroup($translationId);

        /*
         * Check if row exists
         */
        $deleteObj = $groupModel->getById($groupId);

        if (!$deleteObj) {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][10]);

            // Redirect
            $this->goToRoute($this->_moduleData['manage']->route, [$translationId]);
        }

        $errors = 0;

        /*
         * Delete group items
         */
        $itemModel = new \Model\Core\TranslationItem($translationId, $groupId);

        $groupItemsArr = $groupModel->getGroupItems($groupId);

        if (!empty($groupItemsArr)) {

            foreach ($groupItemsArr as $groupItem) {

                if (!$itemModel->del($groupItem->translation_item_id)) $errors ++;
            }
        }

        // Delete translation group record
        if (!$groupModel->del($groupId)) $errors ++;

        if (!$errors) {

            // Set message
            $this->session->setMessage('success', $this->_i18n['MsgSuccess'][6]);

        } else {

            // Set error message
            $this->session->setMessage('error', $this->_i18n['MsgError'][6]);
        }

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route, [$translationId]);
    }

}
