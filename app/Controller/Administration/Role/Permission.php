<?php

namespace Controller\Administration\Role;

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
        $this->_moduleData = (new \Controller\Administration\Role\Dashboard())->getModuleData();

        // Load vocabulary
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
        $this->_i18n = (new \Model\Core\I18nFile())->get('Role');
    }

    /**
     * Index
     *
     * Prepares the necessary data to display the permissions page.
     *
     * @throws \Exception
     */
    public function index(int $roleId = 0): void
    {
        /*
         * Load classes
         */
        $moduleModel = new \Model\Core\Module();
        $roleModel = new \Model\Core\Role();
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
        $data['url-formulary-action'] = $this->_activeModule->url . 'register/' . $roleId . '/';
        $data['url-submit-label'] = $this->i18nCore['Common'][2];
        $data['url-cancel-label'] = $this->i18nCore['Common'][3];

        // Retrieve data from database to populate the formulary
        if ($roleId) {

            $data['role-data'] = $roleModel->getById($roleId);
            $data['default-action'] = $controllerActionsModel->getDefaultAction();
            $data['controller-actions'] = $controllerActionsModel->getAllForSelect();
            $data['role-permissions'] = $roleModel->getRolePermission($roleId);
            $data['interfaces'] = $moduleModel->getAllByParentId(0, 0, '', '');

            foreach ($data['interfaces'] as $interface) {
                $data['modules'][$interface->module_id] = $roleModel->getModules($roleId, $interface->module_id);
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
        $template['menu-active'] = 'Administration/Role';

        $this->template->loadView('Administration/Role/Permission', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Register
     *
     * Handles the registration of a role permissions.
     *
     * @param integer $roleId
     * @throws \Exception
     * @return void
     */
    public function register(int $roleId = 0): void
    {
        // Load classes
        $roleModel = new \Model\Core\Role();

        $errors = 0;
        $rolePermissionArr = [];

        // Delete old values
        $roleModel->flushPermission($roleId);

        // Retrieve permission values
        foreach ($_POST as $moduleId => $value) {

            // Handle permission
            if (!$roleModel->addPermission($roleId, $moduleId)) $errors ++;

            // Add to role permission array
            $rolePermissionArr[] = $moduleId;
        }

        // Adjust user permissions
        if (!$this->_adjustUserPermission($roleId, $rolePermissionArr)) $errors ++;

        /*
         * Set message
         */
        if ($errors) {
            $this->session->setMessage('error', $this->_i18n['MsgError'][5]);
        } else {
            $this->session->setMessage('success', $this->_i18n['MsgSuccess'][4]);
        }

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route);
    }

    /**
     * Register
     *
     * Handles the registration of an existing module.
     *
     * @throws \Exception
     */
    public function _adjustUserPermission(int $roleId = 0, array $rolePermissionArr = []): bool
    {
        // Load model class
        $userModel = new \Model\Entity\User();

        $errors = 0;

        // Get role users
        $userArr = $userModel->getByRoleId($roleId);

        if ($userArr) {

            foreach ($userArr as $user) {

                // Get user permissions
                $userPermissionArr = $userModel->getPermission($user->user_id);

                if ($userPermissionArr) {

                    foreach ($userPermissionArr as $userPermission) {

                        if (!in_array($userPermission->module_id, $rolePermissionArr)) {

                            // Delete user permission
                            if (!$userModel->delPermission($user->user_id, $userPermission->module_id)) $errors ++;
                        }
                    }
                }
            }
        }

        return !$errors;
    }

}
