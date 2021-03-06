<?php
namespace eBaocd\Common;

class xDb
{
    private static $_instance = NULL;
    private static $create = FALSE;

    //设为私有，防止被外部调用
    private function __construct()
    {

    }

    //防止克隆
    private function __clone()
    {

    }

    /**
     * 单例模式
     * @return bool|connect|null
     */
    private static function getInstance($table)
    {
        if (!self::$_instance instanceof self)
        {
            self::$_instance = new \Apps\Model\PurposeModel();
            self::$_instance->setTable($table);
        }

        self::$_instance->setDefault();//每次调用前还原默认参数

        return self::$_instance;
    }

    public static function __callStatic($name, $arguments)
    {
        return self::table($name);
    }

    public static function table($table)
    {
        return self::getInstance($table);
    }
}