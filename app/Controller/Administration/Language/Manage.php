<?php

namespace Controller\Administration\Language;

use Lib\App;

/**
 * Manage
 *
 * Manage existing language records.
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

        $this->getDbArr();

        // Get modules data
        $this->_moduleData = (new \Controller\Administration\Language\Dashboard())->getModuleData();

        // Load vocabulary
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
        $this->_i18n = (new \Model\Core\I18nFile())->get('Language');
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
        $template['menu-active'] = 'Administration/Language';

        $this->template->loadView('Administration/Language/Manage', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Toggle available
     *
     * Toggles the available status for the received id.
     *
     * @throws \Exception
     */
    public function toggleAvailable(int $id = 0, int $newStatus = 0): void
    {
        if ($newStatus == 1) $this->updateTranslation($id);

        $toggler = new \Lib\Html\ToggleSwitch($id, 'Model\Core\Language');

        $toggler->updateStatus($newStatus);
    }

    /**
     * Toggle status
     *
     * Toggles the row status for the received id.
     */
    public function toggleStatus(int $id = 0, mixed $newStatus = ''): void
    {
        $toggler = new \Lib\Html\ToggleSwitch($id, 'Model\Core\Language');

        $toggler->updateStatus($newStatus);
    }

    /**
     * Translation info
     *
     * Prepares the translations info to show in the activation modal.
     *
     * @throws \Exception
     */
    public function translationInfo(int $languageId = 0): string
    {
        /*
         * Initialize array
         */
        $returnArr = [
            'status' => 'success',
            'message' => 'Got translation info!',
            'data' => []
        ];

        // Get translation data
        $translationArr = $this->_translationCount($languageId);

        if (!$translationArr) {

            $returnArr['status'] = 'error';
            $returnArr['message'] = $this->_i18n['MsgError'][4];

        } else {

            // Set i18n
            $data['i18n'] = $this->_i18n;

            // Set translation data
            $data['translation-data'] = $translationArr;

            // Load view
            $returnArr['data'] = (new \Lib\Loader())->view('Administration/Language/ActivationModal', $data, true);
        }

        echo json_encode($returnArr);
        exit();
    }

    /**
     * Translation count
     *
     * Count translation records in all databases defined in constant $_dbArr.
     */
    private function _translationCount(int $languageId = APP_I18N_ID): ?object
    {
        // Load model class
        $updateTranslationModel = new \Model\Core\UpdateTranslation();

        /*
         * Get translation records
         */
        $dbArr = $this->getDbArr();

        $translationArr = [];
        $totalNative = 0;
        $totalRegistered = 0;
        $totalNew = 0;

        foreach ($dbArr as $db) {

            $countNative = $updateTranslationModel->countTranslation($db);
            $countRegistered = $updateTranslationModel->countTranslation($db, $languageId);

            $translationArr[] = (object) [
                'db_name' => $db,
                'native' => $countNative,
                'registered' => $countRegistered,
                'new' => $countNative - $countRegistered,
            ];

            $totalNative += $countNative;
            $totalRegistered += $countRegistered;
            $totalNew += $countNative - $countRegistered;
        }

        /*
         * Get module route records
         */
        $countNativeRoute = $updateTranslationModel->countRoute();
        $countRegisteredRoute = $updateTranslationModel->countRoute($languageId);
        $totalNewRoute = $countNativeRoute - $countRegisteredRoute;

        return (object) [
            'db_data' => $translationArr,
            'db_total' => count($translationArr),
            'total_native' => $totalNative,
            'total_registered' => $totalRegistered,
            'total_new' => $totalNew,
            'total_new_route' => $totalNewRoute,
        ];
    }

    /**
     * Update translation
     *
     * Replicates all translation records from the
     * application language id to a target language id.
     *
     * @throws \Exception
     */
    public function updateTranslation(int $targetLanguageId = 0): int
    {
        $originLanguageId = APP_I18N_ID;

        $updateTranslationModel = new \Model\Core\UpdateTranslation();

        $dbArr = $this->getDbArr();

        $errorCount = 0;

        foreach ($dbArr as $db) {

            $translationErrors = $updateTranslationModel->updateTranslation($originLanguageId, $targetLanguageId, $db);

            if ($translationErrors) $errorCount ++;
        }

        $moduleErrors = $updateTranslationModel->updateModuleRoute($originLanguageId, $targetLanguageId);

        if ($moduleErrors) $errorCount ++;

        return $errorCount;
    }

    /**
     * Get database list
     *
     * Retrieve a list of the existing database names.
     */
    public function getDbArr(): array
    {
        $return = [];

        $dbArr = $GLOBALS['db'];

        if ($dbArr) {

            foreach ($dbArr as $db) $return[] = $db['name'];
        }

        return $return;
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
            0 => 'language_id',
            1 => 'reference_name',
            2 => 'local_name',
            3 => 'iso2',
            4 => 'iso3',
            5 => 'is_active'
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
            $languageModel = new \Model\Core\Language();

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
            $data['url-activate'] = APP_URL . $this->_activeModule->url . 'translationInfo/';
            $data['url-available'] = APP_URL . $this->_activeModule->url . 'toggleAvailable/';

            /*
             * Set modules ids
             */
            $data['module-id-edit'] = $this->_moduleData['edit']->id ?? 0;
            $data['module-id-delete'] = $this->_moduleData['delete']->id ?? 0;
            $data['module-id-permission'] = $this->_moduleData['permission']->id ?? 0;

            /*
             * Data Tables total records
             */
            $totalRecords = $languageModel->getAll(0, 0, '', $multiSort);
            $data['DT_recordsTotal'] = count($totalRecords);

            /*
             * Data Tables total records after filters applied
             */
            $recordsAfterFilter = $languageModel->getAll(0, 0, $search, $multiSort, $mySqlFilter);
            $data['DT_recordsFiltered'] = count($recordsAfterFilter);

            /*
             * Data Tables records after filters and pagination
             */
            $records = $languageModel->getAll($start, $length, $search, $multiSort, $mySqlFilter);
            $data['DT_draw'] = intval($this->input->get('draw'));

            $data['records'] = $this->_prepareData($records);

            /*
             * Render the view
             */
            $this->loader->view('Administration/Language/ManageDT', $data);
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
                    'language_id' => $row->language_id,
                    'reference_name' => $row->reference_name,
                    'local_name' => $row->local_name,
                    'iso2' => $row->iso2,
                    'iso3' => $row->iso3,
                    'is_active' => $row->is_active,
                ];
            }
        }

        return $rowsArr;
    }

}
