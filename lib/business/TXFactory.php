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
     * @return mixed
     */
    public static function create($class, $alias=null)
    {
        if (null === $alias) {
            $alias = $class;
        }

        if (!isset(self::$objects[$alias])) {
            self::$objects[$alias] = new $class();
        }

        return self::$objects[$alias];
    }
}