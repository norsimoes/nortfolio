<?php

namespace Lib;

/**
 * Module
 *
 * Modular system utilities.
 */
class Module
{
    private static ?Module $_instance = null;

    /**
     * Get instance
     *
     * Returns the class instance while preventing double load.
     */
    public static function getInstance(): Module
    {
        if (self::$_instance === null) {

            self::$_instance = new Module();
        }

        return self::$_instance;
    }

    /**
     * Get active
     *
     * Returns the currently active module data.
     */
    public static function getActive(): ?object
    {
        $route = Router::getInstance()->getDbRoute();

        $moduleModel = new \Model\Core\Module();

        return $moduleModel->getByRoute($route);
    }

}
