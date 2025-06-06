<?php

namespace Controller\Administration\Language;

use Lib\App;

/**
 * Add
 *
 * Add a new language record.
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
        $this->_moduleData = (new \Controller\Administration\Language\Dashboard())->getModuleData();

        /*
         * Load vocabulary
         */
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
        $this->_i18n = (new \Model\Core\I18nFile())->get('Language');
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
        $data['role-id'] = $this->session->get('user')->role_id;

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

        // Get data object
        $data['language-data'] = $languageModel->getDataObject();

        /*
         * Render view
         */
        $template['interface-active'] = 'Administration';
        $template['menu-active'] = 'Administration/Language';

        $this->template->loadView('Administration/Language/Formulary', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Register
     *
     * Handles the registration of a new language.
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
        $languageModel = new \Model\Core\Language();

        /*
         * Retrieve POST values
         */
        $referenceName = $this->input->post('reference-name') ?: '';
        $localName = $this->input->post('local-name') ?: '';
        $iso2 = $this->input->post('iso2') ?: '';
        $iso3 = $this->input->post('iso3') ?: '';
        $isActive = $this->input->post('is-active') ?: 0;

        /*
         * Prevent duplicated using iso3
         */
        if ($languageModel->isDuplicated($iso3, 'iso3')) {

            // Set message
            $this->session->setMessage('info', str_replace('{txt}', $iso3, $this->_i18n['MsgInformation'][1]));

            // Redirect
            $this->goToRoute($this->_moduleData['add']->route);
        }

        // Add language record
        $languageId = $languageModel->add($referenceName, $localName, $iso2, $iso3, $isActive);

        if ($languageId) {

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
