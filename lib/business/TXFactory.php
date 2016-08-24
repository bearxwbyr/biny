<?php
/**
 * object factory
 */
class TXFactory {
    /**
     * 对象列表
     *
     * @var array
     */
    private static $objects = array();

    /**
     * dynamic create object
     * @param string $class
     * @param string $alias
     * @return baseDAO
     */
    public static function create($class, $alias=null)
    {
        if (null === $alias) {
            $alias = $class;
        }
        if (!isset(self::$objects[$alias])) {
            //可以不写DAO文件自动建立对象
            if (substr($alias, -3) == 'DAO') {
                $dbConfig = TXConfig::getConfig('dbConfig', 'database');
                if (isset($dbConfig[$class])){
                    $dao = new TXSingleDAO($dbConfig[$class], $class);
                    self::$objects[$alias] = $dao;
                } else {
                    self::$objects[$alias] = new $class();
                }
            } else {
                self::$objects[$alias] = new $class();
            }
        }

        return self::$objects[$alias];
    }
}