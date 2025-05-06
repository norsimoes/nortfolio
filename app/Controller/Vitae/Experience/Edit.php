<?php

namespace Controller\Vitae\Experience;

use Lib\App;

/**
 * Edit
 *
 * Edit an existing experience record.
 */
class Edit extends App
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
     * Prepares the necessary data to display the formulary in "edit" mode.
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
        $data['url-formulary-action'] = $this->_activeModule->url . 'register/' . $experienceId . '/';
        $data['url-submit-label'] = $this->i18nCore['Common'][2];
        $data['url-cancel-label'] = $this->i18nCore['Common'][3];

        // Get data object
        $data['experience-data'] = $experienceModel->getDataObject($experienceId);

        /*
         * Render view
         */
        $template['interface-active'] = 'Vitae';
        $template['menu-active'] = 'Vitae/Experience';

        $this->template->loadView('Vitae/Experience/Formulary', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Register
     *
     * Handles the registration of an existing record.
     *
     * @throws \Exception
     */
    public function register(int $experienceId = 0): void
    {
        // Check if POST exists
        $this->checkPost($this->_moduleData['edit']->route, [$experienceId]);

        /*
         * Load classes
         */
        $experienceModel = new \Model\Vitae\Experience();

        /*
         * Retrieve POST values
         */
        $tech = $this->input->post('tech') ?: '';

        /*
         * Process translation fields
         */
        $translation = new \Lib\Html\TranslationField();
        $nameArr = $translation->processPost('name');
        $startArr = $translation->processPost('start', true);
        $endArr = $translation->processPost('end', true);
        $companyArr = $translation->processPost('company', true);
        $locationArr = $translation->processPost('location', true);
        $descArr = $translation->processPost('desc');

        // Update experience record
        $operationStatus = $experienceModel->edit($experienceId, $nameArr, $startArr, $endArr, $companyArr, $locationArr, $descArr, $tech);

        if ($operationStatus) {

            // Set message
            $this->session->setMessage('success', $this->_i18n['MsgSuccess'][2]);

        } else {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][2]);

            // Redirect
            $this->goToRoute($this->_moduleData['add']->route);
        }

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route);
    }

}
