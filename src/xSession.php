<?php

namespace eBaocd\Common;

class xSession
{
    protected static $_init = null;

    public static function Init()
    {
        \session_start();
        self::$_init = true;
    }

    public static function Set($name, $v)
    {
        if (is_null(self::$_init)) self::Init();
        $_SESSION[$name] = $v;
    }

    public static function Get($name, $once = false)
    {
        if (is_null(self::$_init)) self::Init();
        $v = null;
        if (isset($_SESSION[$name]))
        {
            $v = $_SESSION[$name];
            if ($once) unset($_SESSION[$name]);
        }
        return $v;
    }

    public static function Destory()
    {
        if (is_null(self::$_init)) self::Init();
        \session_destroy();
    }
}