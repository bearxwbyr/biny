<?php
/**
 * Class TXAutoload
 */

class TXAutoload
{
    private static $loaders;
    private static $autoPath;
    private static $isReload = false;
    /**
     * Autoload init
     */
    public static function init()
    {
        self::$autoPath = TXApp::$base_root.DS.TXConfig::getConfig('autoPath');
        if (is_readable(self::$autoPath)){
            self::$loaders = require(self::$autoPath);
        } else {
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
        $lastTime = filemtime(self::$autoPath);
        // 5秒缓存不更新
        if (!$lastTime || time()-$lastTime > TXConfig::getConfig('autoSkipLoad')){
            self::$loaders = array();
            self::getLoads(__DIR__);
            self::getLoads(TXApp::$app_root. DS . "controller");
            self::getLoads(TXApp::$app_root. DS . "shell");
            self::getLoads(TXApp::$app_root. DS . "service");
            self::getLoads(TXApp::$app_root. DS . "dao");
            self::getLoads(TXApp::$app_root. DS . "form");
            self::getLoads(TXApp::$app_root. DS . "event");
            self::getLoads(TXApp::$app_root. DS . "model");
            //写入文件
            if (is_writeable(self::$autoPath)) {
                file_put_contents(self::$autoPath, "<?php\nreturn " . var_export(self::$loaders, true) . ';');
            } else {
                throw new TXException(1005, array(self::$autoPath));
            }
        }
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
            self::loading();
        }

        if (isset(self::$loaders[$class])) {
            $path = self::$loaders[$class];
            if (is_readable($path)) {
                include $path;
            } else {
                throw new TXException(1003, array($class));
            }
        } else if (substr($class, -6) == 'Action') {
            throw new TXException(1003, array($class));
        }
    }
}