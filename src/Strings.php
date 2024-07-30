<?php

namespace Siarko\Utils;

class Strings
{

    public const URL_SEPARATOR = '/';

    /**
     * @param string $url
     * @return string
     */
    public static function urlEncode(string $url): string
    {
        return urlencode($url);
    }

    /**
     * @param string $url
     * @return string
     */
    public static function urlDecode(string $url): string
    {
        return urldecode($url);
    }

    /**
     * @param array $path
     * @return string
     */
    public static function createUrl(array $path): string
    {
        $path = array_merge(
            ...array_map(fn($p) => explode(self::URL_SEPARATOR, trim($p, self::URL_SEPARATOR)), $path)
        );
        $path = array_map(fn($p) => self::urlEncode($p), $path);
        return implode(self::URL_SEPARATOR, $path);
    }

    /**
     * @param array $parts
     * @return string
     */
    public static function createPath(array $parts): string
    {
        $parts = array_map(fn($p) => trim($p, self::URL_SEPARATOR.DIRECTORY_SEPARATOR), $parts);
        return implode(DIRECTORY_SEPARATOR, $parts);
    }

    /**
     * @param string $path
     * @return string
     */
    public static function pathToUrl(string $path): string
    {
        return str_replace(DIRECTORY_SEPARATOR, self::URL_SEPARATOR, $path);
    }

    /**
     * @param string $suffix
     * @return string
     */
    public static function urlToPath(string $suffix): string
    {
        return str_replace(self::URL_SEPARATOR, DIRECTORY_SEPARATOR, $suffix);
    }

    /**
     * @param string $string
     * @return string
     */
    public static function camelCaseToSnakeCase(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    /**
     * @param string $string
     * @param string $separator
     * @param bool $capitalizeFirst
     * @return string
     */
    public static function snakeCaseToCamelCase(string $string, bool $capitalizeFirst = false, string $separator = '_'): string
    {
        $str = preg_replace("/".$separator."(.{1})/", '$1', ucwords($string, $separator));
        if (!$capitalizeFirst) {
            $str = lcfirst($str);
        }

        return $str;
    }

    /**
     * @param int $length
     * @return string
     */
    public static function generateRandomString(int $length = 10): string
    {
        return substr(
            str_shuffle(
                str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )
            ),1,$length
        );
    }
}