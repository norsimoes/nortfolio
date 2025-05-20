<?php

namespace Controller\Administration\User;

use Lib\App;

/**
 * Edit
 *
 * Edit an existing user record.
 */
class Edit extends App
{
    protected ?object $_activeModule = null;
    protected array $_moduleData = [];
    protected array $_i18n = [];
    private string $_securityKey = 'user-key';

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
     * Prepares the necessary data to display the formulary in "edit" mode.
     *
     * @throws \Exception
     */
    public function index(int $userId = 0): void
    {
        // Set the security key
        $this->setSecurityKey($this->_securityKey);

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
        $data['password-required'] = '';

        /*
         * Set modules URLs
         */
        $data['url-back'] = $this->_moduleData['manage']->url ?? '';

        /*
         * Set form action and labels
         */
        $data['url-formulary-action'] = $this->_activeModule->url . 'register/' . $userId . '/';
        $data['url-submit-label'] = $this->i18nCore['Common'][2];
        $data['url-cancel-label'] = $this->i18nCore['Common'][3];

        /*
         * Set lists
         */
        $data['role-list'] = $roleModel->getAllForSelect();
        $data['language-list'] = $languageModel->getAllForSelect();

        // Get data object
        $data['user-data'] = $userModel->getDataObject($userId);

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
     * Handles the registration of an existing user.
     *
     * @throws \Exception
     */
    public function register(int $userId = 0): void
    {
        // Check session security token
        $this->checkSecurityKey($this->_securityKey, $this->_moduleData['edit']->route, [$userId]);

        // Check if POST exists
        $this->checkPost($this->_moduleData['edit']->route, [$userId]);

        /*
         * Load classes
         */
        $userModel = new \Model\Entity\User();
        $authenticationModel = new \Model\Entity\Authentication();
        $languageModel = new \Model\Core\Language();

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
        if ($userModel->isDuplicated($email, 'email', $userId)) {

            // Set message
            $this->session->setMessage('info', str_replace('{txt}', $email, $this->_i18n['MsgInformation'][1]));

            // Redirect
            $this->goToRoute($this->_moduleData['edit']->route, [$userId]);
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

        // Update user record
        $operationStatus = $userModel->edit($userId, $roleId, $languageId, $name, $email, $phone, $status);

        if ($operationStatus) {

            // Update authentication record
            $authenticationModel->editAuth($userId, $email);

            if ($password && $passwordRepeat) {

                /*
                 * Generate a new password
                 */
                $pwdArr = (new \Lib\Helper\Password)->generate($password);
                $pass = $pwdArr['password'];
                $salt = $pwdArr['salt'];

                // Update authentication record
                $authenticationModel->editPass($userId, $pass, $salt);
            }

            /*
             * Update session
             */
            if ($this->session->get('user')->user_id == $userId) {

                $userData = (object) [
                    "user_id" => $userId,
                    "role_id" => $roleId,
                    "language_id" => $languageId,
                    "name" => $name,
                    "email" => $email,
                    "phone" => $phone,
                    "avatar" => '',
                    "status" => $status
                ];

                // Set user session data
                $this->session->set('user', $userData);

                // Get language data
                $languageData = $languageModel->getDataObject($languageId);

                // Set i18n session data
                $this->session->setI18n($languageData->iso2, $languageData->iso3, $languageId);
            }

            // Set message
            $this->session->setMessage('success', $this->_i18n['MsgSuccess'][2]);

        } else {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][2]);

            // Redirect
            $this->goToRoute($this->_moduleData['edit']->route, [$userId]);
        }

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route);
    }

}
