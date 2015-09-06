<?php
/**
 * Class TXAutoload
 */
include 'data/TXMemcache.php';

class TXAutoload
{
    private static $loaders;
    private static $memKey;
    private static $isReload = false;
    /**
     * Autoload init
     */
    public static function init()
    {
        self::$memKey = TXConfig::getConfig('autoMemKey');
        self::$loaders = TXMemcache::instance()->get(self::$memKey);
        if (!self::$loaders){
            self::$isReload = true;
            self::loading();
        }

        if (false === spl_autoload_register(array('TXAutoload', 'load'))) {
            throw new TXException(1004);
        }
    }

    /**
     * 加载
     */
    private static function loading()
    {
        self::$loaders = array();
        self::getLoads(__DIR__);
        self::getLoads(TXApp::$app_root. DS . "controller");
        self::getLoads(TXApp::$app_root. DS . "service");
        self::getLoads(TXApp::$app_root. DS . "dao");
        self::getLoads(TXApp::$app_root. DS . "model");
        TXMemcache::instance()->set(self::$memKey, self::$loaders);
    }

    /**
     * 获取所有类文件
     * @param $path
     * @return array
     */
    private static function getLoads($path)
    {
        foreach (glob($path . '/*') as $file) {
            if (is_dir($file)) {
                self::getLoads($file);
            } else {
                $name = explode(DS, $file);
                $class = str_replace('.php', '', end($name));
                self::$loaders[$class] = $file;
            }
        }
    }

    /**
     * AutoLoad
     * @param $class
     * @throws TXException
     */
    public static function load($class)
    {
        if ((!isset(self::$loaders[$class]) || !is_readable(self::$loaders[$class])) && !self::$isReload){
            self::$loaders = array();
            self::loading();
        }

        if (isset(self::$loaders[$class])) {
            $path = self::$loaders[$class];
            if (is_readable($path)) {
                include $path;
            } else {
                throw new TXException(1002, array($class));
            }
        } else if (substr($class, -6) == 'Action' || substr($class, -4) == 'Ajax') {
            throw new TXException(1002, array($class));
        }
    }
}