<?php

/* ----------------------------------------------------------------------------
 * Session Name
 * 
 * Must be set at all times to prevent collision with other projects on
 * this domain or administration areas.
 * ----------------------------------------------------------------------------
 */
if (!defined('APP_SESSION_NAME')) {

    $sessionName = str_replace([':', '.'], ['_', '_'], APP_URL_DOMAIN);

    $sessionName .= '_mvc';

    define('APP_SESSION_NAME', $sessionName);
}

/* ----------------------------------------------------------------------------
 * Init session
 *
 * Initializes the PHP session if not already done.
 * ----------------------------------------------------------------------------
 */
if (!isset($_SESSION)) {

    session_name(APP_SESSION_NAME);
    
    session_start();
}
