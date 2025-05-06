<?php

namespace Lib\Helper;

/**
 * Text
 *
 * String utilities that handle common tasks.
 */
class Text
{
    /**
     * Slug
     *
     * Converts the given string into a URL slug.
     */
    public static function slug(string $str = '', string $char = '-', ?string $keep = null): string
    {
        $str = preg_replace('/[áàãâä]/ui', 'a', $str);
        $str = preg_replace('/[éèêë]/ui', 'e', $str);
        $str = preg_replace('/[íìîï]/ui', 'i', $str);
        $str = preg_replace('/[óòõôö]/ui', 'o', $str);
        $str = preg_replace('/[úùûü]/ui', 'u', $str);
        $str = preg_replace('/ç/ui', 'c', $str);
        $str = preg_replace('/[,();:|!"#$%&?~^><ªº\-]/', '_', $str);

        $keep = !empty($keep) ? $keep : '';
        $str = preg_replace('/[^a-z0-9' . $keep . ']/i', '_', $str);
        $str = preg_replace('/_+/', '_', $str);

        $str = trim(strtolower($str), "_");

        $char = empty($char) ? '-' : $char;

        return str_replace('_', $char, $str);
    }

    /**
     * Truncate
     *
     * Truncates a string to the specified length without breaking words.
     */
    public static function truncate(string $str = '', int $maxLen = 0, bool $dotted = true, string $splitChar = ' '): string
    {
        // No work required, returns the received value enforcing a string
        if (strlen($str) <= $maxLen) {
            return $str;
        }

        // Cut the string
        $newStr = substr($str, 0, $maxLen);

        // Check if a word was cut, thus stripping the remaining chars
        if (strrpos($newStr, $splitChar) > 0 && substr($newStr, -1, 1) != $splitChar) {

            $newStr = substr($newStr, 0, strrpos($newStr, $splitChar));
        }

        // Add dots to the end
        if ($dotted) {
            $newStr = $newStr . '...';
        }

        return $newStr;
    }

    /**
     * Random
     *
     * Generates a random string with the received number of characters.
     */
    public static function random(int $numChars = 10): string
    {
        $allowedChars = '0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTVWXYZ';

        return substr(str_shuffle($allowedChars), 0, $numChars);
    }
}
