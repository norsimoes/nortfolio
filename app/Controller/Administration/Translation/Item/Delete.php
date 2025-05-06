<?php

namespace Controller\Administration\Translation\Item;

use Lib\App;

/**
 * Delete
 *
 * Delete an existing translation item record.
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
        $this->_moduleData = (new \Controller\Administration\Translation\Item\Dashboard())->getModuleData();

        // Load vocabulary
        $this->_i18n = (new \Model\Core\I18nFile())->get('Translation');
    }

    /**
     * Index
     *
     * Deletes a translation item record from the database.
     *
     * @throws \Exception
     */
    public function index(int $translationId = 0, int $groupId = 0, int $itemId = 0): void
    {
        /*
         * Load classes
         */
        $itemModel = new \Model\Core\TranslationItem($translationId, $groupId);

        /*
         * Check if row exists
         */
        $deleteObj = $itemModel->getById($itemId);

        if (!$deleteObj) {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][10]);

            // Redirect
            $this->goToRoute($this->_moduleData['manage']->route, [$translationId, $groupId]);
        }

        // Delete translation item record
        $operationStatus = $itemModel->del($itemId);

        if ($operationStatus) {

            // Set message
            $this->session->setMessage('success', $this->_i18n['MsgSuccess'][9]);

        } else {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][9]);
        }

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route, [$translationId, $groupId]);
    }

}
