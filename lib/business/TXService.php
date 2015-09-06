<?php
/**
 * 业务基类
 */
class TXService {

    private static $_cache = [];

    /**
     * 获取Service
     * @param $obj
     * @return TXSingleDAO
     */
    public function __get($obj)
    {
        if (substr($obj, -3) == 'DAO') {
            return TXDAO::getDAO($obj);
        }
    }

    /**
     * 获取Service类
     * @param $obj
     * @return TXService
     */
    public static function getService($obj)
    {
        if (!isset(self::$_cache[$obj])){
            self::$_cache[$obj] = new $obj();
        }
        return self::$_cache[$obj];
    }
}