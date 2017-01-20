<?php

# 基本加载
include __DIR__.'/TXAutoload.php';
include __DIR__.'/config/TXConfig.php';
include __DIR__.'/business/TXEvent.php';
include __DIR__.'/logger/TXLogger.php';
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
     * Template根路径
     * @var string /app/template/
     */
    public static $view_root;

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
        self::define();
        self::$base = new self();
        self::$base_root = dirname(__DIR__);
        self::$plugins_root = self::$base_root.DS."plugins";
        self::$log_root = self::$base_root.DS."logs";
        if (RUN_SHELL){
            self::$log_root .= '/shell';
        }

        if (is_readable($apppath)) {
            self::$app_root = $apppath;
        } else {
            throw new TXException(1001, array($apppath));
        }
        self::$view_root = self::$app_root.DS."template";
        if (!is_writable(self::$log_root) && !mkdir(self::$log_root)){
            throw new TXException(1007, array(self::$log_root));
        }

        self::init();
    }

    /**
     * 初始化定义
     */
    private static function define()
    {
        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
        //定义保护
        defined('RUN_SHELL') or define('RUN_SHELL', false);
        defined('SYS_DEBUG') or define('SYS_DEBUG', false);
        defined('SYS_CONSOLE') or define('SYS_CONSOLE', false);
        defined('isMaintenance') or define('isMaintenance', false);

        defined('ENV_DEV') or define('ENV_DEV', SYS_ENV === 'dev');
        defined('ENV_PRE') or define('ENV_PRE', SYS_ENV === 'pre');
        defined('ENV_PUB') or define('ENV_PUB', SYS_ENV === 'pub');

        defined('ERROR') or define('ERROR', 1);
        defined('WARNING') or define('WARNING', 2);
        defined('NOTICE') or define('NOTICE', 8);
        defined('DEBUG') or define('DEBUG', 9);
        defined('INFO') or define('INFO', 10);

        //TXEvent 默认事件
        defined('beforeAction') or define('beforeAction', 1);
        defined('afterAction') or define('afterAction', 2);
        defined('onException') or define('onException', 3);
        defined('onError') or define('onError', 4);
        defined('onRequest') or define('onRequest', 5);
        defined('onSql') or define('onSql', 'onSql');
    }

    /**
     * 异常捕获类
     * @param $code
     * @param $message
     * @param $file
     * @param $line
     * @throws TXException
     */
    public static function handleError($code, $message, $file, $line)
    {
        if ($code === E_WARNING || $code === E_NOTICE){
            $message = sprintf("%s\n#1 %s(%s)", $message, $file, $line);
            TXLogger::addError($message, $code);
        } elseif (error_reporting() & $code) {
            throw new TXException(1000, $message);
        }
        return;
    }

    /**
     * 核心初始化
     */
    private static function init()
    {
        TXAutoload::init();
        set_error_handler(['TXApp', 'handleError']);
        TXDefine::init();
        TXEvent::init();
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
     * shell 执行
     */
    public static function shell()
    {
        self::$controller->shellStart();
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