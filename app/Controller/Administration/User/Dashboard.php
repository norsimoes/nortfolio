<?php

namespace Controller\Administration\User;

use Lib\App;

/**
 * Dashboard
 *
 * Allows access to all submodules.
 */
class Dashboard extends App
{
    /**
     * Modules available in the dashboard grid.
     */
    protected array $_moduleList = [
        'Administration/User' => 'Administration/User/Manage'
    ];

    /**
     * Class constructor
     *
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();

        // Check login
        $this->requiresAuthentication();
    }

    /**
     * Index
     *
     * Prepares the necessary data to display the dashboard.
     *
     * @throws \Exception
     */
    public function index(): void
    {
        /*
         * Initialize arrays
         */
        $data = [];
        $template = [];

        // Prepare dashboard
        $data['module-data'] = (new \Controller\Administration\Dashboard())->getModules($this->_moduleList);

        // If there is only one submodule, redirect to it
        if (count($data['module-data']) == 1) $this->url->redirect($data['module-data'][0]->target);

        /*
         * Render view
         */
        $template['interface-active'] = 'Administration';
        $template['menu-active'] = 'Administration/User';

        $this->template->loadView('Administration/Dashboard', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Get module data
     *
     * Retrieve the auxiliary modules data.
     */
    public function getModuleData(): array
    {
        $return = [];

        $moduleModel = new \Model\Core\Module();

        $routeArr = [
            'interface' => 'Administration',
            'module' => 'Administration/User',
            'manage' => 'Administration/User/Manage',
            'add' => 'Administration/User/Add',
            'edit' => 'Administration/User/Edit',
            'delete' => 'Administration/User/Delete',
            'permission' => 'Administration/User/Permission',
        ];

        foreach ($routeArr as $name => $route) {

            $module = $moduleModel->getByRoute($route);

            if (!$module) continue;

            $return[$name] = (object) [
                'id' => $module->module_id,
                'route' => $module->route,
                'url' => APP_URL . $module->url,
            ];
        }

        return $return;
    }

}
