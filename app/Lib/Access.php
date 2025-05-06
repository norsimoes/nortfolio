<?php

namespace Lib;

/**
 * Access
 *
 * Wrapper for the application access permission system.
 */
class Access
{
    private static ?Access $_instance = null;

    /**
     * Get instance
     *
     * Returns the class instance while preventing double load.
     */
    public static function getInstance(): Access
    {
        if (self::$_instance === null) {

            self::$_instance = new Access();
        }

        return self::$_instance;
    }

    /**
     * Module
     *
     * Ascertains user permission to the received module id.
     *
     * @throws \Exception
     */
    public static function module(int $moduleId = 0): bool
    {
        $userId = (int) Session::getInstance()->get('user')->user_id;

        return (new \Model\Entity\Access())->module($userId, $moduleId);
    }

}
