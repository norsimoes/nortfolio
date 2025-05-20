<?php

namespace Controller\Administration\User;

use Lib\App;

/**
 * Delete
 *
 * Delete existing user records.
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
        $this->_moduleData = (new \Controller\Administration\User\Dashboard())->getModuleData();

        // Load vocabulary
        $this->_i18n = (new \Model\Core\I18nFile())->get('User');
    }

    /**
     * Index
     *
     * Deletes an existing user record.
     *
     * @throws \Exception
     */
    public function index(int $userId = 0, int $parentModuleId = 0): void
    {
        // Load model class
        $userModel = new \Model\Entity\User();
        $authenticationModel = new \Model\Entity\Authentication();

        /*
         * Check if row exists
         */
        $deleteObj = $userModel->getById($userId);

        if (!$deleteObj) {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][4]);

            // Redirect
            $this->goToRoute($this->_moduleData['manage']->route);
        }

        $errors = 0;

        // Delete user record
        if (!$userModel->del($userId)) $errors ++;

        // Delete authentication record
        if (!$authenticationModel->del($userId)) $errors ++;

        // Delete permission records
        $userModel->flushPermission($userId);

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
