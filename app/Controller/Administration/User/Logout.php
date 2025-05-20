<?php

namespace Controller\Administration\User;

use Lib\App;

/**
 * Logout
 *
 * Processes the user logout.
 */
class Logout extends App
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
     * Handles the user logout.
     *
     * @throws \Exception
     */
    public function index(): void
    {
        // Get logged user
        $loggedUser = $this->session->get('user');

        /*
         * Add log entry
         */
        $authenticationModel = new \Model\Entity\Authentication();
        $authenticationObj = $authenticationModel->getByUsername($loggedUser->email);
        $authenticationModel->log($authenticationObj->user_authentication_id, 'login', 1, 'User successfully logged out.');

        $this->session->destroy();

        $this->url->redirect(APP_URL);
    }

}
