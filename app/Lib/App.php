<?php

namespace Lib;

/**
 * App
 *
 * Application wrapper class extended by user defined controllers.
 */
class App
{
    public ?Template $template = null;
    public ?Router $router = null;
    public ?Module $module = null;
    public ?Session $session = null;
    public ?Loader $loader = null;
    public ?Input $input = null;
    public ?Url $url = null;
    protected ?object $_activeModule = null;
    public array $i18nCore = [];

    /**
     * Class constructor
     *
     * @throws \Exception
     */
    public function __construct()
    {
        /*
         * Instantiate classes
         */
        $this->router = Router::getInstance();
        $this->module = Module::getInstance();
        $this->session = Session::getInstance();
        $this->loader = Loader::getInstance();
        $this->input = Input::getInstance();
        $this->url = Url::getInstance();

        // Load vocabulary
        $this->i18nCore = (new \Model\Core\I18nFile())->get('Core');
    }

    /**
     * Init Template
     *
     * Initialize the template object setting the current route and template name.
     *
     * @throws \Exception
     */
    public function initTemplate(): void
    {
        $this->template = new Template($this);

        if (!is_object($this->template)) {
            throw new \Exception('Template initialization fail!');
        }

        if (!property_exists($this->template, 'controller')) {
            throw new \Exception('Template controller property is missing!');
        }

        if (!property_exists($this->template, 'method')) {
            throw new \Exception('Template method property is missing!');
        }

        if (!property_exists($this->template, 'arguments')) {
            throw new \Exception('Template arguments property is missing!');
        }

        $this->template->controller = $this->router::$controller;
        $this->template->method = $this->router::$method;
        $this->template->arguments = $this->router::$arguments;
    }

    /**
     * Set security key
     *
     * Set the session security key entry.
     *
     * @throws \Exception
     */
    public function setSecurityKey(string $key = ''): void
    {
        $this->session->set($key, sha1(session_id() . APP_PASSWORD_HASH));
    }

    /**
     * Check security key
     *
     * Ascertain if the request came from the application.
     *
     * @throws \Exception
     */
    public function checkSecurityKey(string $key = '', string $failRoute = '', array $paramArr = []): void
    {
		if ($this->session->get($key) != sha1(session_id() . APP_PASSWORD_HASH)) {

			 // Set message
             $this->session->setMessage('error', $this->i18nCore['MsgError'][2]);

			 // Redirect
			 $this->goToRoute($failRoute, $paramArr);
		}
    }

    /**
     * Check POST
     *
     * Makes sure we have a POST method with data to process.
     *
     * @throws \Exception
     */
    public function checkPost(string $failRoute = '', array $paramArr = []): void
    {
        if (!isset($_POST) || !is_array($_POST) || count($_POST) <= 0) {

            // Set message
            $this->session->setMessage('error', $this->i18nCore['MsgError'][3]);

            // Redirect
            $this->goToRoute($failRoute, $paramArr);
        }
    }

    /**
     * Check login
     *
     * Redirects to target module or app url if the session doesn't have the logged user data.
     *
     * @throws \Exception
     */
    public function requiresAuthentication(string $targetRoute = ''): void
    {
		if (!$this->session->getLogin()) {

            if ($targetRoute) {

                $this->goToRoute($targetRoute);

            } else {

                $this->url->redirect(APP_URL);
            }
		}
    }

    /**
     * Check permission
     *
     * Makes sure the user have permission to operate in the module.
     *
     * @throws \Exception
     */
    public function checkPermission(mixed $activeModule = null, bool $returnMessage = true): bool
    {
        // Get active module
        $moduleObj = is_object($activeModule) ? $activeModule : $this->_activeModule;

        if (!\Lib\Access::getInstance()->module($moduleObj->module_id)) {

            if ($returnMessage) {

                // Set message
                $this->session->setMessage('error', $this->i18nCore['MsgError'][1]);

                // Redirect
                $this->goToRoute('Administration');
            }

            return false;
        }

        return true;
    }

    /**
     * Go to route
     *
     * Receives a module route and redirects to it.
     *
     * @throws \Exception
     */
    public function goToRoute(string $route = '', array $paramArr = []): void
    {
        // Get target module
        $targetModule = (new \Model\Core\Module())->getByRoute($route);

        if (!$targetModule) {
            throw new \Exception('Target module not found!');
        }

        // Setup URL
        $url = $this->url->base($targetModule->url);

        /*
         * Add parameters to url
         */
        if (!empty($paramArr)) {

            foreach ($paramArr as $param) {

                $url = $url . $param . '/';
            }
        }

        // Redirect
        $this->url->redirect($url, 'location', 303);
    }

}
