<?php

namespace Lib;

/**
 * Template
 *
 * Handles the application template.
 * Can render the template or return the buffer output.
 */
class Template
{
    private ?App $_app = null;
    private string $_template = '';
    private string $_templateFilePath = '';
    private string $_view = '';
    private string $_scriptBody = '';
    private string $_scriptHead = '';
    private string $_cssHead = '';
    private string $_metaHead = '';
    public string $controller = '';
    public string $method = '';
    public array $arguments = [];

    public ?Template $template = null;
    public ?Router $router = null;
    public ?Module $module = null;
    public ?Session $session = null;
    public ?Loader $loader = null;
    public ?Input $input = null;
    public ?Url $url = null;
    public array $i18nCore = [];

    /**
     * Class constructor
     *
     * Instantiates the template engine using a reflection
     * of the received App object to share publicly accessible values.
     * It also allows to render templates outside the App dominion,
     * if no App object received.
     *
     * @throws \Exception
     */
    public function __construct(?App $app = null, string $template = '')
    {
        /*
         * Pass the App class reference to a local property
         * so that we can update it from this class and have
         * global methods available the same way across all files.
         */
        if ($app) {

            $this->_app = $app;

            $this->_reflect($app);
        }

        /*
         * Set a template name if we have received one.
         */
        if (!empty($template)) {

            $this->setTemplate($template);
        }
    }

    /**
     * Reflect
     *
     * Initializes a new App reflection class to
     * attach every global class to a local property.
     */
    private function _reflect(?App $app = null): void
    {
        $ref = new \ReflectionClass('\Lib\App');

        foreach ($ref->getProperties(\ReflectionProperty::IS_PUBLIC) as $public) {

            $property = $public->name;

            $this->$property = $app->$property;
        }
    }

    /**
     * Add script
     *
     * Generates a new script tag and appends it to
     * the list of HEAD or BODY script tags to apply on the page.
     */
    public function addScript(string $src = '', $where = 'body', $type = 'text/javascript'): void
    {
        if (defined('APP_DEBUG') && APP_DEBUG === true) {

            if (!str_contains($src, '?')) {
                $src .= '?t=' . time();
            } else {
                $src .= '&t=' . time();
            }
        }

        $scriptTag = '<script type="' . $type . '" src="' . $src . '"></script>' . PHP_EOL;

        if ($where == 'head') {
            $this->_scriptHead .= $scriptTag;
        } else {
            $this->_scriptBody .= $scriptTag;
        }
    }

    /**
     * Add css
     *
     * Generates a new CSS tag and appends it to
     * the list of CSS tags to apply on the page.
     */
    public function addCss(string $href = '', string $media = ''): void
    {
        if (empty($href)) return;

        $mediaAttr = !empty($media) ? 'media=" ' . $media . ' "' : '';

        $cssTag = '<link href="' . $href . '" rel="stylesheet" type="text/css" ' . $mediaAttr . ' />' . PHP_EOL;

        $this->_cssHead .= $cssTag;
    }

    /**
     * Add meta
     *
     * Generates a new meta tag and appends it
     * to the list of meta tags to apply on the page.
     */
    public function addMeta(string $name = '', string $content = ''): void
    {
        $metaTag = '<meta name="' . $name . '" content="' . $content . '" />' . PHP_EOL;

        $this->_metaHead .= $metaTag;
    }

    /**
     * Add line
     *
     * Generates a new meta tag and appends it
     * to the list of meta tags to apply on the page.
     */
    public function addLine(string $class = '', string $content = ''): void
    {
        echo $content;
    }

    /**
     * Set template
     *
     * Set the template folder and file paths.
     *
     * @throws \Exception
     */
    public function setTemplate(string $template = ''): void
    {
        if (empty($template)) {
            throw new \Exception('Template name is empty!');
        }

        $this->_template = $template;

        /*
         * Handle the template folder
         */
        $path = APP_PATH_TEMPLATE . $this->_template . DIRECTORY_SEPARATOR;

        if (!is_dir($path)) {

            $this->_template = ucfirst($this->_template);

            $path = APP_PATH_TEMPLATE . $this->_template . DIRECTORY_SEPARATOR;
        }

        if (!is_dir($path)) {

            $message = 'Template folder not found! Make sure you have a folder named ';
            $message .= '"' . $this->_template . '" inside your application templates folder.';

            throw new \Exception($message);
        }

        /*
         * Handle the template file
         */
        $filepath = $path . 'Template.php';

        if (!is_file($filepath)) {

            $message = 'Template file not found! Make sure you have a file named ';
            $message .= '"Template.php" inside your template folder.';

            throw new \Exception($message);
        }

        $this->_templateFilePath = $filepath;
    }

    /**
     * Get template
     *
     * Retrieve the currently active template name.
     */
    public function getTemplate(): string
    {
        return $this->_template;
    }

    /**
     * Load view
     *
     * Loads the specified view into the template.
     *
     * @throws \Exception
     */
    public function loadView(string $route = '', array $data = []): void
    {
        if (is_object($this->_app)) {

            // Load the requested view using the App class reference.
            $this->_view = $this->_app->loader->view($route, $data, true);

        } else {

            // No App object reflection, lets do it manually
            $this->_view = (new Loader())->view($route, $data, true);
        }
    }

    /**
     * Render
     *
     * Output the template with loaded view.
     *
     * @throws \Exception
     */
    public function render(string $template = '', array $data = []): void
    {
        if (!empty($template)) {
            $this->setTemplate($template);
        }

        if (empty($this->_template)) {
            throw new \Exception('Template name is empty!');
        }

        $_i18n = (new \Lib\Session())->getI18n('iso2');
        $_meta = $this->_metaHead;
        $_css = $this->_cssHead;
        $_jsHead = $this->_scriptHead;
        $_jsBody = $this->_scriptBody;
        $_view = $this->_view;

        if (!empty($data)) {
            $_data = $data;
        }

        ob_start();

        require $this->_templateFilePath;

        echo ob_get_clean();
    }

}
