<?php

namespace Lib\Helper;

/**
 * Password
 *
 * Password utilities helper.
 */
class Password
{
    /**
     * Validate
     *
     * Validate if supplied and stored passwords match.
     *
     * @throws \Exception
     */
    public static function validate( string $password = '', string $storedPassword = '' , string $storedSalt = '' ): bool
    {
        if (!$password) return false;

        $argumentsCount = func_num_args();
        $argumentsList = func_get_args();

        $missingParametersError = 'The class "' . __CLASS__ . '" expects a value for the argument #';

        for ($i = 0; $i < $argumentsCount; $i++) {
            if (empty($argumentsList[$i])) {
                throw new \Exception($missingParametersError . $i);
            }
        }

        $sha1 = self::_encrypt($password, $storedSalt);

        return ($storedPassword === $sha1);
    }

    /**
     * Generate
     *
     * Generates a new password set composed by an encrypted string and a random SALT.
     *
     * @throws \Exception
     */
    public static function generate(string $password = ''): array
    {
        if (empty($password)) {
            throw new \Exception('The class "' . __CLASS__ . '" expects a value for the argument #1');
        }

        $salt = self::_salt();

        $sha1 = self::_encrypt($password, $salt);

        return [
            "password" => $sha1,
            "salt" => $salt
        ];
    }

    /**
     * Salt
     *
     * Generates a random string to use on the password set generator,
     * as well as on the password verification.
     */
    private static function _salt(): string
    {
        return sha1(mt_rand() . time() . session_id() . self::_rand(10));
    }

    /**
     * Random
     *
     * Generates a random string with the specified number of chars.
     */
    private static function _rand(int $size = 0): string
    {
        return substr(str_shuffle(MD5(microtime())), 0, $size);
    }

    /**
     * Encrypt
     *
     * Encrypts the received script with a different
     * approach depending on the first character received.
     */
    private static function _encrypt(string $password = '', string $salt = ''): string
    {
        if (is_numeric($password[0])) {

            $str = $password . $salt;

            $sha1 = is_numeric($password[strlen($password)-1]) ? sha1($str) . $salt : $salt . sha1($str);

        } else {

            $str = $salt . $password;

            $sha1 = is_numeric($password[strlen($password)-1]) ? $salt . sha1($str) : sha1($str) . $salt;
        }

        return $sha1;
    }

}
