<?php

/* --------------------------------------------------------------------------
 * Configuration files
 *
 * Wrapper for configuration files included in a specific order.
 * --------------------------------------------------------------------------
 */
$configArr = [
    'Path' => 'Path.php',
    'URL' => 'Url.php',
    'Debug' => 'Debug.php',
    'Database' => 'Database.php',
    'Loader' => 'Loader.php',
    'Session' => 'Session.php'
];

foreach ($configArr as $name => $file) {

    $path = __DIR__ . DIRECTORY_SEPARATOR . $file;

    if (is_file($path)) {

        require_once ($path);

    } else {

        die($name . ' settings not found!');
    }
}

/* ----------------------------------------------------------------------------
 * Unset global variables
 * ----------------------------------------------------------------------------
 */
require_once (__DIR__ . DIRECTORY_SEPARATOR . 'Unset.php');

/* ----------------------------------------------------------------------------
 * Minify output
 * ----------------------------------------------------------------------------
 */
function minify($buffer): string
{
    $search = ['/>[^\S ]+/', '/[^\S ]+</', '/(\s)+/', '/<!--(.|\s)*?-->/'];

    $replace = ['>', '<', '\\1', ''];

    return preg_replace($search, $replace, $buffer);
}
