<?php

// comment for git testing purposes

namespace Controller\Website;

use Lib\App;

/**
 * Dashboard
 *
 * Display the website interface dashboard.
 */
class Dashboard extends App
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
        $this->_moduleData = $this->getModuleData();

        // Load vocabulary
        $this->_i18n = (new \Model\Core\I18nFile())->get('Website');
    }

    /**
     * Index
     *
     * Display the website interface dashboard.
     *
     * @throws \Exception
     */
    public function index(): void
    {
        // Set the security key
        $this->setSecurityKey($this->_securityKey);

        /*
         * Load classes
         */
        $experienceModel = new \Model\Vitae\Experience();
        $educationModel = new \Model\Vitae\Education();

        /*
         * Initialize arrays
         */
        $data = [];
        $template = [];

        /*
         * Set data
         */
        $data['i18n'] = $this->_i18n;
        $data['active-module'] = $this->_activeModule;

        /*
         * Set modules URLs
         */
        $data['url-language'] = $this->_moduleData['language']->url ?? '';

        /*
         * Set lists
         */
        $data['skill-list'] = $this->getSkillData();
        $data['profile-list'] = $this->getProfileData();
        $data['experience-list'] = $experienceModel->getAll();
        $data['education-list'] = $educationModel->getAll();

        /*
         * Render view
         */
        $this->template->loadView('Website/Dashboard', $data);
        $this->template->render('Website', $template);
    }

    /**
     * Get skill data
     *
     * Prepare skill data for website display.
     */
    public function getSkillData(): array
    {
        /*
         * Load classes
         */
        $skillModel = new \Model\Vitae\Skill();

        $programmingData = $skillModel->getByType('Programming');
        $designData = $skillModel->getByType('Design');
        $languageData = $skillModel->getByType('Language');

        return [
            $this->_i18n['Skill'][1] => [
                $this->_i18n['Skill'][2] => $programmingData,
                $this->_i18n['Skill'][3] => $designData,
                $this->_i18n['Skill'][4] => $languageData,
            ]
        ];
    }

    /**
     * Get profile data
     *
     * Prepare profile data for website display.
     */
    public function getProfileData(): array
    {
        /*
         * Load classes
         */
        $profileModel = new \Model\Vitae\Profile();

        $contactData = $profileModel->getByType('Contact');
        $socialData = $profileModel->getByType('Social');
        $interestsData = $profileModel->getByType('Interests');

        return [
            $this->_i18n['Profile'][1] => [
                $this->_i18n['Profile'][2] => $contactData,
                $this->_i18n['Profile'][3] => $socialData,
                $this->_i18n['Profile'][4] => $interestsData,
            ]
        ];
    }

    /**
     * Get module data
     *
     * Retrieve the auxiliary modules data.
     */
    public function getModuleData(): array
    {
        $return = [];

        $moduleModel = new \Model\Core\Module();

        $routeArr = [
            'interface' => 'Website',
            'module' => 'Website/Dashboard',
            'language' => 'Website/Language',
        ];

        foreach ($routeArr as $name => $route) {

            $module = $moduleModel->getByRoute($route);

            if (!$module) {
                continue;
            }

            $return[$name] = (object) [
                'id' => $module->module_id,
                'route' => $module->route,
                'url' => APP_URL . $module->url,
            ];
        }

        return $return;
    }
}
