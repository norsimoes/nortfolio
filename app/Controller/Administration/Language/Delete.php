<?php

namespace Controller\Administration\Language;

use Lib\App;

/**
 * Delete
 *
 * Delete existing language records.
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
        $this->_moduleData = (new \Controller\Administration\Language\Dashboard())->getModuleData();

        // Load vocabulary
        $this->_i18n = (new \Model\Core\I18nFile())->get('Language');
    }

    /**
     * Index
     *
     * Deletes an existing language record.
     *
     * @throws \Exception
     */
    public function index(int $languageId = 0): void
    {
        // Load model class
        $languageModel = new \Model\Core\Language();

        /*
         * Check if row exists
         */
        $deleteObj = $languageModel->getById($languageId);

        if (!$deleteObj) {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][4]);

            // Redirect
            $this->goToRoute($this->_moduleData['manage']->route);
        }

        $errors = 0;

        // Delete language record
        if (!$languageModel->del($languageId)) $errors ++;

        /*
         * Set message
         */
        if ($errors) {

            $this->session->setMessage('error', $this->_i18n['MsgError'][3]);

        } else {

            $this->session->setMessage('success', $this->_i18n['MsgSuccess'][3]);
        }

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route);
    }

}
