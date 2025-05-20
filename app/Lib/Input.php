<?php

namespace Lib;

/**
 * Input
 *
 * Input handler for basic $_POST, $_GET, $_SERVER and $_COOKIE data.
 */
class Input
{
    private static ?Input $_instance = null;

    /**
     * Get instance
     *
     * Returns the class instance while preventing double load.
     */
    public static function getInstance(): Input
    {
        if (self::$_instance === null) {

            self::$_instance = new Input();
        }

        return self::$_instance;
    }

    /**
     * Get index
     *
     * Helper function to retrieve a value from a global array.
     */
    private function _getIndex(array $array = [], string $index = '', bool $filter = false): mixed
    {
        if (!isset($array[$index])) return false;

        if ($filter) {

            if (is_array($array[$index])) {

                array_walk_recursive($array[$index], function (&$v) {
                    $v = filter_var(trim($v), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                });

                return $array[$index];
            }

            return filter_var(trim($array[$index]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        return $array[$index];
    }

    /**
     * Get all
     *
     * Helper function to retrieve a global array.
     */
    private function _getAll(array $array = [], bool $filter = false): array
    {
        if (!$filter) return $array;

        $return = [];

        foreach ($array as $key => $value) {

            $return[$key] = filter_var(trim($value), FILTER_UNSAFE_RAW);
        }

        return $return;
    }

    /**
     * File
     *
     * Retrieve a $_FILE index value.
     * If no index specified, returns the entire array.
     */
    public function file(string $index = ''): bool | array | string
    {
        if (empty($index)) return $this->_getAll($_FILES);

        return $this->_getIndex($_FILES, $index);
    }

    /**
     * Post
     *
     * Retrieve a $_POST index value.
     * If no index specified, returns the entire array.
     */
    public function post(string $index = '', bool $filter = false): bool | array | string
    {
        if (empty($index)) return $this->_getAll($_POST, $filter);

        return $this->_getIndex($_POST, $index, $filter);
    }

    /**
     * Get
     *
     * Retrieve a $_GET index value.
     */
    public function get(string $index = ''): bool | array | string
    {
        return $this->_getIndex($_GET, $index);
    }

    /**
     * Server
     *
     * Retrieve a $_SERVER index value.
     */
    public function server(string $index = ''): bool | array | string
    {
        return $this->_getIndex($_SERVER, $index);
    }

    /**
     * Cookie
     *
     * Retrieve a $_COOKIE index value.
     */
    public function cookie(string $index = ''): bool | array | string
    {
        return $this->_getIndex($_COOKIE, $index);
    }
}
