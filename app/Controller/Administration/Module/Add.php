<?php

namespace Controller\Administration\Module;

use Lib\App;

/**
 * Module add
 *
 * Add a new module record.
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
     * Prepares the necessary data to display the formulary in "add" mode.
     *
     * @throws \Exception
     */
    public function index(int $parentModuleId = 0): void
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
        $data['url-back'] = $this->_moduleData['manage']->url . $parentModuleId . '/';

        /*
         * Set form action and labels
         */
        $data['url-formulary-action'] = $this->_activeModule->url . 'register/';
        $data['url-submit-label'] = $this->i18nCore['Common'][1];
        $data['url-cancel-label'] = $this->i18nCore['Common'][3];

        /*
         * Inner breadcrumbs
         */
        $breadcrumbsController = new \Controller\Administration\Module\Breadcrumbs();
        $data['inner-breadcrumbs'] = $breadcrumbsController->getBreadcrumbs($this->_activeModule, $parentModuleId);

        // Get data object
        $data['module-data'] = $moduleModel->getDataObject();

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
     * Handles the registration of a new module.
     *
     * @throws \Exception
     */
    public function register(): void
    {
        // Check if POST exists
        $this->checkPost($this->_moduleData['add']->route);

        // Load model class
        $moduleModel = new \Model\Core\Module();

        /*
         * Retrieve POST values
         */
        $parentModuleId = $this->input->post('parent-module-id') ?: 0;
        $callSign = $this->input->post('call-sign') ?? '';
        $icon = $this->input->post('icon') ?? '';
        $isActive = $this->input->post('is-active') ?? 0;

        $position = $moduleModel->getPosition($parentModuleId);

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

        if ($moduleModel->isDuplicated($dupRoute, 'route')) {

            // Set message
            $this->session->setMessage('info', str_replace('{txt}', $callSign, $this->_i18n['MsgInformation'][1]));

            // Redirect
            $this->goToRoute($this->_moduleData['add']->route, [$parentModuleId]);
        }

        /*
         * Process module
         */
        $addStatus = $this->processModule($parentModuleId, $callSign, $nameArr, $descArr, $icon, $isActive, $position);

        if (is_string($addStatus)) {

            // Set message
            $this->session->setMessage('error', $addStatus);

            // Redirect
            $this->goToRoute($this->_moduleData['add']->route, [$parentModuleId]);
        }

        // Set message
        $this->session->setMessage('success', $this->_i18n['MsgSuccess'][1]);

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route, [$parentModuleId]);
    }

    /**
     * Process module
     *
     * Processes the registration of a new module.
     *
     * @throws \Exception
     */
    public function processModule(
        int $parentModuleId = 0,
        string $callSign = '',
        array $nameArr = [],
        array $descArr = [],
        string $icon = '',
        int $isActive = 0,
        int $position = 0
    ): int|string {

        // Load classes
        $moduleModel = new \Model\Core\Module();

        /*
         * Create module record
         */
        $moduleId = $moduleModel->addModule($parentModuleId, $callSign, $nameArr, $descArr, $icon, $isActive, $position);

        if (!$moduleId) return $this->_i18n['MsgError'][1];

        foreach ($nameArr as $languageId => $name) {

            /*
             * Prepare route and slug
             */
            $parentData = $moduleModel->getRouteData($parentModuleId, $languageId);

            $parentRoute = $parentData ? $parentData->route . '/' : '';
            $parentSlug = $parentData ? $parentData->slug . '/' : '';

            $route = $parentRoute . $callSign;
            $slug = $parentSlug . (new \Lib\Helper\Text())->slug($name);

            /*
             * Create module route record
             */
            $moduleRouteId = $moduleModel->addRoute($moduleId, $languageId, $route, $slug);

            if (!$moduleRouteId) return $this->_i18n['MsgError'][1];
        }

        return $moduleId;
    }

}
