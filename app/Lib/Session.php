<?php

namespace Lib;

/**
 * Session
 *
 * Application session handler.
 */
class Session
{
    private static ?Session $_instance = null;

    /**
     * Get instance
     *
     * Returns the class instance while preventing double load.
     */
    public static function getInstance(): Session
    {
        if (self::$_instance === null) {

            self::$_instance = new Session();
        }

        return self::$_instance;
    }

    /**
     * Init time
     *
     * Handle the session time entry.
     */
    public static function initTime(): Session
    {
        if (!isset($_SESSION[APP_SESSION_NAME]['__mvc']['time'])) {

            $_SESSION[APP_SESSION_NAME]['__mvc']['time'] = [
                'created' => time(),
                'modified' => 0,
                'login' => 0
            ];

        } else {

            $_SESSION[APP_SESSION_NAME]['__mvc']['time']['modified'] = time();
        }

        return self::$_instance;
    }

    /**
     * Init i18n
     *
     * Handle the session internationalization entry.
     */
    public static function initI18n(): Session
    {
        if (!isset($_SESSION[APP_SESSION_NAME]['__mvc']['i18n'])) {

            $_SESSION[APP_SESSION_NAME]['__mvc']['i18n'] = [
                'iso2' => APP_I18N_ISO2,
                'iso3' => APP_I18N_ISO3,
                'language_id' => APP_I18N_ID
            ];
        }

        return self::$_instance;
    }

    /**
     * Init message
     *
     * Handle the session message entry.
     */
    public static function initMessage(): Session
    {
        if (!isset($_SESSION[APP_SESSION_NAME]['__mvc']['message'])) {

            $_SESSION[APP_SESSION_NAME]['__mvc']['message'] = [];
        }

        return self::$_instance;
    }

    /**
     * Get id
     *
     * Returns the session id.
     */
    public static function getId(): string
    {
        return session_id();
    }

    /**
     * Get name
     *
     * Returns the session name.
     */
    public static function getName(): string
    {
        return session_name();
    }

    /**
     * Get lifetime
     *
     * Returns the session lifetime.
     */
    public static function getLifetime(): int
    {
        return ini_get('session.gc_maxlifetime');
    }

    /**
     * Get Login
     *
     * Retrieves the user login status.
     *
     * @throws \Exception
     */
    public static function getLogin(): bool
    {
        $loggedUser = self::get('user');

        return (is_object($loggedUser));
    }

    /**
     * Get
     *
     * Retrieve a session entry value.
     *
     * @throws \Exception
     */
    public static function get(string $entry = ''): mixed
    {
        if (empty($entry)) {

            throw new \Exception('You cannot retrieve a value of an empty session entry!');
        }

        if ($entry === "__mvc") {

            throw new \Exception('The session entry "__mvc" is reserved!');
        }

        return $_SESSION[APP_SESSION_NAME][$entry] ?? null;
    }

    /**
     * Set
     *
     * Set a new session entry with received value.
     * Will overwrite previous value, if any.
     *
     * @throws \Exception
     */
    public static function set(string $entry = '', mixed $value = null): void
    {
        if (empty($entry)) {

            throw new \Exception('You cannot set a value to an empty session entry!');
        }

        if ($entry === "__mvc") {

            throw new \Exception('The session entry "__mvc" is reserved!');
        }

        $_SESSION[APP_SESSION_NAME][$entry] = $value;
    }

    /**
     * Set user
     *
     * Set a new session user entry with received value.
     * Will overwrite previous value, if any.
     *
     * @throws \Exception
     */
    public static function setUser(string $entry = '', mixed $value = null): void
    {
        if (empty($entry)) {

            throw new \Exception('You cannot set a value to an empty session entry!');
        }

        if ($entry === "__mvc") {

            throw new \Exception('The session entry "__mvc" is reserved!');
        }

        $_SESSION[APP_SESSION_NAME]['user']->$entry = $value;
    }

    /**
     * Set security key
     *
     * Set the session security key.
     *
     * @throws \Exception
     */
    public static function setSecurityKey(string $key = ''): void
    {
        self::set($key, sha1(session_id() . APP_PASSWORD_HASH));
    }

    /**
     * Get i18n
     *
     * Retrieves the active language value. Defaults to ISO3.
     *
     * @throws \Exception
     */
    public static function getI18n(string $type = 'iso3'): mixed
    {
        $i18n = $_SESSION[APP_SESSION_NAME]['__mvc']['i18n'];

        if (!isset($i18n[$type])) {

            throw new \Exception('No such type on the session i18n specification!');
        }

        return $i18n[$type];
    }

    /**
     * Set i18n
     *
     * Sets the i18n session entry.
     */
    public static function setI18n(string $iso2 = '', string $iso3 = '', int $languageId = 0): void
    {
        $_SESSION[APP_SESSION_NAME]['__mvc']['i18n']['iso2'] = $iso2;

        $_SESSION[APP_SESSION_NAME]['__mvc']['i18n']['iso3'] = $iso3;

        $_SESSION[APP_SESSION_NAME]['__mvc']['i18n']['language_id'] = $languageId;
    }

    /**
     * Get Message
     *
     * Get a session message entry type.
     */
    public static function getMessage(string $type): array
    {
        $msgArr = $_SESSION[APP_SESSION_NAME]['__mvc']['message'];

        return isset($msgArr[$type]) ? (array) $msgArr[$type] : [];
    }

    /**
     * Set message
     *
     * Set a new session message entry in the specified type array.
     */
    public static function setMessage(string $type = '', string $message = ''): void
    {
        $msgArr = $_SESSION[APP_SESSION_NAME]['__mvc']['message'];

        if (!isset($msgArr[$type])) $msgArr[$type] = [];

        $msgArr[$type][] = $message;

        $_SESSION[APP_SESSION_NAME]['__mvc']['message'] = $msgArr;
    }

    /**
     * Clear Message
     *
     * Clear a session stored message type.
     */
    public static function clearMessage(string $type = ''): void
    {
        $msgArr = $_SESSION[APP_SESSION_NAME]['__mvc']['message'];

        if (isset($msgArr[$type])) unset($msgArr[$type]);

        $_SESSION[APP_SESSION_NAME]['__mvc']['message'] = $msgArr;
    }

    /**
     * Destroy
     *
     * Performs all PHP session resets to have the session destroyed.
     */
    public static function destroy(): void
    {
        // Unset all session variables
        $_SESSION = [];

        // Kill the session cookie
        if (ini_get('session.use_cookies')) {

            $arr = session_get_cookie_params();

            setcookie(session_name(), '', time() - 42000, $arr['path'], $arr['domain'], $arr['secure'], $arr['httponly']);
        }

        // Destroy the session
        session_destroy();
    }

    /**
     * Is ajax
     *
     * Ascertain if the HTTP request is ajax.
     */
    public static function isAjax(): bool
    {
        $requestText = (new \Lib\Input())->server('HTTP_X_REQUESTED_WITH');

        return $requestText && strtolower($requestText) === 'xmlhttprequest';
    }

}
