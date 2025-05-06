<?php

namespace Controller\Administration\User;

use Lib\App;

/**
 * Manage
 *
 * Manage existing user records.
 */
class Manage extends App
{
    protected ?object $_activeModule = null;
    protected array $_moduleData = [];
    protected array $_i18n = [];
    private string $_securityKey = 'user-key';

    /**
     * Class constructor
     *
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();

		// Active module
        $this->_activeModule = $this->module->getActive();

        // Check login
        $this->requiresAuthentication();

        // Check permissions
        $this->checkPermission();

        // Get modules data
        $this->_moduleData = (new \Controller\Administration\User\Dashboard())->getModuleData();

        // Load vocabulary
        $this->_i18n = (new \Model\Core\I18nFile())->get('User');
    }

    /**
     * Index
     *
     * Prepares the necessary data to display the management page.
     *
     * @throws \Exception
     */
    public function index(): void
    {
        // Set the security key
        $this->setSecurityKey($this->_securityKey);

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
        $data['url-back'] = $this->_moduleData['interface']->url ?? '';
        $data['url-add'] = $this->_moduleData['add']->url ?? '';

        // Set modules ids
        $data['module-id-add'] = $this->_moduleData['add']->id ?? 0;

        // Data tables plugin
        $data['url-data-tables-source'] = $this->_activeModule->url . 'dataTables/';

        /*
         * Render view
         */
        $template['interface-active'] = 'Administration';
        $template['menu-active'] = 'Administration/User';

        $this->template->loadView('Administration/User/Manage', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Toggle status
     *
     * Toggles the row status for the received id.
     */
    public function toggleStatus(int $id = 0, int $newStatus = 0): void
    {
        $toggler = new \Lib\Html\ToggleSwitch($id, 'Model\Entity\User');

        $toggler->updateStatus($newStatus);
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
            0 => 'user_id',
            1 => 'name',
            2 => 'email',
            3 => 'phone',
            4 => 'role_name',
            5 => 'status',
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
            $userModel = new \Model\Entity\User();

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
            $data['url-permission'] = $this->_moduleData['permission']->url ?? '';
            $data['url-status'] = APP_URL . $this->_activeModule->url . 'toggleStatus/';

            /*
             * Set modules ids
             */
            $data['module-id-edit'] = $this->_moduleData['edit']->id ?? 0;
            $data['module-id-delete'] = $this->_moduleData['delete']->id ?? 0;
            $data['module-id-permission'] = $this->_moduleData['permission']->id ?? 0;

            /*
             * Data Tables total records
             */
            $totalRecords = $userModel->getAll(0, 0, '', $multiSort);
            $data['DT_recordsTotal'] = count($totalRecords);

            /*
             * Data Tables total records after filters applied
             */
            $recordsAfterFilter = $userModel->getAll(0, 0, $search, $multiSort, $mySqlFilter);
            $data['DT_recordsFiltered'] = count($recordsAfterFilter);

            /*
             * Data Tables records after filters and pagination
             */
            $records = $userModel->getAll($start, $length, $search, $multiSort, $mySqlFilter);
            $data['DT_draw'] = intval($this->input->get('draw'));

            $data['records'] = $this->_prepareData($records);

            /*
             * Render the view
             */
            $this->loader->view('Administration/User/ManageDT', $data);
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
                    'user_id' => $row->user_id,
                    'role_id' => $row->role_id,
                    'role_name' => $row->role_name,
                    'name' => $row->name,
                    'email' => $row->email,
                    'phone' => $row->phone,
                    'status' => $row->status,
                    'avatar' => $row->avatar,
                ];
            }
        }

        return $rowsArr;
    }

}
