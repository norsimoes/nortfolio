<?php

namespace Lib;

/**
 * Router
 *
 * Route handler that parses URIs and determines internal routing.
 */
class Router
{
    private static ?Router $_instance = null;
    public static array $urlPath = [];
    public static string $controllerRoute = '';
    public static string $controllerFilename = '';
    public static string $controllerNamespace = '';
    public static string $controllerPath = '';
    public static string $controller = '';
    public static string $method = '';
    public static array $arguments = [];

    /**
     * Get Instance
     *
     * Returns the class instance while preventing double load.
     */
    public static function getInstance(): Router
    {
        if (self::$_instance === null) {

            self::$_instance = new Router();
        }

        return self::$_instance;
    }

    /**
     * Parse URL
     *
     * Collects the URL path, clears values and saves it in a local array.
     *
     * @throws \Exception
     */
    public function parseUrl(): Router
    {
        // Remove tags and the query string, as we only want the path
        $urlPath = strtok(strip_tags($_SERVER['REQUEST_URI']), '?');

        // Current URI structure
        $urlArr = explode('/', $urlPath);

        if (!is_array($urlArr)) throw new \Exception('No route found!');

        /*
         * Remove the App folder from the collected URI structure
         */
        $key = array_search(rtrim(APP_BASEDIR, '/'), $urlArr);

        if ($key) unset($urlArr[$key]);

        // Clear empty entries
        $urlArr = array_filter($urlArr);

        self::$urlPath = array_values($urlArr);

        return $this;
    }

