<?php

namespace Controller\Vitae\Skill;

use Lib\App;

/**
 * Edit
 *
 * Edit an existing skill record.
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
        $this->_moduleData = (new \Controller\Vitae\Skill\Dashboard())->getModuleData();

        // Load vocabulary
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
        $this->_i18n = (new \Model\Core\I18nFile())->get('Skill');
    }

    /**
     * Index
     *
     * Prepares the necessary data to display the formulary in "edit" mode.
     *
     * @throws \Exception
     */
    public function index(int $skillId = 0): void
    {
        /*
         * Load classes
         */
        $skillModel = new \Model\Vitae\Skill();

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
        $data['url-delete-file'] = $this->_moduleData['delete']->url . 'deleteFile/' . $skillId . '/' ?? '';

        /*
         * Set form action and labels
         */
        $data['url-formulary-action'] = $this->_activeModule->url . 'register/' . $skillId . '/';
        $data['url-submit-label'] = $this->i18nCore['Common'][2];
        $data['url-cancel-label'] = $this->i18nCore['Common'][3];

        /*
         * Set lists
         */
        $data['type-list'] = [
            'Programming' => $this->_i18n['Formulary'][9],
            'Design' => $this->_i18n['Formulary'][10],
            'Language' => $this->_i18n['Formulary'][11],
        ];

        // Get data object
        $data['skill-data'] = $skillModel->getDataObject($skillId);

        // Add custom style
        $this->template->addCss('assets/Core/Css/Form.css');

        // Custom scripts
        $this->template->addScript('assets/Core/Js/Form.js?' . time());

        /*
         * Render view
         */
        $template['interface-active'] = 'Vitae';
        $template['menu-active'] = 'Vitae/Skill';

        $this->template->loadView('Vitae/Skill/Formulary', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Register
     *
     * Handles the registration of an existing record.
     *
     * @throws \Exception
     */
    public function register(int $skillId = 0): void
    {
        // Check if POST exists
        $this->checkPost($this->_moduleData['edit']->route, [$skillId]);

        /*
         * Load classes
         */
        $skillModel = new \Model\Vitae\Skill();

        // get skill data
        $skillObj = $skillModel->getById($skillId);

        /*
         * Retrieve POST values
         */
        $type = $this->input->post('type') ?: '';
        $value = $this->input->post('value') ?: 0;

        /*
         * Process translation fields
         */
        $translation = new \Lib\Html\TranslationField();
        $nameArr = $translation->processPost('name', true);
        $overrideArr = $translation->processPost('override', true);

        /*
         * Process icon
         */
        $file = new \Lib\Html\FileImage();
        $icon = $file->processFile('icon', 'skill/', $skillObj->icon);

        // Update skill record
        $operationStatus = $skillModel->edit($skillId, $nameArr, $overrideArr, $type, $value, $icon);

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
