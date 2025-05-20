<?php

/* --------------------------------------------------------------------------
 * URL configuration
 * --------------------------------------------------------------------------
 */

/*
 * Protocol (http or https)
 */
if (!defined('APP_URL_PROTOCOL')) {

    $isSecure = false;

    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {

        $isSecure = true;

    } else {

        $proto = (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https');

        $ssl = (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on');

        if ($proto || $ssl) $isSecure = true;
    }

    $requestProtocol = $isSecure ? 'https' : 'http';

    define('APP_URL_PROTOCOL', $requestProtocol);
}

/*
 * Domain
 */
if (!defined('APP_URL_DOMAIN')) {

    $domain = '';

    if (isset($_SERVER) && is_array($_SERVER)) {

        if (!empty($_SERVER['HTTP_HOST'])) {

            $domain = $_SERVER['HTTP_HOST'];
        }
    }

    define('APP_URL_DOMAIN', $domain);
}

/*
 * Port
 */
if (!defined('APP_URL_PORT')) {

    $port = $_SERVER['SERVER_PORT'];

    $serverPort = (in_array($port, ['80', '443'])) ? '' : (int) $port;

    define('APP_URL_PORT', $serverPort);
}

/*
 * Application URL
 */
if (!defined('APP_URL') && defined('APP_BASEDIR')) {

    if (defined('APP_URL_PROTOCOL') && defined('APP_URL_PORT') && defined('APP_URL_DOMAIN')) {

        $appUrlSuffix = APP_BASEDIR && APP_BASEDIR !== 'public_html/' ? APP_BASEDIR : '';

        // Make sure that the detected PORT isn't already in the detected DOMAIN
        $appDomain = str_replace(APP_URL_PORT, '', APP_URL_DOMAIN);

        // Set the base URL
        define('APP_URL', APP_URL_PROTOCOL . '://' . $appDomain . APP_URL_PORT . '/' . $appUrlSuffix);
    }
}

/*
 * Cdn URL
 */
if (!defined('APP_URL_CDN') && defined('APP_URL_PROTOCOL') && defined('APP_URL_DOMAIN')) {

    define('APP_URL_CDN', APP_URL . 'cdn/');
}

/*
 * Assets URL
 */
if (!defined('APP_URL_ASSETS') && defined('APP_URL_PROTOCOL') && defined('APP_URL_DOMAIN')) {

    define('APP_URL_ASSETS', APP_URL . 'assets/');
}
