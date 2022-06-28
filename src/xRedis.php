<?php

namespace eBaocd\Common;

use Predis\Client;

class xRedis
{
    protected static $_instance = NULL;
    protected $redis = NULL;

    private function __construct()
    {
        global $APP_G;

        $redis = $APP_G['redis'];
        $type  = strtolower($redis['type']);

        if ($type == 'single')
        {
            $this->redis = new Client($redis[$type]);//单台redis模式
        }
        else
        {
            $this->redis = new Client($redis[$type]['server'], $redis[$type]['option']);//redis集群模式
        }
    }

    /**
     * 单例模式
     * @return bool|xRedis|null
     */
    public static function init()
    {
        if (!(self::$_instance instanceof self))
        {
            self::$_instance = new self();
        }

        return self::$_instance->redis ? self::$_instance->redis : FALSE;
    }

    public function __destruct()
    {
        if (null !== self::$_instance)
        {
            self::$_instance->redis->disconnect();
        }
    }

    /**
     * 设置
     * @param string $key
     * @param string $value
     * @param int $expire
     */
    public static function set(string $key, string $value, int $expire = 0)
    {
        if ($expire > 0)
        {
            self::init()->setex(self::keyInit($key), $expire, $value);
        }
        else
        {
            self::init()->set(self::keyInit($key), $value);
        }
    }

    /**
     * 获取
     * @param string $key
     * @return mixed
     */
    public static function get(string $key)
    {
        return self::init()->get(self::keyInit($key));
    }

    /**
     * 删除
     * @param array $keys
     */
    public static function del(array $keys)
    {
        self::init()->del(self::keyInit($keys));
    }

    /**
     * 键处理
     * @param $keys
     * @return mixed|string
     */
    protected static function keyInit($keys)
    {
        if (is_string($keys))
        {
            $keys   = SERVER_ENV . ':' . $keys;
            $prefix = xFun::getGlobalConfig('project');
            if (!empty($prefix)) $keys = $prefix . ':' . $keys;
        }
        elseif (is_array($keys))
        {
            foreach ($keys as &$key)
            {
                $key    = SERVER_ENV . ':' . $key;
                $prefix = xFun::getGlobalConfig('project');
                if (!empty($prefix)) $key = $prefix . ':' . $key;
            }
        }
        return $keys;
    }
}