<?php

namespace Controller\Vitae\Profile;

use Lib\App;

/**
 * Manage
 *
 * Manage existing profile records.
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
        $this->_moduleData = (new \Controller\Vitae\Profile\Dashboard())->getModuleData();

        // Load vocabulary
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
        $this->_i18n = (new \Model\Core\I18nFile())->get('Profile');
    }

    /**
     * Index
     *
     * Prepares the necessary data to display the dataTables list.
     *
     * @throws \Exception
     */
    public function index(): void
    {
        /*
         * Load classes
         */
        $profileModel = new \Model\Vitae\Profile();

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
        $data['total-items'] = $profileModel->count();

        /*
         * Set modules URLs
         */
        $data['url-back'] = $this->_moduleData['interface']->url ?? '';
        $data['url-add'] = $this->_moduleData['add']->url ?? '';
        $data['url-sort'] = $this->_moduleData['sort']->url ?? '';

        // Set modules ids
        $data['module-id-add'] = $this->_moduleData['add']->id ?? 0;
        $data['module-id-sort'] = $this->_moduleData['sort']->id ?? 0;

        // Data tables plugin
        $data['url-data-tables-source'] = $this->_activeModule->url . 'dataTables/';

        /*
         * Render view
         */
        $template['interface-active'] = 'Vitae';
        $template['menu-active'] = 'Vitae/Profile';

        $this->template->loadView('Vitae/Profile/Manage', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Data tables
     *
     * Source for the list powered by the dataTables.
     *
     * @throws \Exception
     */
    public function dataTables(): void
    {
        /*
         * Order columns
         */
        $columnsArr = [
            0 => 'profile_id',
            1 => 'name',
            2 => 'type',
            3 => 'position',
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
            $profileModel = new \Model\Vitae\Profile();

            // Initialize array
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

            /*
             * Set modules ids
             */
            $data['module-id-edit'] = $this->_moduleData['edit']->id ?? 0;
            $data['module-id-delete'] = $this->_moduleData['delete']->id ?? 0;

            /*
             * DataTables total records
             */
            $totalRecords = $profileModel->getAll(0, 0, '', $multiSort);
            $data['DT_recordsTotal'] = count($totalRecords);

            /*
             * DataTables total records after filters applied
             */
            $recordsAfterFilter = $profileModel->getAll(0, 0, $search, $multiSort, $mySqlFilter);
            $data['DT_recordsFiltered'] = count($recordsAfterFilter);

            /*
             * DataTables records after filters and pagination
             */
            $records = $profileModel->getAll($start, $length, $search, $multiSort, $mySqlFilter);
            $data['DT_draw'] = intval($this->input->get('draw'));

            $data['records'] = $this->_prepareData($records);

            /*
             * Render the view
             */
            $this->loader->view('Vitae/Profile/ManageDT', $data);
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
                    'profile_id' => $row->profile_id,
                    'name' => $row->name,
                    'type' => $row->type,
                    'url' => $row->url,
                    'icon' => $row->icon,
                    'position' => $row->position,
                ];
            }
        }

        return $rowsArr;
    }

}
