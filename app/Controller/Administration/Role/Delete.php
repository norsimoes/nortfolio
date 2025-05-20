<?php

namespace Controller\Administration\Role;

use Lib\App;

/**
 * Delete
 *
 * Delete existing role records.
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
        $this->_moduleData = (new \Controller\Administration\Role\Dashboard())->getModuleData();

        // Load vocabulary
        $this->_i18n = (new \Model\Core\I18nFile())->get('Role');
    }

    /**
     * Index
     *
     * Deletes an existing role record.
     *
     * @throws \Exception
     */
    public function index(int $roleId = 0, int $parentModuleId = 0): void
    {
        // Load model class
        $roleModel = new \Model\Core\Role();

        /*
         * Check if row exists
         */
        $deleteObj = $roleModel->getById($roleId);

        if (!$deleteObj) {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][4]);

            // Redirect
            $this->goToRoute($this->_moduleData['manage']->route);
        }

        $errors = 0;

        // Delete role record
        if (!$roleModel->del($roleId)) $errors ++;

        // Delete permission records
        if (!$roleModel->flushPermission($roleId)) $errors ++;

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
