<?php

namespace Controller\Administration;

use Lib\App;

/**
 * Menu
 *
 * Handles the Administration interface menu.
 */
class Menu extends App
{
    /**
     * Modules available in the dashboard grid.
     *
     * @var array
     */
    protected array $_moduleList = [
        'Administration' => [
            'Administration/Dashboard' => 'Administration',
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
        ],
    ];

    /**
     * Class Constructor
     *
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
	}

    /**
     * Load menu
     *
     * Prepare main menu items.
     *
     * @throws \Exception
     */
	public function loadMenu(string $interfaceActive = '', string $menuActive = ''): array
    {
        // Initialize array
        $returnArr = [];

        // Load model class
		$moduleModel = new \Model\Core\Module();

        foreach ($this->_moduleList as $interfaceRoute => $moduleRouteArr) {

            // Get interface data
            $interfaceObj = $moduleModel->getByRoute($interfaceRoute);

            if (!$interfaceObj) continue;

            /*
             * Prepare modules
             */
            $moduleArr = [];

            foreach ($moduleRouteArr as $infoRoute => $targetRoute) {

                /*
                 * Get modules data
                 */
                $infoObj = $moduleModel->getByRoute($infoRoute);
                $targetObj = $moduleModel->getByRoute($targetRoute);

                if (!$infoObj || !$targetObj) continue;

                // Check module permissions
                if (!\Lib\Access::getInstance()->module($infoObj->module_id) && $infoObj->parent_module_id != 0) continue;

                $moduleArr[] = (object) [
                    'name' => $infoObj->name,
                    'call_sign' => $infoObj->call_sign,
                    'active' => $menuActive == $infoObj->route ? 'active' : '',
                    'url' => APP_URL . $targetObj->url,
                    'icon' => $infoObj->icon
                ];
            }

            if (count($moduleArr) < 1) continue;

            /*
             * Prepare interfaces
             */
            $returnArr[] = (object) [
                'name' => $interfaceObj->name,
                'call_sign' => $interfaceObj->call_sign,
                'active' => $interfaceActive == $interfaceObj->call_sign,
                'icon' => $interfaceObj->icon,
                'moduleArr' => $moduleArr
            ];
        }

        return $returnArr;
    }

}
