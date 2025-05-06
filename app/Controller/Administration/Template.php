<?php

namespace Controller\Administration;

use Lib\App;

/**
 * Template
 *
 * Provides features related to the Administration template.
 */
class Template extends App
{
    private array $_i18n;

    /**
     * Class Constructor
     *
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();

        // Load vocabulary
        $this->_i18n = (new \Model\Core\I18nFile())->get('Website');
    }

	/**
	 * Get i18n
     *
     * Get the controller loaded i18n.
	 */
	public function getI18n(): array
	{
        return $this->_i18n;
    }

	/**
     * Template url
     *
	 * Returns the full URLs of the controllers to the header template.
     */
    public function templateUrl(): object
    {
        $moduleModel = new \Model\Core\Module();

        $websiteRoute = $moduleModel->getByRoute('Website');
        $administrationRoute = $moduleModel->getByRoute('Administration');
        $loginRoute = $moduleModel->getByRoute('Administration/User/Login');
        $logoutRoute = $moduleModel->getByRoute('Administration/User/Logout');
        $contactRoute = $moduleModel->getByRoute('Website/Contact');

        return (object) [
            'root' => APP_URL,
            'website' => $websiteRoute ? APP_URL . $websiteRoute->url : '',
            'administration' => $administrationRoute ? APP_URL . $administrationRoute->url : '',
            'authenticate' => $loginRoute ? APP_URL . $loginRoute->url : '',
            'logout' => $logoutRoute ? APP_URL . $logoutRoute->url : '',
            'set_language' => $contactRoute ? APP_URL . $contactRoute->url . 'setLanguage/' : '',
        ];
    }

}
