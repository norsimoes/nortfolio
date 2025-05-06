<?php

/* ----------------------------------------------------------------------------
 * MVC
 * ----------------------------------------------------------------------------
 */
try {

    /* ------------------------------------------------------------------------
     * User session
     * ------------------------------------------------------------------------
     */
    $session = \Lib\Session::getInstance();

    $session->initTime();
    $session->initI18n();
    $session->initMessage();

    /* ------------------------------------------------------------------------
     * Route mapping
     * ------------------------------------------------------------------------
     */
    $router = \Lib\Router::getInstance();

    $router->parseUrl();
    $router->map();

    $controller = $router->getController();
    $method = $router->getMethodName();
    $arguments = $router->getArguments();
    $controllerPath = $router->getControllerFilename($controller);

    /* ------------------------------------------------------------------------
     * Initialize the application
     * ------------------------------------------------------------------------
     */
    if (is_file($controllerPath)) {

        $module = new $controller();

        if (is_object($module)) {

            $module->initTemplate();

            if (method_exists($module, $method)) {

                $module->$method(...$arguments);

            } else {

                $module->index(...$arguments);
            }
        }

    } else {

        throw new \Exception('Controller file not found!', 400);
    }

} catch (Exception $e) {

    // Display the error message to the user
    $report = new Lib\Report($e);

    $report->renderErrorPage('Exception');

} catch (Error $e) {

    // Display the error message to the user
    $report = new Lib\Report($e);

    $report->renderErrorPage('Error');
}
