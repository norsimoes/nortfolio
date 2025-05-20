<?php

namespace Controller\Administration\Module;

use Lib\App;

/**
 * Actions
 *
 * Deploy the predefined controller actions modules.
 */
class Actions extends App
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
     * Default class method.
     *
     * @throws \Exception
     */
    public function index(): void
    {
        $this->url->redirect(APP_URL);
    }

    /**
     * Get actions
     *
     * Retrieve a list with the controller actions modules.
     *
     * @throws \Exception
     */
    public function getActions(): void
    {
        // Get controller actions
        $actionsArr = (new \Model\Core\ControllerAction())->getAllAction();

        if (!$actionsArr) {

            $returnArr['status'] = 'error';
            $returnArr['message'] = $this->_i18n['MsgError'][6];

        } else {

            /*
             * Set data
             */
            $data['i18n-core'] = $this->i18nCore;
            $data['i18n'] = $this->_i18n;
            $data['actions'] = $actionsArr;

            $returnArr = [
                'status' => 'success',
                'message' => 'Got actions data!',
                'data' => (new \Lib\Loader())->view('Administration/Module/ModalActions', $data, true)
            ];
        }

        /*
         * Return data
         */
        echo json_encode($returnArr);
        exit();
    }

    /**
     * Create actions
     *
     * Deploy the defined controller actions modules.
     *
     * @throws \Exception
     */
    public function createActions(int $parentModuleId = 0): void
    {
        // Check if POST exists
        $this->checkPost($this->_moduleData['manage']->route, [$parentModuleId]);

        /*
         * Load classes
         */
        $actionModel = new \Model\Core\ControllerAction();
        $moduleModel = new \Model\Core\Module();

        // Get parent data
        $parentObj = $moduleModel->getById($parentModuleId);

        /*
         * Get checked actions
         */
        foreach ($_POST as $key => $value) {

            $actionObj = $actionModel->getDataObject($key);

            $position = $moduleModel->getPosition($parentModuleId);

            /*
             * Process module
             */
            $addController = new \Controller\Administration\Module\Add();

            $addStatus = $addController->processModule($parentModuleId, $actionObj->call_sign, $actionObj->name_arr, $actionObj->desc_arr, $parentObj->icon, 1, $position);

            if (!is_int($addStatus)) {

                // Set message
                $this->session->setMessage('error', $addStatus);

                // Redirect
                $this->goToRoute($this->_moduleData['manage']->route, [$parentModuleId]);
            }
        }

        // Set message
        $this->session->setMessage('success', $this->_i18n['MsgSuccess'][5]);

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route, [$parentModuleId]);
    }

}
