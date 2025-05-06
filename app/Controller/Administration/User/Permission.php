<?php

namespace Controller\Administration\User;

use Lib\App;

/**
 * Permission
 *
 * Edit a role permissions.
 */
class Permission extends App
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
        $this->_moduleData = (new \Controller\Administration\User\Dashboard())->getModuleData();

        // Load vocabulary
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
        $this->_i18n = (new \Model\Core\I18nFile())->get('User');
    }

    /**
     * Index
     *
     * Prepares the necessary data to display the permissions page.
     *
     * @throws \Exception
     */
    public function index(int $userId = 0): void
    {
        /*
         * Load classes
         */
        $moduleModel = new \Model\Core\Module();
        $roleModel = new \Model\Core\Role();
        $userModel = new \Model\Entity\User();
        $controllerActionsModel = new \Model\Core\ControllerAction;

        /*
         * Set data
         */
        $data['i18n-core'] = $this->i18nCore;
        $data['i18n'] = $this->_i18n;
        $data['active-module'] = $this->_activeModule;

        /*
         * Set modules URLs
         */
        $data['url-back'] = $this->_moduleData['manage']->url ?? '';
        $data['url-add'] = $this->_moduleData['add']->url ?? '';

        /*
         * Set form action and labels
         */
        $data['url-formulary-action'] = $this->_activeModule->url . 'register/' . $userId . '/';
        $data['url-submit-label'] = $this->i18nCore['Common'][2];
        $data['url-cancel-label'] = $this->i18nCore['Common'][3];

        // Retrieve data from database to populate the formulary
        if ($userId) {

            $data['user-data'] = $userModel->getById($userId);
            $data['default-action'] = $controllerActionsModel->getDefaultAction();
            $data['controller-actions'] = $controllerActionsModel->getAllForSelect();
            $data['user-permissions'] = $userModel->getUserPermission($userId);
            $data['role-permissions'] = $roleModel->getRolePermission($data['user-data']->role_id);
            $data['interfaces'] = $moduleModel->getAllByParentId(0, 0, '', '');

            foreach ($data['interfaces'] as $interface) {
                $data['modules'][$interface->module_id] = $roleModel->getModules($userId, $interface->module_id);
            }
        }

        // Add custom style
        $this->template->addCss('assets/Administration/Css/Role/Permissions.css');

        // Custom scripts
        $this->template->addScript('assets/Administration/Js/Role/Permissions.js?' . time());

        /*
         * Render view
         */
        $template['interface-active'] = 'Administration';
        $template['menu-active'] = 'Administration/User';

        $this->template->loadView('Administration/User/Permission', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Register
     *
     * Handles the registration of a user permissions.
     *
     * @throws \Exception
     */
    public function register(int $userId = 0): void
    {
        // Load classes
        $userModel = new \Model\Entity\User();

        $errors = 0;

        // Delete old values
        $userModel->flushPermission($userId);

        // Retrieve permission values
        foreach ($_POST as $moduleId => $value) {

            // Handle permission
            if (!$userModel->addPermission($userId, $moduleId)) $errors ++;
        }

        /*
         * Set message
         */
        if ($errors) {
            $this->session->setMessage('error', $this->_i18n['MsgError'][6]);
        } else {
            $this->session->setMessage('success', $this->_i18n['MsgSuccess'][4]);
        }

        // Redirect
        $this->goToRoute($this->_moduleData['permission']->route, [$userId]);
    }

}
