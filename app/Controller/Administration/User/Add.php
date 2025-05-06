<?php

namespace Controller\Administration\User;

use Lib\App;

/**
 * Add
 *
 * Add a new user record.
 */
class Add extends App
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

        /*
         * Load vocabulary
         */
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
        $this->_i18n = (new \Model\Core\I18nFile())->get('User');
    }

    /**
     * Index
     *
     * Prepares the necessary data to display the formulary in "add" mode.
     *
     * @throws \Exception
     */
    public function index(): void
    {
        /*
         * Load classes
         */
        $userModel = new \Model\Entity\User();
        $roleModel = new \Model\Core\Role();
        $languageModel = new \Model\Core\Language();

        /*
         * Initialize arrays
         */
        $data = [];
        $template = [];

        /*
         * Set data
         */
        $data['i18n-core'] = $this->i18nCore;
        $data['i18n'] = $this->_i18n;
        $data['active-module'] = $this->_activeModule;
        $data['password-required'] = 'required';

        /*
         * Set modules URLs
         */
        $data['url-back'] = $this->_moduleData['manage']->url ?? '';

        /*
         * Set form action and labels
         */
        $data['url-formulary-action'] = $this->_activeModule->url . 'register/';
        $data['url-submit-label'] = $this->i18nCore['Common'][1];
        $data['url-cancel-label'] = $this->i18nCore['Common'][3];

        /*
         * Set lists
         */
        $data['role-list'] = $roleModel->getAllForSelect();
        $data['language-list'] = $languageModel->getAllForSelect();

        // Get data object
        $data['user-data'] = $userModel->getDataObject();

        /*
         * Render view
         */
        $template['interface-active'] = 'Administration';
        $template['menu-active'] = 'Administration/User';

        $this->template->loadView('Administration/User/Formulary', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Register
     *
     * Handles the registration of a new user.
     *
     * @throws \Exception
     */
    public function register(): void
    {
        // Check if POST exists
        $this->checkPost($this->_moduleData['add']->route);

        /*
         * Load classes
         */
        $userModel = new \Model\Entity\User();
        $authenticationModel = new \Model\Entity\Authentication();

        /*
         * Retrieve POST values
         */
        $name = $this->input->post('name') ?? '';
        $email = $this->input->post('email') ?? '';
        $phone = $this->input->post('phone') ?? '';
        $password = $this->input->post('usr-password') ?? '';
        $passwordRepeat = $this->input->post('usr-password-repeat') ?? '';
        $roleId = (int) $this->input->post('role-id') ?: 0;
        $languageId = (int) $this->input->post('language-id') ?: 0;
        $status = (int) $this->input->post('status') ?: 0;

        /*
         * Prevent duplicated using email
         */
        if ($userModel->isDuplicated($email, 'email')) {

            // Set message
            $this->session->setMessage('info', str_replace('{txt}', $email, $this->_i18n['MsgInformation'][1]));

            // Redirect
            $this->goToRoute($this->_moduleData['add']->route);
        }

        /*
         * Check if the two passwords match
         */
        if ($password != $passwordRepeat) {

            // Set an error message
            $this->session->setMessage('error', $this->_i18n['MsgError'][5]);

            // Redirect
            $this->goToRoute($this->_moduleData['add']->route);
        }

        // Add user record
        $userId = $userModel->add($roleId, $languageId, $name, $email, $phone, $status);

        if ($userId) {

            /*
             * Generate a new password
             */
            $pwdArr = (new \Lib\Helper\Password)->generate($password);
            $pass = $pwdArr['password'];
            $salt = $pwdArr['salt'];

            // Create authentication record
            $authenticationModel->add($userId, $email, $pass, $salt);

            // Set message
            $this->session->setMessage('success', $this->_i18n['MsgSuccess'][1]);

        } else {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][1]);

            // Redirect
            $this->goToRoute($this->_moduleData['add']->route);
        }

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route);
    }

}
