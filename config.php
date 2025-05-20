<?php

/* ----------------------------------------------------------------------------
 * PHP runtime configuration
 * ----------------------------------------------------------------------------
 */
ini_set('session.gc_maxlifetime', 864000);

ini_set('max_execution_time', 300);

ini_set('memory_limit', '1024M');

/* ----------------------------------------------------------------------------
 * Application configuration
 * ----------------------------------------------------------------------------
 */
define('APP_NAME', 'Nortfolio');

define("APP_PASSWORD_HASH", 'n0rtf0l1o');

/* ----------------------------------------------------------------------------
 * Internationalization
 * ----------------------------------------------------------------------------
 */
define('APP_I18N_ISO2', 'en');

define('APP_I18N_ISO3', 'eng');

define('APP_I18N_ID', 44);

/* ----------------------------------------------------------------------------
 * Paths
 * ----------------------------------------------------------------------------
 */
define('APP_BASEPATH', realpath(__DIR__) . DIRECTORY_SEPARATOR);

define('APP_BASEDIR', basename(APP_BASEPATH) . DIRECTORY_SEPARATOR);

define('APP_BASEPATH_APP', APP_BASEPATH . 'app' . DIRECTORY_SEPARATOR);

define('APP_BASEPATH_ASSETS', APP_BASEPATH . 'assets' . DIRECTORY_SEPARATOR);

define('APP_BASEPATH_LIB', APP_BASEPATH . 'lib' . DIRECTORY_SEPARATOR);

define('APP_BASEPATH_CDN', APP_BASEPATH . 'cdn' . DIRECTORY_SEPARATOR);

/* ----------------------------------------------------------------------------
 * Router
 * ----------------------------------------------------------------------------
 */
define('APP_DEFAULT_ROUTE', 'Website');

define('APP_DEFAULT_CONTROLLER', 'Dashboard');

define('APP_DEFAULT_METHOD', 'index');

/* ----------------------------------------------------------------------------
 * Error display
 * ----------------------------------------------------------------------------
 */
define('APP_DEBUG', true);

define('APP_DEBUG_INFO', true);
