<?php

namespace Controller\Administration\Translation\Group;

use Lib\App;

/**
 * Add
 *
 * Add a new translation group record.
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
        $this->_moduleData = (new \Controller\Administration\Translation\Group\Dashboard())->getModuleData();

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
    public function index(int $translationId = 0): void
    {
        /*
         * Load classes
         */
        $groupModel = new \Model\Core\TranslationGroup($translationId);

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
        $data['url-back'] = $this->_moduleData['manage']->url . $translationId . '/' ?? '';

        /*
         * Set form action and labels
         */
        $data['url-formulary-action'] = $this->_activeModule->url . 'register/' . $translationId . '/';
        $data['url-submit-label'] = $this->i18nCore['Common'][1];
        $data['url-cancel-label'] = $this->i18nCore['Common'][3];

        // Inner breadcrumbs
        $breadcrumbsController = new \Controller\Administration\Translation\Breadcrumbs();
        $data['inner-breadcrumbs'] = $breadcrumbsController->getBreadcrumbs($this->_activeModule->call_sign, $translationId);

        // Get data
        $data['group-data'] = $groupModel->getGroupData();

        /*
         * Render view
         */
        $template['interface-active'] = 'Administration';
        $template['menu-active'] = 'Administration/Translation';

        $this->template->loadView('Administration/Translation/Group/Formulary', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Register
     *
     * Handles the registration of a new translation group.
     *
     * @throws \Exception
     */
    public function register(int $translationId = 0): void
    {
        // Check if POST exists
        $this->checkPost($this->_moduleData['add']->route);

        /*
         * Load classes
         */
        $groupModel = new \Model\Core\TranslationGroup($translationId);

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
        if ($groupModel->isDuplicated($callSign, 'call_sign', $translationId)) {

            // Set message
            $this->session->setMessage('info', str_replace('{txt}', $callSign, $this->_i18n['MsgInformation'][2]));

            // Redirect
            $this->goToRoute($this->_moduleData['add']->route, [$translationId]);
        }

        // Add translation group record
        $translation = $groupModel->add($callSign, $valueArr);

        if (!$translation) {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][4]);

            // Redirect
            $this->goToRoute($this->_moduleData['add']->route, [$translationId]);
        }

        // Set message
        $this->session->setMessage('success', $this->_i18n['MsgSuccess'][4]);

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route, [$translationId]);
    }

}
