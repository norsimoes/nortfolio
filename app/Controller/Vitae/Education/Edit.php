<?php

namespace Controller\Vitae\Education;

use Lib\App;

/**
 * Edit
 *
 * Edit an existing education record.
 */
class Edit extends App
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
        $this->_moduleData = (new \Controller\Vitae\Education\Dashboard())->getModuleData();

        // Load vocabulary
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
        $this->_i18n = (new \Model\Core\I18nFile())->get('Education');
    }

    /**
     * Index
     *
     * Prepares the necessary data to display the formulary in "edit" mode.
     *
     * @throws \Exception
     */
    public function index(int $educationId = 0): void
    {
        /*
         * Load classes
         */
        $educationModel = new \Model\Vitae\Education();

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
        $data['role-id'] = $this->session->get('user')->role_id;

        /*
         * Set modules URLs
         */
        $data['url-back'] = $this->_moduleData['manage']->url ?? '';

        /*
         * Set form action and labels
         */
        $data['url-formulary-action'] = $this->_activeModule->url . 'register/' . $educationId . '/';
        $data['url-submit-label'] = $this->i18nCore['Common'][2];
        $data['url-cancel-label'] = $this->i18nCore['Common'][3];

        // Get data object
        $data['education-data'] = $educationModel->getDataObject($educationId);

        /*
         * Render view
         */
        $template['interface-active'] = 'Vitae';
        $template['menu-active'] = 'Vitae/Education';

        $this->template->loadView('Vitae/Education/Formulary', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Register
     *
     * Handles the registration of an existing record.
     *
     * @throws \Exception
     */
    public function register(int $educationId = 0): void
    {
        // Check if POST exists
        $this->checkPost($this->_moduleData['edit']->route, [$educationId]);

        /*
         * Load classes
         */
        $educationModel = new \Model\Vitae\Education();

        /*
         * Process translation fields
         */
        $translation = new \Lib\Html\TranslationField();
        $institutionArr = $translation->processPost('institution', true);
        $startArr = $translation->processPost('start', true);
        $endArr = $translation->processPost('end', true);
        $courseArr = $translation->processPost('course', true);
        $descArr = $translation->processPost('desc', true);
        $gradeArr = $translation->processPost('grade', true);

        // Update education record
        $operationStatus = $educationModel->edit($educationId, $institutionArr, $startArr, $endArr, $courseArr, $descArr, $gradeArr);

        if ($operationStatus) {

            // Set message
            $this->session->setMessage('success', $this->_i18n['MsgSuccess'][2]);

        } else {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][2]);

            // Redirect
            $this->goToRoute($this->_moduleData['add']->route);
        }

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route);
    }

}
