<?php
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

# 基本加载
include 'TXAutoload.php';
include 'config/TXConfig.php';
include 'config/TXDefine.php';
include 'exception/TXException.php';

/**
 * Framework App核心
 */
class TXApp
{
    /**
     * 项目根路径
     * @var string
     */
    public static $base_root;

    /**
     * App根路径
     * @var string
     */
    public static $app_root;

    /**
     * 插件路径
     * @var string
     */
    public static $plugins_root;

    /**
     * @var TXController
     */
    private static $controller;

    /**
     * App注册运行
     * @param $apppath
     * @throws TXException
     */
    public static function registry($apppath)
    {
        self::$base_root = dirname(__DIR__);
        self::$plugins_root = self::$base_root.DS."plugins";

        if (is_readable($apppath)) {
            self::$app_root = $apppath;
        } else {
            throw new TXException(1000, array($apppath));
        }

        self::init();
    }

    /**
     * 核心初始化
     */
    private static function init()
    {
        TXDefine::init();
        TXAutoload::init();
        self::$controller = TXFactory::create('TXController');
    }

    /**
     * application to run
     */
    public static function run()
    {
        self::$controller->dispatcher();
    }

}