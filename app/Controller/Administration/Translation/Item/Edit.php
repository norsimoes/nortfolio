<?php

namespace Controller\Administration\Translation\Item;

use Lib\App;

/**
 * Edit
 *
 * Edit an existing translation item record.
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
        $this->_moduleData = (new \Controller\Administration\Translation\Item\Dashboard())->getModuleData();

        // Load vocabulary
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
        $this->_i18n = (new \Model\Core\I18nFile())->get('Translation');
    }

    /**
     * Index
     *
     * Prepares the necessary data to display the formulary in "edit" mode.
     *
     * @throws \Exception
     */
    public function index(int $translationId = 0, int $groupId = 0, int $itemId = 0): void
    {
        /*
         * Load classes
         */
        $itemModel = new \Model\Core\TranslationItem($translationId, $groupId);

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
        $data['url-back'] = $this->_moduleData['manage']->url . $translationId . '/' . $groupId . '/' ?? '';

        /*
         * Set form action and labels
         */
        $data['url-formulary-action'] = $this->_activeModule->url . 'register/' . $translationId . '/' . $groupId . '/' . $itemId . '/';
        $data['url-submit-label'] = $this->i18nCore['Common'][2];
        $data['url-cancel-label'] = $this->i18nCore['Common'][3];

        // Inner breadcrumbs
        $breadcrumbsController = new \Controller\Administration\Translation\Breadcrumbs();
        $data['inner-breadcrumbs'] = $breadcrumbsController->getBreadcrumbs($this->_activeModule->call_sign, $translationId, $groupId);

        // Get data
        $data['item-data'] = $itemModel->getItemData($itemId);

        /*
         * Render view
         */
        $template['interface-active'] = 'Administration';
        $template['menu-active'] = 'Administration/Translation';

        $this->template->loadView('Administration/Translation/Item/Formulary', $data);
        $this->template->render('Administration', $template);
    }

    /**
     * Register
     *
     * Handles the registration of an existing translation item.
     *
     * @throws \Exception
     */
    public function register(int $translationId = 0, int $groupId = 0, int $itemId = 0): void
    {
        // Check if POST exists
        $this->checkPost($this->_moduleData['edit']->route, [$translationId, $groupId]);

        /*
         * Load classes
         */
        $itemModel = new \Model\Core\TranslationItem($translationId, $groupId);

        /*
         * Process translation fields
         */
        $translation = new \Lib\Html\TranslationField();
        $valueArr = $translation->processPost('value');

        // Update translation item record
        $operationStatus = $itemModel->edit($itemId, $valueArr);

        if (!$operationStatus) {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][8]);

            // Redirect
            $this->goToRoute($this->_moduleData['edit']->route, [$translationId, $groupId, $itemId]);
        }

        // Set message
        $this->session->setMessage('success', $this->_i18n['MsgSuccess'][8]);

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route, [$translationId, $groupId]);
    }

}
