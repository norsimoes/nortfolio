<?php

namespace Lib;

/**
 * Loader
 *
 * Handles the loading of Views into the App core object.
 */
class Loader
{
    private static ?Loader $_instance = null;

    /**
     * Get instance
     *
     * Returns the class instance while preventing double load.
     */
    public static function getInstance(): Loader
    {
        if (self::$_instance === null) {

            self::$_instance = new Loader();
        }

        return self::$_instance;
    }

    /**
     * View
     *
     * Loads a view and sets an optional data array.
     * Outputs data to the browser, or optionally returns it.
     *
     * @throws \Exception
     */
    public function view( string $route = '', array $data = [], bool $return = false ): string
    {
        $filename = APP_PATH_VIEW . $route . '.php';

        if (!is_file($filename)) throw new \Exception('View file not found!');

        ob_start();

        require $filename;

        $buffer = ob_get_clean();

        if (!$return) {

            echo $buffer;
            exit();
        }

        return $buffer;
    }

}
