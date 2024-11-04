<?php

namespace Siarko\Utils\Persistance\Cookie;

class CookieManager
{

    public function get(string $name, $default = null)
    {
        return $_COOKIE[$name] ?? $default;
    }

    public function set(string $name, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false){
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }
}