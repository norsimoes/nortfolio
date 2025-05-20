<?php

namespace Controller\Administration\Module;

use Lib\App;

/**
 * Delete
 *
 * Delete existing module records.
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
        $this->_moduleData = (new \Controller\Administration\Module\Dashboard())->getModuleData();

        // Load vocabulary
        $this->_i18n = (new \Model\Core\I18nFile())->get('Module');
    }

    /**
     * Index
     *
     * Recursively deletes modules from the database.
     *
     * @throws \Exception
     */
    public function index(string $moduleId = '', int $parentModuleId = 0): void
    {
        // Load model class
        $moduleModel = new \Model\Core\Module();

        /*
         * Check if row exists
         */
        $deleteObj = $moduleModel->getById($moduleId);

        if (!$deleteObj) {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][4]);

            // Redirect
            $this->goToRoute($this->_moduleData['manage']->route);
        }

        $errors = 0;

        /*
         * Delete module and all child
         */
        $childArr = $moduleModel->getAllRecursive($moduleId);
        $deleteArr = $moduleModel->flattenArray($childArr) ?: [];
        $deleteArr[] = $moduleId;

        foreach ($deleteArr as $deleteId) {

            if (!$moduleModel->delModule($deleteId)) $errors ++;
            if (!$moduleModel->delRoute($deleteId)) $errors ++;
        }

        /*
         * Set message
         */
        if ($errors) {

            $this->session->setMessage('error', $this->_i18n['MsgError'][3]);

        } else {

            $this->session->setMessage('success', $this->_i18n['MsgSuccess'][3]);
        }

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route, [$parentModuleId]);
    }

}
