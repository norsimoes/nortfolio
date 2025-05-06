<?php

namespace Controller\Administration\Module;

use Lib\App;

/**
 * Breadcrumbs
 *
 * Prepare the translation modules inner breadcrumb navigation.
 */
class Breadcrumbs extends App
{
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

        // Get modules data
        $this->_moduleData = (new \Controller\Administration\Module\Dashboard())->getModuleData();

        // Load vocabulary
        $this->_i18n = (new \Model\Core\I18nFile())->get('Module');
    }

    /**
     * Index
     *
     * Default class method.
     *
     * @throws \Exception
     */
    public function index(): void
    {
        $this->url->redirect(APP_URL);
    }

    /**
     * Get breadcrumbs
     *
     * Prepare the modules inner breadcrumb navigation.
     *
     * @throws \Exception
     */
    public function getBreadcrumbs(?object $activeModule = null, int $parentModuleId = 0): string
    {
        $data = [];

        $manageUrl = $this->_moduleData['manage']->url ?? '';

        $data[] = (object) [
            'name' => $this->_i18n['Manage'][9],
            'call_sign' => 'Module',
            'url' => $parentModuleId == 0 && $activeModule->call_sign == 'Manage' ? '' : $manageUrl
        ];

        $parentsArr = $this->_getParentModuleData($parentModuleId);

        if ($parentsArr) {

            $parentsArr = array_reverse($parentsArr);
            $lastElement = end($parentsArr);

            foreach ($parentsArr as $parent) {

                $data[] = (object) [
                    'name' => $parent->name,
                    'call_sign' => $parent->call_sign,
                    'url' => $parent == $lastElement && $activeModule->call_sign == 'Manage' ? '' : $manageUrl . $parent->module_id . '/'
                ];
            }
        }

        return (new \Lib\Loader())->view('Administration/Module/Breadcrumbs', $data, true);
    }

    /**
     * Get parent module data
     *
     * Get the module parents recursively for the automatic breadcrumbs.
     */
    private function _getParentModuleData(int $moduleId = 0): array
    {
        $returnArr = [];
        $parentsArr = [];

        $moduleModel = new \Model\Core\Module();

        $moduleObj = $moduleModel->getById($moduleId);

        if (!$moduleObj) return $returnArr;

        $returnArr[] = (object) [
            'name' => $moduleObj->name,
            'call_sign' => $moduleObj->call_sign,
            'module_id' => $moduleObj->module_id,
            'url' => $moduleObj->url
        ];

        if ($moduleObj->parent_module_id > 0) $parentsArr = $this->_getParentModuleData($moduleObj->parent_module_id);

        if ($parentsArr) {

            foreach ($parentsArr as $parent) {

                $returnArr[] = (object) [
                    'name' => $parent->name,
                    'call_sign' => $parent->call_sign,
                    'module_id' => $parent->module_id,
                    'url' => $parent->url
                ];
            }
        }

        return $returnArr;
    }

}
