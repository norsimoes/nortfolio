<?php

namespace Controller\Administration\Module;

use Lib\App;

/**
 * Module edit
 *
 * Edit an existing module record.
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
        $this->_moduleData = (new \Controller\Administration\Module\Dashboard())->getModuleData();

        /*
         * Load vocabulary
         */
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
        $this->_i18n = (new \Model\Core\I18nFile())->get('Module');
    }

    /**
     * Index
     *
     * Prepares the necessary data to display the formulary in "edit" mode.
     *
     * @throws \Exception
     */
    public function index(int $moduleId = 0, int $parentModuleId = 0): void
    {
        /*
         * Load model class
         */
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

        $data['parent-module-id'] = $parentModuleId;
        $data['role-id'] = $this->session->get('user')->role_id;

        /*
         * Set modules URLs
         */
        $data['url-back'] = $this->_moduleData['manage']->url . $parentModuleId . '/' ?? '';
        $data['url-manage'] = $this->_moduleData['manage']->url ?? '';

        /*
         * Set form action and labels
         */
        $data['url-formulary-action'] = $this->_activeModule->url . 'register/' . $moduleId . '/';
        $data['url-submit-label'] = $this->i18nCore['Common'][2];
        $data['url-cancel-label'] = $this->i18nCore['Common'][3];

        /*
         * Inner breadcrumbs
         */
        $breadcrumbsController = new \Controller\Administration\Module\Breadcrumbs();
        $data['inner-breadcrumbs'] = $breadcrumbsController->getBreadcrumbs($this->_activeModule, $parentModuleId);

        // Get data object
        $data['module-data'] = $moduleModel->getDataObject($moduleId);

        /*
         * Render view
         */
        $template['interface-active'] = 'Administration';
        $template['menu-active'] = 'Administration/Module';

        $this->template->loadView('Administration/Module/Formulary', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Register
     *
     * Handles the registration of an existing module.
     *
     * @throws \Exception
     */
    public function register(int $moduleId = 0): void
    {
        // Check if POST exists
        $this->checkPost($this->_moduleData['edit']->route, [$moduleId]);

        // Load model class
        $moduleModel = new \Model\Core\Module();

        /*
         * Retrieve POST values
         */
        $parentModuleId = $this->input->post('parent-module-id') ?: 0;
        $callSign = $this->input->post('call-sign') ?? '';
        $icon = $this->input->post('icon') ?? '';
        $isActive = (int) $this->input->post('is-active') ?? 0;

        /*
         * Process translation fields
         */
        $translation = new \Lib\Html\TranslationField();
        $nameArr = $translation->processPost('name');
        $descArr = $translation->processPost('desc');

        // Get parent data
        $parentObj = $moduleModel->getById($parentModuleId);

        /*
         * Prevent duplicated using route
         */
        $dupRoute = $parentObj ? $parentObj->route . '/' . $callSign : $callSign;

        if ($moduleModel->isDuplicated($dupRoute, 'route', $moduleId)) {

            // Set message
            $this->session->setMessage('info', str_replace('{txt}', $callSign, $this->_i18n['MsgInformation'][1]));

            // Redirect
            $this->goToRoute($this->_moduleData['edit']->route, [$moduleId, $parentModuleId]);
        }

        /*
         * Update module record
         */
        $operationStatus = $moduleModel->editModule($moduleId, $callSign, $nameArr, $descArr, $icon, $isActive);

        if (!$operationStatus) {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][2]);

            // Redirect
            $this->goToRoute($this->_moduleData['edit']->route, [$moduleId, $parentModuleId]);
        }

        /*
         * Update child routes
         */
        $childOperation = $this->updateChildRoute($moduleId, $parentModuleId, $callSign, $nameArr);

        if (!$childOperation) {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][2]);

            // Redirect
            $this->goToRoute($this->_moduleData['edit']->route, [$moduleId, $parentModuleId]);
        }

        // Set message
        $this->session->setMessage('success', $this->_i18n['MsgSuccess'][2]);

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route, [$parentModuleId]);
    }

    /**
     * Update child route
     *
     * Update the module child routes and urls.
     *
     * @throws \Exception
     */
    public function updateChildRoute(int $moduleId = 0, int $parentModuleId = 0, string $callSign = '', array $nameArr = []): bool
    {
        // Load model class
        $moduleModel = new \Model\Core\Module();

        foreach ($nameArr as $languageId => $name) {

            /*
             * Prepare route and slug
             */
            $parentData = $moduleModel->getRouteData($parentModuleId, $languageId) ?? null;
            $moduleData = $moduleModel->getRouteData($moduleId, $languageId);

            $parentRoute = $parentData ? $parentData->route . '/' : '';
            $parentSlug = $parentData ? $parentData->slug . '/' : '';

            $route = $parentRoute . $callSign;
            $slug = $parentSlug . (new \Lib\Helper\Text())->slug($name);

            // Update routes and slugs
            if (!$moduleModel->editChildRoute($languageId, $moduleData->route, $route, $moduleData->slug, $slug)) return false;
        }

        return true;
    }

}
