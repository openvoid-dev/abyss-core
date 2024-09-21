<?php

namespace Abyss\Horizon;

class Session
{
    public static function has(string $key) : bool
    {
        return (bool) static::get($key);
    }

    public static function put($key, $value) : void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null) : mixed
    {
        return $_SESSION['_flash'][$key] ?? $_SESSION[$key] ?? $default;
    }

    public static function flash(string $key, $value)
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function unflash()
    {
        unset($_SESSION['_flash']);
    }

    public static function flush()
    {
        $_SESSION = [];
    }

    public static function destroy()
    {
        static::flush();

        session_destroy();

        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
}
