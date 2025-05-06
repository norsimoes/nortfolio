<?php

namespace Controller\Vitae\Education;

use Lib\App;

/**
 * Delete
 *
 * Delete an existing education record.
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
        $this->_moduleData = (new \Controller\Vitae\Education\Dashboard())->getModuleData();

        // Load vocabulary
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
        $this->_i18n = (new \Model\Core\I18nFile())->get('Education');
    }

    /**
     * Index
     *
     * Deletes a row from the database.
     *
     * @throws \Exception
     */
    public function index(int $educationId = 0): void
    {
        /*
         * Load classes
         */
        $educationModel = new \Model\Vitae\Education();

        /*
         * Check if row exists
         */
        $delete = $educationModel->getById($educationId);

        if (!$delete) {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][4]);

            // Redirect
            $this->goToRoute($this->_moduleData['manage']->route);
        }

        // Delete records
        $operationStatus = $educationModel->del($educationId);

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
