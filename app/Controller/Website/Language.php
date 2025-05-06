<?php

namespace Controller\Website;

use Lib\App;

/**
 * Language
 *
 * Handles the website language settings.
 */
class Language extends App
{
    protected ?object $_activeModule = null;
    protected array $_moduleData = [];
    protected array $_i18n = [];
    private string $_securityKey = 'website-key';

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

        // Get modules data
        $this->_moduleData = (new \Controller\Website\Dashboard())->getModuleData();

        // Load vocabulary
        $this->_i18n = (new \Model\Core\I18nFile())->get('Website');
    }

    /**
     * Index
     *
     * Set the selected language as active.
     *
     * @throws \Exception
     */
    public function index(): never
    {
        // Check session security token
        $this->checkSecurityKey($this->_securityKey, $this->_moduleData['interface']->route);

        /*
         * Load classes
         */
        $languageModel = new \Model\Core\Language();
        $userModel = new \Model\Entity\User();

        // Retrieve post data
        $encoded = $this->input->post('language');

        /*
         * Ascertain the language id
         */
        $languageId = 0;

        $active = $languageModel->getActive();

        $activeId = array_column($active, 'language_id');

        foreach ($activeId as $id) {

            $activeEncoded = sha1($id . APP_PASSWORD_HASH);

            if ($encoded === $activeEncoded) {

                $languageId = (int) $id;
                break;
            }
        }

        if (!$languageId) {

            $this->url->redirect(APP_URL);

        } else {

            /*
             * Set session i18n data
             */
            $languageData = $languageModel->getDataObject($languageId);

            $this->session->setI18n($languageData->iso2, $languageData->iso3, $languageId);
        }

        /*
         * Set user language data
         */
        $loggedUser = $this->session->get('user');

        if ($loggedUser) {

            $userData = $userModel->getDataObject($loggedUser->user_id);

            // Set user session data
            $this->session->setUser('language_id', $languageId);

            // Update user language id
            $userModel->editLanguageId($userData->user_id, $languageId);
        }

        /*
         * Prepare response
         */
        $return = (object) [
            'status' => 'success',
            'message' => 'Language successfully changed.',
            'redirect' => $_SERVER['HTTP_REFERER']
        ];

        /*
         * Return data
         */
        echo json_encode($return);
        exit();
    }

}
