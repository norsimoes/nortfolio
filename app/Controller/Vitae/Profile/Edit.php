<?php

namespace Controller\Vitae\Profile;

use Lib\App;

/**
 * Edit
 *
 * Edit an existing profile record.
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
        $this->_moduleData = (new \Controller\Vitae\Profile\Dashboard())->getModuleData();

        // Load vocabulary
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
        $this->_i18n = (new \Model\Core\I18nFile())->get('Profile');
    }

    /**
     * Index
     *
     * Prepares the necessary data to display the formulary in "edit" mode.
     *
     * @throws \Exception
     */
    public function index(int $profileId = 0): void
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
        $data['role-id'] = $this->session->get('user')->role_id;

        /*
         * Set modules URLs
         */
        $data['url-back'] = $this->_moduleData['manage']->url ?? '';
        $data['url-delete-file'] = $this->_moduleData['delete']->url . 'deleteFile/' . $profileId . '/' ?? '';

        /*
         * Set form action and labels
         */
        $data['url-formulary-action'] = $this->_activeModule->url . 'register/' . $profileId . '/';
        $data['url-submit-label'] = $this->i18nCore['Common'][2];
        $data['url-cancel-label'] = $this->i18nCore['Common'][3];

        /*
         * Set lists
         */
        $data['type-list'] = [
            'Contact' => $this->_i18n['Formulary'][9],
            'Social' => $this->_i18n['Formulary'][10],
            'Interests' => $this->_i18n['Formulary'][11],
        ];

        // Get data object
        $data['profile-data'] = $profileModel->getDataObject($profileId);

        /*
         * Render view
         */
        $template['interface-active'] = 'Vitae';
        $template['menu-active'] = 'Vitae/Profile';

        $this->template->loadView('Vitae/Profile/Formulary', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Register
     *
     * Handles the registration of an existing record.
     *
     * @throws \Exception
     */
    public function register(int $profileId = 0): void
    {
        // Check if POST exists
        $this->checkPost($this->_moduleData['edit']->route, [$profileId]);

        /*
         * Load classes
         */
        $profileModel = new \Model\Vitae\Profile();

        // get profile data
        $profileObj = $profileModel->getById($profileId);

        /*
         * Retrieve POST values
         */
        $type = $this->input->post('type') ?: '';
        $url = $this->input->post('url') ?: '';

        /*
         * Process translation fields
         */
        $translation = new \Lib\Html\TranslationField();
        $nameArr = $translation->processPost('name', true);
        $tooltipArr = $translation->processPost('tooltip', true);

        /*
         * Process icon
         */
        $file = new \Lib\Html\FileImage();
        $icon = $file->processFile('icon', 'profile/', $profileObj->icon);

        // Update profile record
        $operationStatus = $profileModel->edit($profileId, $nameArr, $type, $url, $tooltipArr, $icon);

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
