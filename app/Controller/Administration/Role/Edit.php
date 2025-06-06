<?php

namespace Controller\Administration\Role;

use Lib\App;

/**
 * Edit
 *
 * Edit an existing role record.
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
        $this->_moduleData = (new \Controller\Administration\Role\Dashboard())->getModuleData();

        /*
         * Load vocabulary
         */
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
        $this->_i18n = (new \Model\Core\I18nFile())->get('Role');
    }

    /**
     * Index
     *
     * Prepares the necessary data to display the formulary in "edit" mode.
     *
     * @throws \Exception
     */
    public function index(int $roleId = 0): void
    {
        /*
         * Load classes
         */
        $roleModel = new \Model\Core\Role();
        $moduleModel = new \Model\Core\Module();

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
        $data['url-formulary-action'] = $this->_activeModule->url . 'register/' . $roleId . '/';
        $data['url-submit-label'] = $this->i18nCore['Common'][2];
        $data['url-cancel-label'] = $this->i18nCore['Common'][3];

        /*
         * Set lists
         */
        $data['interface-list'] = $moduleModel->getInterfacesForSelect();

        // Get data object
        $data['role-data'] = $roleModel->getDataObject($roleId);

        /*
         * Render view
         */
        $template['interface-active'] = 'Administration';
        $template['menu-active'] = 'Administration/Role';

        $this->template->loadView('Administration/Role/Formulary', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Register
     *
     * Handles the registration of an existing role.
     *
     * @throws \Exception
     */
    public function register(int $roleId = 0): void
    {
        // Check if POST exists
        $this->checkPost($this->_moduleData['edit']->route, [$roleId]);

        /*
         * Load classes
         */
        $roleModel = new \Model\Core\Role();

        /*
         * Retrieve POST values
         */
        $callSign = $this->input->post('call-sign') ?: 0;
        $moduleId = $this->input->post('module-id') ?: 0;

        /*
         * Process translation fields
         */
        $translation = new \Lib\Html\TranslationField();
        $nameArr = $translation->processPost('name');
        $descArr = $translation->processPost('desc');

        /*
         * Prevent duplicated using call sign
         */
        if ($roleModel->isDuplicated($callSign, 'call_sign', $roleId)) {

            // Set message
            $this->session->setMessage('info', str_replace('{txt}', $callSign, $this->_i18n['MsgInformation'][1]));

            // Redirect
            $this->goToRoute($this->_moduleData['edit']->route, [$roleId]);
        }

        // Update role record
        $operationStatus = $roleModel->edit($roleId, $callSign, $nameArr, $descArr, $moduleId);

        if ($operationStatus) {

            // Set message
            $this->session->setMessage('success', $this->_i18n['MsgSuccess'][2]);

        } else {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][2]);

            // Redirect
            $this->goToRoute($this->_moduleData['edit']->route, [$roleId]);
        }

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route);
    }

}
