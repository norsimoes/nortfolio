<?php

namespace Lib;

/**
 * Url
 *
 * URL utilities that handle common tasks.
 */
class Url
{
    private static ?Url $_instance = null;

    /**
     * Get instance
     *
     * Returns the class instance while preventing double load.
     */
    public static function getInstance(): Url
    {
        if (self::$_instance === null) {

            self::$_instance = new Url();
        }

        return self::$_instance;
    }

    /**
     * Go to
     *
     * Shortcut function to receive a local path and redirect to it.
     *
     * @throws \Exception
     */
    public static function goTo(string $uri = '', string $method = 'location', int $http_response_code = 302): void
    {
        $url = self::base($uri);

        self::redirect($url, $method, $http_response_code);
    }

    /**
     * Base
     *
     * Returns the base URL with optional URI string.
     */
    public static function base(string $uri = ''): string
    {
        if (defined('APP_URL') && APP_URL !== '') {

            $pageURL = APP_URL;

        } else {

            // Ascertain the protocol
            $pageURL = (@$_SERVER["HTTPS"] == "on") ? 'https://' : 'http://';

            /*
             * Get server name and access port
             */
            if (isset($_SERVER["SERVER_NAME"]) && isset($_SERVER["REQUEST_URI"])) {

                if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != '80') {
                    $pageURL .= $_SERVER["SERVER_NAME"] . ':' . $_SERVER["SERVER_PORT"];
                } else {
                    $pageURL .= $_SERVER["SERVER_NAME"];
                }
            }

            /*
             * Add trailing slash to the base URL if none present
             */
            if (!str_ends_with($pageURL, '/')) {
                $pageURL .= '/';
            }
        }

        if (!empty($uri)) {

            // Remove leading slash from URI
            $uri = ltrim($uri, '/');

            // Append URI to our base URL
            $pageURL .= $uri;

            // Enforce trailing slash
            $pageURL = rtrim($pageURL);
            $pageURL = rtrim($pageURL, '/') . 'Url.php/';
        }

        return $pageURL;
    }

    /**
     * Redirect
     *
     * Redirects the visitor to another URL.
     *
     * @throws \Exception
     */
    public static function redirect(string $url = '', string $method = 'location', int $http_response_code = 302): void
    {
        if (empty($url)) {
            throw new \Exception('You cannot redirect with an empty URL!');
        }

        switch ($method) {

            case 'refresh':
                header("Refresh:0;url=" . $url);
                break;

            default:
                header("Location: " . $url, true, $http_response_code);
                break;
        }

        exit();
    }

}
