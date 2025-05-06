<?php

namespace Controller\Administration\Translation;

use Lib\App;

/**
 * Add
 *
 * Add a new translation record.
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
        $this->_moduleData = (new \Controller\Administration\Translation\Dashboard())->getModuleData();

        // Load vocabulary
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
        $this->_i18n = (new \Model\Core\I18nFile())->get('Translation');
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
        $translationModel = new \Model\Core\Translation();

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

        // Inner breadcrumbs
        $breadcrumbsController = new \Controller\Administration\Translation\Breadcrumbs();
        $data['inner-breadcrumbs'] = $breadcrumbsController->getBreadcrumbs($this->_activeModule->call_sign);

        // Get data
        $data['translation-data'] = $translationModel->getTranslationData();

        /*
         * Render view
         */
        $template['interface-active'] = 'Administration';
        $template['menu-active'] = 'Administration/Translation';

        $this->template->loadView('Administration/Translation/Formulary', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Register
     *
     * Handles the registration of a new translation.
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
        $translationModel = new \Model\Core\Translation();

        /*
         * Retrieve POST values
         */
        $callSign = $this->input->post('call-sign') ?? '';

        /*
         * Process translation fields
         */
        $translation = new \Lib\Html\TranslationField();
        $valueArr = $translation->processPost('value');

        /*
         * Prevent duplicated using call sign
         */
        if ($translationModel->isDuplicated($callSign, 'call_sign')) {

            // Set message
            $this->session->setMessage('info', str_replace('{txt}', $callSign, $this->_i18n['MsgInformation'][1]));

            // Redirect
            $this->goToRoute($this->_moduleData['add']->route);
        }

        // Add translation record
        $translationId = $translationModel->add($callSign, $valueArr);

        if (!$translationId) {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][1]);

            // Redirect
            $this->goToRoute($this->_moduleData['add']->route);
        }

        // Set message
        $this->session->setMessage('success', $this->_i18n['MsgSuccess'][1]);

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route);
    }

}
