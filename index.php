<?php
 
/* ----------------------------------------------------------------------------
 * Application configuration
 * ----------------------------------------------------------------------------
 */
$appConfig = 'config.php';

if (!is_file($appConfig)) die("App configuration not found!");

require_once($appConfig);

/* ----------------------------------------------------------------------------
 * Database configuration
 * ----------------------------------------------------------------------------
 */
$dbConfig = 'connect.php';

if (!is_file($dbConfig)) die("Database configuration not found!");

require_once($dbConfig);

/* ----------------------------------------------------------------------------
 * Application internal configuration
 * ----------------------------------------------------------------------------
 */
$configSettings = APP_BASEPATH_APP . 'Config/Wrapper.php';

if (!is_file($configSettings)) die("Internal configuration not found!");

require_once($configSettings);

/* ----------------------------------------------------------------------------
 * Start output buffer
 * ----------------------------------------------------------------------------
 */
ob_start('minify');

/* ----------------------------------------------------------------------------
 * Application core
 * ----------------------------------------------------------------------------
 */
$appCore = APP_BASEPATH_APP . 'mvc.php';

if (!is_file($appCore)) die("App core not found!");

require_once($appCore);

/* ----------------------------------------------------------------------------
 * Flush output buffer
 * ----------------------------------------------------------------------------
 */
ob_flush();

/* ----------------------------------------------------------------------------
 * Exit application
 * ----------------------------------------------------------------------------
 */
exit();
