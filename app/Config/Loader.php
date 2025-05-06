<?php

/* ----------------------------------------------------------------------------
 * Class auto loader
 *
 * Autoload classes based on a 1:1 mapping from namespace to directory structure.
 * ----------------------------------------------------------------------------
 */
spl_autoload_register(function(string $className = '') {

    // Replace namespace separator with directory separator
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);

    // Get full name of file containing the required class
    $file = APP_BASEPATH_APP . $className . '.php';

    // Get file if it is readable
    if (is_readable($file)) {

        require_once $file;

    } else {

        throw new \Exception('PHP Class "' . $className . '" not found on the file: "' . $file . '".');
    }
});
