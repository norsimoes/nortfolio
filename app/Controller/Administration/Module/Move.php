<?php

namespace Controller\Administration\Module;

use Lib\App;

/**
 * Move
 *
 * Move a module and its children to a new parent.
 */
class Move extends App
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
     * Get target module
     *
     * Retrieve a list of all modules that are valid parents when moving a module.
     *
     * @throws \Exception
     */
    public function getTargetModule(int $moduleId = 0): void
    {
        // Get parent target modules
        $targetArr = (new \Model\Core\Module())->getAllForSelect($moduleId, true, true);

        if (!$targetArr) {

            $returnArr['status'] = 'error';
            $returnArr['message'] = $this->_i18n['MsgError'][7];

        } else {

            /*
             * Set data
             */
            $data['i18n-core'] = $this->i18nCore;
            $data['i18n'] = $this->_i18n;
            $data['targets'] = $targetArr;

            $returnArr = [
                'status' => 'success',
                'message' => 'Got parent target modules!',
                'data' => (new \Lib\Loader())->view('Administration/Module/ModalMove', $data, true)
            ];
        }

        /*
         * Return data
         */
        echo json_encode($returnArr);
        exit();
    }

    /**
     * Move module
     *
     * Move a module to a new parent module.
     *
     * @throws \Exception
     */
    public function moveModule(int $moduleId = 0): void
    {
        // Load class
        $moduleModel = new \Model\Core\Module();

        $operationStatus = 0;

        // Get new parent module id
        $newParentId = $this->input->post('parent-module-id') ?? 0;

        $moduleData = $moduleModel->getMoveData($moduleId);

        if ($moduleId && $newParentId) {

            // Update record
            $operationStatus = $moduleModel->updateParentId($moduleId, $newParentId);
        }

        if ($operationStatus) {

            // Update routes and urls
            (new \Controller\Administration\Module\Edit())->updateChildRoute($moduleId, $newParentId, $moduleData->call_sign, $moduleData->name_arr);

            // Set message
            $this->session->setMessage('success', $this->_i18n['MsgSuccess'][6]);

        } else {

            // Set message
            $this->session->setMessage('error', $this->_i18n['MsgError'][8]);

            // Redirect
            $this->goToRoute($this->_moduleData['manage']->route);
        }

        // Redirect
        $this->goToRoute($this->_moduleData['manage']->route, [$newParentId]);
    }

}
