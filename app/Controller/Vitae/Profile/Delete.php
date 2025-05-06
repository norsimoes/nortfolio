<?php

namespace Controller\Vitae\Profile;

use Lib\App;

/**
 * Delete
 *
 * Delete an existing profile record.
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
        $this->_moduleData = (new \Controller\Vitae\Profile\Dashboard())->getModuleData();

        // Load vocabulary
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
        $this->_i18n = (new \Model\Core\I18nFile())->get('Profile');
    }

    /**
     * Index
     *
     * Deletes a row from the database.
     *
     * @throws \Exception
     */
    public function index(int $profileId = 0): void
    {
        /*
         * Load classes
         */
        $profileModel = new \Model\Vitae\Profile();

        /*
         * Check if row exists
         */
        $delete = $profileModel->getById($profileId);

        if (!$delete) {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][4]);

            // Redirect
            $this->goToRoute($this->_moduleData['manage']->route);
        }

        // Delete records
        $operationStatus = $profileModel->del($profileId);

        if ($operationStatus) {

            // Delete icon
            unlink(APP_BASEPATH_CDN . 'profile/' . $delete->icon);

            // Set message
            $this->session->setMessage('success', $this->_i18n['MsgSuccess'][3]);

        } else {

            // Set error message
            $this->session->setMessage('error', $this->_i18n['MsgError'][3]);
        }

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route);
    }

    /**
     * Delete file
     *
     * Delete an image file from database and cdn folder.
     *
     * @throws \Exception
     */
    public function deleteFile(int $profileId = 0): int
    {
        // Load class
        $profileModel = new \Model\Vitae\Profile();

        // Get profile data
        $profileData = $profileModel->getById($profileId);

        // Update icon column
        $operationStatus = $profileModel->delIcon($profileId);

        if (!$operationStatus) {

            $returnArr['status'] = 'error';
            $returnArr['message'] = $this->_i18n['MsgError'][6];

        } else {

            // Delete file from cdn folder
            unlink(APP_BASEPATH_CDN . 'profile/' . $profileData->icon);

            $returnArr['status'] = 'success';
            $returnArr['message'] = $this->_i18n['MsgSuccess'][5];
        }

        /*
         * Return data
         */
        echo json_encode($returnArr);
        exit();
    }

}
