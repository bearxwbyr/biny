<?php
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

# 基本加载
include __DIR__.'/TXAutoload.php';
include __DIR__.'/config/TXConfig.php';
include __DIR__.'/config/TXDefine.php';
include __DIR__.'/exception/TXException.php';

/**
 * Framework App核心
 * @property TXRequest $request
 * @property TXSession $session
 * @property TXRouter $router
 * @property TXCache $cache
 * @property TXRedis $redis
 * @property TXMemcache $memcache
 * @property Person $person
 */
class TXApp
{
    /**
     * @var TXApp
     */
    public static $base;

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
     * 日志路径
     * @var string
     */
    public static $log_root;

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
        self::$base = new self();
        self::$base_root = dirname(__DIR__);
        self::$plugins_root = self::$base_root.DS."plugins";
        self::$log_root = self::$base_root.DS."logs";

        if (is_readable($apppath)) {
            self::$app_root = $apppath;
        } else {
            throw new TXException(1001, array($apppath));
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
        TXEvent::on(onException, ['TXException', 'event']);
        TXEvent::on(beforeAction, ['TXAction', 'beforeAction']);
        self::$controller = TXFactory::create('TXController');
    }

    /**
     * application to run
     */
    public static function run()
    {
        self::$controller->dispatcher();
    }

    /**
     * 获取单例全局量
     * @param $name
     * @return mixed
     * @throws TXException
     */
    public function __get($name)
    {
        switch ($name){
            case 'person':
                return Person::get();
            case 'request':
                return TXRequest::getInstance();
            case 'redis':
                return TXRedis::instance();
            case 'memcache':
                return TXMemcache::instance();
            case 'session':
                return TXSession::instance();
            case 'router':
            case 'cache':
                $module = 'TX'.ucfirst($name);
                return TXFactory::create($module);

            default:
                throw new TXException(1006, $name);
        }
    }

}