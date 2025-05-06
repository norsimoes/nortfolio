<?php

namespace Controller\Administration\Translation\Group;

use Lib\App;

/**
 * Manage
 *
 * Manage existing translation group records.
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
        $this->_moduleData = (new \Controller\Administration\Translation\Group\Dashboard())->getModuleData();

        // Load vocabulary
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
        $this->_i18n = (new \Model\Core\I18nFile())->get('Translation');
    }

    /**
     * Index
     *
     * Prepares the necessary data to display the management page.
     *
     * @throws \Exception
     */
    public function index(int $translationId = 0): void
    {
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

        /*
         * Set modules URLs
         */
        $data['url-back'] = $this->_moduleData['translation']->url ?? '';
        $data['url-add'] = $this->_moduleData['add']->url . $translationId . '/' ?? '';

        // Set modules ids
        $data['module-id-add'] = $this->_moduleData['add']->id ?? 0;

        // Inner breadcrumbs
        $breadcrumbsController = new \Controller\Administration\Translation\Breadcrumbs();
        $data['inner-breadcrumbs'] = $breadcrumbsController->getBreadcrumbs($this->_activeModule->call_sign, $translationId);

        // Data tables plugin
        $data['url-data-tables-source'] = $this->_activeModule->url . 'dataTables/' . $translationId . '/';

        /*
         * Render view
         */
        $template['interface-active'] = 'Administration';
        $template['menu-active'] = 'Administration/Translation';

        $this->template->loadView('Administration/Translation/Group/Manage', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Data tables
     *
     * Source for the list powered by the dataTables.
     *
     * @throws \Exception
     */
    public function dataTables(int $translationId = 0): void
    {
        /*
         * Order columns
         */
        $columnsArr = [
            0 => 'translation_group_id',
            1 => 'call_sign',
            2 => 'value',
            3 => 'item_count'
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
            $groupModel = new \Model\Core\TranslationGroup($translationId);

            // Initialize array
            $data = [];

            /*
             * Set data
             */
            $data['i18n-core'] = $this->i18nCore;
            $data['i18n'] = $this->_i18n;
            $data['active-module'] = $this->_activeModule;

            /*
             * Set modules URLs
             */
            $data['url-edit'] = $this->_moduleData['edit']->url . $translationId . '/' ?? '';
            $data['url-delete'] = $this->_moduleData['delete']->url . $translationId . '/' ?? '';
            $data['url-get-item'] = $this->_moduleData['item']->url . $translationId . '/' ?? '';
            $data['url-clone-form'] = $this->_moduleData['copy']->url . 'cloneGroupForm/';
            $data['url-clone'] = $this->_moduleData['copy']->url . 'cloneGroup/';

            /*
             * Set modules ids
             */
            $data['module-id-edit'] = $this->_moduleData['edit']->id ?? 0;
            $data['module-id-delete'] = $this->_moduleData['delete']->id ?? 0;
            $data['module-id-clone'] = $this->_moduleData['copy']->id ?? 0;

            /*
             * Data Tables total records
             */
            $totalRecords = $groupModel->getAll(0, 0, '', $multiSort);
            $data['DT_recordsTotal'] = count($totalRecords);

            /*
             * Data Tables total records after filters applied
             */
            $recordsAfterFilter = $groupModel->getAll(0, 0, $search, $multiSort, $mySqlFilter);
            $data['DT_recordsFiltered'] = count($recordsAfterFilter);

            /*
             * Data Tables records after filters and pagination
             */
            $records = $groupModel->getAll($start, $length, $search, $multiSort, $mySqlFilter);
            $data['DT_draw'] = intval($this->input->get('draw'));

            $data['records'] = $this->_prepareData($records);

            /*
             * Render the view
             */
            $this->loader->view('Administration/Translation/Group/ManageDT', $data);
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
        $rowsArr = [];

        if ($rows) {

            foreach ($rows as $row) {

                $rowsArr[] = (object) [
                    'translation_id' => $row->translation_id,
                    'translation_group_id' => $row->translation_group_id,
                    'call_sign' => $row->call_sign,
                    'value' => $row->value,
                    'item_count' => $row->item_count
                ];
            }
        }

        return $rowsArr;
    }

}