    /**
     * Map
     *
     * Handles the database mapping process.
     *
     * @throws \Exception
     */
    public function map(): Router
    {
        $routerModel = new \Model\Core\Router();

        // Collect mapped route from the database using the current url path
        self::$controllerRoute = $routerModel->getByPath(implode('/', self::$urlPath));

        /*
         * Nothing found, lets try a direct match
         */
        if (empty(self::$controllerRoute)) {

            self::$controllerRoute = implode(DIRECTORY_SEPARATOR, self::$urlPath);
        }

        /*
         * Nothing found, lets use the default
         */
        if (empty(self::$controllerRoute)) {

            self::$controllerRoute = APP_DEFAULT_ROUTE;
        }

        /*
         * If the file system location does not contain a directory separator,
         * we can only be targeting a controller file name or a folder with
         * the default controller file name inside.
         */
        if (!str_contains(self::$controllerRoute, DIRECTORY_SEPARATOR)) {

            /*
             * Is it a Directory?
             */
            $isDirAsIs = is_dir(APP_PATH_CONTROLLER . self::$controllerRoute);

            $isDirNamingConventions = is_dir(APP_PATH_CONTROLLER . ucfirst(self::$controllerRoute));

            if ($isDirAsIs || $isDirNamingConventions) {

                if ($isDirAsIs) {
                    $controllerPath = self::$controllerRoute . DIRECTORY_SEPARATOR;
                } else {
                    $controllerPath = ucfirst(self::$controllerRoute) . DIRECTORY_SEPARATOR;
                }

                // Since it is a directory, set the default controller as the one to be used
                $controllerName = APP_DEFAULT_CONTROLLER;

                // Remove file system path
                self::$controllerPath = str_replace(APP_PATH_CONTROLLER, '', $controllerPath);

                // Change directory separator to namespace separator
                self::$controllerPath = str_replace(DIRECTORY_SEPARATOR, '\\', self::$controllerPath);

                // Clear leading and trailing slash
                self::$controllerPath = trim(self::$controllerPath, '\\');

                // Ensure trailing slash if a path exists
                self::$controllerPath .= empty(self::$controllerPath) ? '' : '\\';

            } else {

                /*
                 * Check if the file exists and is readable,
                 * otherwise switch the case on the first
                 * letter and follows with that.
                 *
                 * Falls back to the default controller.
                 */
                if (is_readable(APP_PATH_CONTROLLER . self::$controllerRoute . '.php')) {

                    $controllerName = self::$controllerRoute;

                } elseif (is_readable(APP_PATH_CONTROLLER . ucfirst(self::$controllerRoute) . '.php')) {

                    $controllerName = ucfirst(self::$controllerRoute);

                } else {

                    $controllerName = APP_DEFAULT_CONTROLLER;
                }
            }

            // Set controller name
            self::$controller = $controllerName;

            // Set default method
            self::$method = APP_DEFAULT_METHOD;

            // Initialize arguments
            self::$arguments = [];

            // Set controller filename
            self::$controllerFilename = APP_PATH_CONTROLLER . self::$controllerRoute . DIRECTORY_SEPARATOR . $controllerName . '.php';

            // Set controller namespace
            self::$controllerNamespace = 'Controller\\' . trim(self::$controllerPath, '\\');

            /*
             * Our target is the first URL path entry, if we have more...
             */
            if (is_array(self::$urlPath) && count(self::$urlPath) >= 2) {

                // cache the URL path array
                $argumentsArr = self::$urlPath;

                // clear the entry that represents our target file
                unset($argumentsArr[0]);

                // set the rest as arguments
                self::$arguments = $argumentsArr;
            }

            return $this;

        } else {

            // Initialize arguments
            self::$arguments = [];

            /*
             * Our target is the first URL path entry, if we have more...
             */
            if (is_array(self::$urlPath) && count(self::$urlPath) >= 2) {

                // cache the URL path array
                $argumentsArr = self::$urlPath;

                // clear the entry that represents our target file
                unset($argumentsArr[0]);

                // set the rest as arguments
                self::$arguments = $argumentsArr;
            }
        }

        /*
         * We must identify what each member of the file system location
         * really is to map the route correctly.
         */
        $arr = explode(DIRECTORY_SEPARATOR, self::$controllerRoute);

        /*
         * Let's assume that all entries are arguments,
         * and we will clear entries as we properly identify them
         */
        $argumentsArr = self::$urlPath;

        /*
         * Identify each entry
         */
        $controllerName = '';

        $controllerPath = APP_PATH_CONTROLLER;

        foreach ($arr as $key => $entry) {

            /*
             * While we don't have a controller, we are
             * either finding folders or a file.
             */
            if (empty($controllerName)) {

                $isDirAsIs = is_dir($controllerPath . $entry);

                $isDirNamingConventions = is_dir($controllerPath . ucfirst($entry));

                if ($isDirAsIs || $isDirNamingConventions) {

                    if ($isDirAsIs) {

                        $controllerPath .= $entry . DIRECTORY_SEPARATOR;

                    } else {

                        $controllerPath .= ucfirst($entry) . DIRECTORY_SEPARATOR;
                    }

                    unset($argumentsArr[$key]);

                    continue;
                }

                $isFileAsIs = is_file($controllerPath . $entry . '.php');

                $isFileNamingConventions = is_file($controllerPath . ucfirst($entry) . '.php');

                if ($isFileAsIs || $isFileNamingConventions) {
                    if ($isFileAsIs) {
                        $controllerName = $entry;
                    } else {
                        $controllerName = ucfirst($entry);
                    }

                    unset($argumentsArr[$key]);

                    continue;
                }

            }

            /*
             * If we got here, this entry is our method
             * and we clear it from the arguments list.
             *
             * Since we have the method, no point on
             * continuing the loop..
             */
            $ctrl = 'Controller\\' . str_replace('/', '\\', $controllerName);

            $ctrlPath = APP_PATH_CONTROLLER;
            $ctrlPath .= str_replace('\\', '/', str_replace('Controller\\', '', $ctrl));
            $ctrlPath .= '.php';

            if (is_file($ctrlPath)) {

                $rc = new \ReflectionClass($ctrl);

                if ($rc->hasMethod($entry)) {

                    self::$method = $entry;

                    unset($argumentsArr[$key]);
                }
            }

            break;
        }

        /*
         * Reset arguments that remain
         */
        if (is_array($argumentsArr) && count($argumentsArr) >= 1) {
            $argumentsArr = array_values($argumentsArr);
        }

        /*
         * Confirm that we have a controller
         */
        if (empty($controllerName)) {

            /*
             * No controller so far, maybe we have the default controller filename on our path?
             */
            $defaultController = APP_DEFAULT_CONTROLLER;

            $isFileAsIs = is_file($controllerPath . $defaultController . '.php');

            $isFileNamingConventions = is_file($controllerPath . ucfirst($defaultController) . '.php');

            if ($isFileAsIs || $isFileNamingConventions) {
                if ($isFileAsIs) {
                    $controllerName = $defaultController;
                } else {
                    $controllerName = ucfirst($defaultController);
                }
            }

            if (empty($controllerName)) {

                throw new \Exception('Controller route not found!', 404);
            }
        }

        // Remove file system path
        self::$controllerPath = str_replace(APP_PATH_CONTROLLER, '', $controllerPath);

        // Change directory separator to namespace separator
        self::$controllerPath = str_replace(DIRECTORY_SEPARATOR, '\\', self::$controllerPath);

        // Clear leading and trailing slash
        self::$controllerPath = trim(self::$controllerPath, '\\');

        // Ensure trailing slash if a path exists
        self::$controllerPath .= empty(self::$controllerPath) ? '' : '\\';

        // Set controller name
        self::$controller = $controllerName;

        /*
         * Method empty?
         * If method is still empty, means that on arguments array
         * we probably have a method on the first entry...
         */
        if (empty(self::$method)) {

            if (is_array($argumentsArr) && count($argumentsArr) >= 1) {

                $methodName = $argumentsArr[0];

                $rc = new \ReflectionClass($this->getController());

                if ($rc->hasMethod($methodName)) {
                    self::$method = $methodName;

                    /*
                     * Remove the method from arguments list
                     */
                    unset($argumentsArr[0]);

                    /*
                     * Reset arguments that remain
                     */
                    if (is_array($argumentsArr) && count($argumentsArr) >= 1) {
                        $argumentsArr = array_values($argumentsArr);
                    }
                }
            }

            /*
             * If we still haven't found a method,
             * lets use the default configured.
             */
            if (empty(self::$method)) {
                self::$method = APP_DEFAULT_METHOD;
            }
        }

        /**
         * If we still have arguments on our temporary variable, pass them to the method,
         * otherwise, we make sure the argument list isn't polluted.
         */
        if (!empty($argumentsArr) && is_array($argumentsArr) && count($argumentsArr) >= 1) {
            self::$arguments = $argumentsArr;
        } else {
            self::$arguments = [];
        }

        // Set controller filename
        self::$controllerFilename = $controllerPath . $controllerName . '.php';

        // Set controller namespace
        self::$controllerNamespace = 'Controller\\' . trim(self::$controllerPath, '\\');

        return $this;
    }

