<?php

namespace Controller\Administration;

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
        'Administration/User' => 'Administration/User',
        'Administration/Role' => 'Administration/Role',
        'Administration/Module' => 'Administration/Module',
        'Administration/Translation' => 'Administration/Translation',
        'Administration/Language' => 'Administration/Language',
        'Administration/Blank' => 'Administration/Blank',
        'Vitae/Experience' => 'Vitae/Experience',
        'Vitae/Education' => 'Vitae/Education',
        'Vitae/Skill' => 'Vitae/Skill',
        'Vitae/Profile' => 'Vitae/Profile',
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
        $data['module-data'] = $this->getModules($this->_moduleList);

        /*
         * Render view
         */
        $template['interface-active'] = 'Administration';
        $template['menu-active'] = 'Administration/Dashboard';

        $this->template->loadView('Administration/Dashboard', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Get modules
     *
     * Prepares the dashboard modules grid.
     *
     * @throws \Exception
     */
    public function getModules(array $moduleList = []): array
    {
        $returnArr = [];

        // Load classes
        $moduleModel = new \Model\Core\Module();

        foreach ($moduleList as $infoRoute => $targetRoute) {

            /*
             * Get modules data
             */
            $infoObj = $moduleModel->getByRoute($infoRoute);
            $targetObj = $moduleModel->getByRoute($targetRoute);

            if (!$infoObj || !$targetObj) continue;

            // Check module permissions
            if (!\Lib\Access::getInstance()->module($infoObj->module_id)) continue;

            // Record count
            $recordCount = property_exists($infoObj, 'count') ? $infoObj->count : '';

            /*
             * Return data
             */
            $returnArr[] = (object) [
                'module_id' => $infoObj->module_id,
                'name' => $infoObj->name,
                'desc' => $infoObj->desc,
                'icon' => $infoObj->icon,
                'route' => $infoObj->route,
                'target' => APP_URL . $targetObj->url,
                'count' => $recordCount,
            ];
        }

        return $returnArr;
    }

}
