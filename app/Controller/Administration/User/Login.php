<?php

namespace Controller\Administration\User;

use Lib\App;

/**
 * Login
 *
 * Handles the user authentication.
 */
class Login extends App
{
    /**
     * Class constructor
     *
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Index
     *
     * Handles the user authentication.
     *
     * @throws \Exception
     */
    public function index(): never
    {
        // Check if post exists
        $this->checkPost('Website');

        /*
         * Load class
         */
        $authenticationModel = new \Model\Entity\Authentication();
        $userModel = new \Model\Entity\User();
        $languageModel = new \Model\Core\Language();

        /*
         * Retrieve post data
         */
        $username = $this->input->post('login-user');
        $password = $this->input->post('login-pass');

        /*
         * Search for a username with the received value
         */
        $authenticationObj = $authenticationModel->getByUsername($username);
        $authenticationId = 0;

        if (!$authenticationObj) {

            $this->_return(0, 0, 'error', 'Username not found: ' . $username);

        } else {

            $authenticationId = $authenticationObj->user_authentication_id;
        }

        /*
         * Validate the access password
         */
        $validated = (new \Lib\Helper\Password())->validate($password, $authenticationObj->password, $authenticationObj->salt);

        if (!$validated) {

            $this->_return($authenticationId, 0, 'error', 'Wrong password: ' . $password);
        }

        /*
         * Get user details
         */
        $userObj = $userModel->getById($authenticationObj->user_id);

        if (!$userObj) {

            $this->_return($authenticationId, 0, 'error', 'Account not found!');
        }

        /*
         * Check user status
         */
        if ($userObj->status != 1)  {

            $this->_return($authenticationId, 0, 'error', 'Account blocked!');
        }

        /*
         * Prepare session data
         */
        $userData = (object) [
            "user_id" => (int) $userObj->user_id,
            "role_id" => (int) $userObj->role_id,
            "language_id" => (int) $userObj->language_id,
            "name" => $userObj->name,
            "email" => $userObj->email,
            "phone" => $userObj->phone,
            "avatar" => $userObj->avatar,
            "status" => $userObj->status
        ];

        // Set user session data
        $this->session->set('user', $userData);

        // Get language data
        $languageData = $languageModel->getDataObject($userObj->language_id);

        // Set i18n session data
        $this->session->setI18n($languageData->iso2, $languageData->iso3, $languageData->language_id);

        // Return successfully
        $this->_return($authenticationId, 1, 'success', 'User successfully logged in.');
    }

    /**
     * Return
     *
     * Log the login action and return the ajax response.
     *
     * @throws \Exception
     */
    public function _return(int $authenticationId = 0, string $type = '', string $status = '', string $message = ''): never
    {
        /*
         * Add log entry
         */
        $authenticationModel = new \Model\Entity\Authentication();
        $authenticationModel->log($authenticationId, 'login', $type, $message);

        /*
         * Prepare response
         */
        $returnArr = (object) [
            'status' => $status,
            'message' => $message,
        ];

        /*
         * Return data
         */
        echo json_encode($returnArr);
        exit();
    }

}