    /**
     * Get controller
     *
     * The php class and namespace to work with.
     */
    public function getController(): string
    {
        return 'Controller\\' . self::$controllerPath . self::$controller;
    }

    /**
     * Get controller name
     *
     * The php class name to work with.
     */
    public function getControllerName(): string
    {
        return self::$controller;
    }

    /**
     * Get controller path
     *
     * Returns the controller path inside the application controllers folder.
     */
    public function getControllerPath(bool $dbRoute = false): string
    {
        if ($dbRoute) {

            $path = str_replace('\\', DIRECTORY_SEPARATOR, self::$controllerPath);

            return trim($path, DIRECTORY_SEPARATOR);
        }

        return self::$controllerPath;
    }

    /**
     * Get controller filename
     *
     * Returns the controller filename with absolute path.
     */
    public function getControllerFilename(string $controller = ''): string
    {
        $controller = str_replace('/', '\\', $controller);

        $controllerRoute = str_replace(['Controller\\', '\\'], ['', '/'], $controller);

        return APP_PATH_CONTROLLER . $controllerRoute . '.php';
    }

    /**
     * Get method
     *
     * The controller class method name to work with.
     */
    public function getMethodName(): string
    {
        return self::$method;
    }

    /**
     * Get arguments
     *
     * Arguments are all that remains after mapping the
     * URL path into a local file system location.
     */
    public function getArguments(): array
    {
        return self::$arguments;
    }

    /**
     * Get db route
     *
     * Returns the expected database route string for the active route.
     */
    public function getDbRoute(): string
    {
        $path = $this->getControllerPath(true);

        $controller = $this->getControllerName();

        if ($controller != APP_DEFAULT_CONTROLLER) {

            return $path . DIRECTORY_SEPARATOR . $controller;
        }

        return $path;
    }

}
