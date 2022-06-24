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

 
        public static function admin()
        {
            return self::table('admin');
        }
 
        public static function author()
        {
            return self::table('author');
        }
 
        public static function authorContent()
        {
            return self::table('author_content');
        }
 
        public static function authorTemp()
        {
            return self::table('author_temp');
        }
 
        public static function company()
        {
            return self::table('company');
        }
 
        public static function companyKeyword()
        {
            return self::table('company_keyword');
        }
 
        public static function companyKeywordHistory()
        {
            return self::table('company_keyword_history');
        }
 
        public static function enumerate()
        {
            return self::table('enumerate');
        }
 
        public static function event()
        {
            return self::table('event');
        }
 
        public static function eventExtend()
        {
            return self::table('event_extend');
        }
 
        public static function fileUpload()
        {
            return self::table('file_upload');
        }
 
        public static function information()
        {
            return self::table('information');
        }
 
        public static function log()
        {
            return self::table('log');
        }
 
        public static function media()
        {
            return self::table('media');
        }
 
        public static function report()
        {
            return self::table('report');
        }
 
        public static function reportExtend()
        {
            return self::table('report_extend');
        }
 
        public static function uploadLog()
        {
            return self::table('upload_log');
        }
 
        public static function user()
        {
            return self::table('user');
        }
}