<?php

/* ----------------------------------------------------------------------------
 * Error Reporting
 * ----------------------------------------------------------------------------
 */
if (defined('APP_DEBUG') && APP_DEBUG === true) {

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

} else {

    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
}
