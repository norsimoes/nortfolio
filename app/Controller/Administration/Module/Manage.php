<?php

namespace Controller\Administration\Module;

use Lib\App;

/**
 * Module management
 *
 * Manage existing module records.
 */
class Manage extends App
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

        // Load vocabulary
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
        $this->_i18n = (new \Model\Core\I18nFile())->get('Module');
    }

    /**
     * Index
     *
     * Prepares the necessary data to display the management page.
     *
     * @throws \Exception
     */
    public function index(int $parentModuleId = 0): void
    {
        /*
         * Load classes
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
        $data['grand-parent-module-id'] = $moduleModel->getParentModuleId($parentModuleId);
        $grandParent = ($data['parent-module-id'] == 0 && $data['grand-parent-module-id'] == 0) ? '' : $data['grand-parent-module-id'] . '/';

        $data['total-items'] = $moduleModel->countByParentId($parentModuleId);

        /*
         * Set modules URLs
         */
        $backUrl = $grandParent ? $this->_moduleData['manage']->url . $grandParent : $this->_moduleData['interface']->url;
        $data['url-back'] = $backUrl;
        $data['url-add'] = $this->_moduleData['add']->url ?? '';
        $data['url-sort'] = $this->_moduleData['sort']->url . $parentModuleId . '/' ?? '';
        $data['url-get-actions'] = $this->_moduleData['actions']->url . 'getActions/' ?? '';
        $data['url-post-actions'] = $this->_moduleData['actions']->url . 'createActions/' . $parentModuleId . '/' ?? '';

        /*
         * Set modules ids
         */
        $data['module-id-add'] = $this->_moduleData['add']->id ?? 0;
        $data['module-id-sort'] = $this->_moduleData['sort']->id ?? 0;
        $data['module-id-actions'] = $this->_moduleData['actions']->id ?? 0;

        /*
         * Inner breadcrumbs
         */
        $breadcrumbsController = new \Controller\Administration\Module\Breadcrumbs();
        $data['inner-breadcrumbs'] = $breadcrumbsController->getBreadcrumbs($this->_activeModule, $parentModuleId);

        // Data tables plugin
        $data['url-data-tables-source'] = $this->_activeModule->url . 'dataTables/' . $parentModuleId . '/';

        /*
         * Render view
         */
        $template['interface-active'] = 'Administration';
        $template['menu-active'] = 'Administration/Module';

        $this->template->loadView('Administration/Module/Manage', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Toggle status
     *
     * Toggles the row status for the received id.
     */
    public function toggleStatus(int $id = 0, int $newStatus = 0): void
    {
        $toggler = new \Lib\Html\ToggleSwitch($id, 'Model\Core\Module');

        $toggler->updateStatus($newStatus);
    }

    /**
     * Data tables
     *
     * Source for the list powered by the dataTables.
     *
     * @throws \Exception
     */
    public function dataTables(int $parentModuleId = 0): void
    {
        /*
         * Order columns
         */
        $columnsArr = [
            0 => 'module_id',
            1 => 'icon',
            2 => 'name',
            3 => 'route',
            4 => 'url',
            5 => 'position',
            6 => 'is_active',
        ];

        /*
         * Set defaults
         */
        $start = $this->input->get('start') ?? 0;
        $length = $this->input->get('length') ?? 0;
        $searchArr = $this->input->get('search') ?? '';
        $search = $searchArr['value'] ?? '';
        $orderArr = $this->input->get('order') ?? '';

        /*
         * Multi-Sort
         */
        $multiSort = '';

        if (is_array($orderArr) && count($orderArr)>=1) {
            foreach ($orderArr as $arr) {
                $multiSort .= $columnsArr[$arr['column']] . ' ' . $arr['dir'] . ',';
            }
            $multiSort = rtrim($multiSort, ',');
        }

        /*
         * Dropdown filters
         */
        $dtColumnsArr = $this->input->get('columns');
        $mySqlFilter = [];

        if (is_array($dtColumnsArr) && count($dtColumnsArr)>=1) {
            foreach ($dtColumnsArr as $index => $arr) {
                if (isset($arr['search']) && is_array($arr['search'])) {
                    if (!empty($arr['search']['value'])) {
                        $mySqlFilter[$columnsArr[$index]] = $arr['search']['value'];
                    }
                }
            }
        }

        /*
         * Handle the ajax request for dataTables
         */
        if ($this->input->get('__source')) {

            /*
             * Load classes
             */
            $moduleModel = new \Model\Core\Module();

            // Initialize arrays
            $data = [];

            /*
             * Set data
             */
            $data['i18n-core'] = $this->i18nCore;
            $data['i18n'] = $this->_i18n;
            $data['active-module'] = $this->_activeModule;
            $data['logged-user'] = $this->session->get('user');

            /*
             * Set modules URLs
             */
            $data['url-edit'] = $this->_moduleData['edit']->url ?? '';
            $data['url-delete'] = $this->_moduleData['delete']->url ?? '';
            $data['url-get-child'] = $this->_moduleData['manage']->url ?? '';
            $data['url-status'] = APP_URL . $this->_activeModule->url . 'toggleStatus/';
            $data['url-get-target'] = $this->_moduleData['move']->url . 'getTargetModule/';
            $data['url-move'] = $this->_moduleData['move']->url . 'moveModule/';

            /*
             * Set modules ids
             */
            $data['module-id-edit'] = $this->_moduleData['edit']->id ?? 0;
            $data['module-id-delete'] = $this->_moduleData['delete']->id ?? 0;
            $data['module-id-move'] = $this->_moduleData['move']->id ?? 0;

            /*
             * Data Tables total records
             */
            $totalRecords = $moduleModel->getAllByParentId(0, 0, '', $multiSort, [], $parentModuleId);
            $data['DT_recordsTotal'] = count($totalRecords);

            /*
             * Data Tables total records after filters applied
             */
            $recordsAfterFilter = $moduleModel->getAllByParentId(0, 0, $search, $multiSort, $mySqlFilter, $parentModuleId);
            $data['DT_recordsFiltered'] = count($recordsAfterFilter);

            /*
             * Data Tables records after filters and pagination
             */
            $records = $moduleModel->getAllByParentId($start, $length, $search, $multiSort, $mySqlFilter, $parentModuleId);
            $data['DT_draw'] = intval($this->input->get('draw'));

            $data['records'] = $this->_prepareData($records);

            /*
             * Render the view
             */
            $this->loader->view('Administration/Module/ManageDT', $data);
            exit();
        }
    }

    /**
     * Prepare data
     *
     * Prepare table data for dataTables listing.
     */
    private function _prepareData(array $rows = []): array
    {
        // Initialize array
        $rowsArr = [];

        if ($rows) {

            foreach ($rows as $row) {

                $rowsArr[] = (object) [
                    'module_id' => $row->module_id,
                    'parent_module_id' => $row->parent_module_id,
                    'name' => $row->name,
                    'desc' => $row->desc,
                    'route' => $row->route,
                    'url' => $row->url,
                    'position' => $row->position,
                    'icon' => $row->icon,
                    'child_count' => $row->child_count,
                    'is_active' => $row->is_active,
                ];
            }
        }

        return $rowsArr;
    }

}
