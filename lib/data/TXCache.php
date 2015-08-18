<?php
/**
 * Cache
 */
class TXCache {
    /**
     * @var TXCache
     */
    private static $instance = array();

    /**
     * 获取全局临时缓存
     * @param $key
     * @param null $default
     * @return null
     */
    public static function getCache($key, $default=null)
    {
        return isset(self::$instance[$key]) ? self::$instance[$key] : $default;
    }

    /**
     * 设置全局临时缓存
     * @param $key
     * @param $value
     */
    public static function setCache($key, $value)
    {
        self::$instance[$key] = $value;
    }

    /**
     * 删除全局临时缓存
     * @param $key
     */
    public static function delCache($key)
    {
        unset(self::$instance[$key]);
    }
}