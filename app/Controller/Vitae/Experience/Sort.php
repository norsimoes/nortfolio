<?php

namespace Controller\Vitae\Experience;

use Lib\App;

/**
 * Sort
 *
 * Sort existing records.
 */
class Sort extends App
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
        $this->_moduleData = (new \Controller\Vitae\Experience\Dashboard())->getModuleData();

        /*
         * Load vocabulary
         */
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
        $this->_i18n = (new \Model\Core\I18nFile())->get('Experience');
    }

    /**
     * Index
     *
     * Prepares the necessary data to display the sort list.
     *
     * @throws \Exception
     */
    public function index(): void
    {
        /*
         * Load classes
         */
        $experienceModel = new \Model\Vitae\Experience();

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
        $data['url-back'] = $this->_moduleData['manage']->url?? '';
        $data['url-sort'] = APP_URL . $this->_activeModule->url . 'sort/';

        // Set module data
        $data['experience-data'] = $experienceModel->getAll();

        /*
         * Render view
         */
        $template['interface-active'] = 'Vitae';
        $template['menu-active'] = 'Vitae/Experience';

        $this->template->loadView('Vitae/Experience/Sort', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Sort
     *
     * Sort the listed items.
     *
     * @throws \Exception
     */
    public function sort(): string
    {
        /*
         * Load model class
         */
        $experienceModel = new \Model\Vitae\Experience();

        $successCount = 0;
        $errorCount = 0;
        $returnArr = [];

        // Get post values
        $positionArr = $_POST['position'];

        if (is_array($positionArr) && count($positionArr) > 0) {

            foreach ($positionArr as $position => $id) {

                if ($experienceModel->updatePosition($id, $position + 1)) {
                    $successCount ++;
                } else {
                    $errorCount ++;
                }
            }
        }

        /*
         * Set messages
         */
        if ($successCount) {

            $returnArr['status'] = 'success';
            $returnArr['message'] = $this->_i18n['MsgSuccess'][4];
        }

        if ($errorCount) {

            $returnArr['status'] = 'error';
            $returnArr['message'] = $this->_i18n['MsgError'][5];
        }

        /*
         * Return data
         */
        if ($this->session->isAjax()) {

            echo json_encode($returnArr);
            exit();
        }

        return json_encode($returnArr);
    }

}
