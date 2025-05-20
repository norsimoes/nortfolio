<?php

namespace Controller\Vitae\Experience;

use Lib\App;

/**
 * Delete
 *
 * Delete an existing experience record.
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
        $this->_moduleData = (new \Controller\Vitae\Experience\Dashboard())->getModuleData();

        // Load vocabulary
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
        $this->_i18n = (new \Model\Core\I18nFile())->get('Experience');
    }

    /**
     * Index
     *
     * Deletes a row from the database.
     *
     * @throws \Exception
     */
    public function index(int $experienceId = 0): void
    {
        /*
         * Load classes
         */
        $experienceModel = new \Model\Vitae\Experience();

        /*
         * Check if row exists
         */
        $delete = $experienceModel->getById($experienceId);

        if (!$delete) {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][4]);

            // Redirect
            $this->goToRoute($this->_moduleData['manage']->route);
        }

        // Delete records
        $operationStatus = $experienceModel->del($experienceId);

        if ($operationStatus) {

            // Set message
            $this->session->setMessage('success', $this->_i18n['MsgSuccess'][3]);

        } else {

            // Set error message
            $this->session->setMessage('error', $this->_i18n['MsgError'][3]);
        }

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route);
    }

}
