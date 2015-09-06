<?php
/**
 * Session service
 * @author billge
 */
class sessionService extends baseService
{
    private static $instance = null;

    public function __construct()
    {
        self::initSession();
    }

    /**
     * 初始化Session
     */
    private static function initSession()
    {
        if (null === self::$instance) {
            $memcache = TXConfig::getAppConfig('memcache', 'dns');
            ini_set("session.save_handler", "memcache");
            ini_set("session.save_path", 'tcp://' . $memcache['host'] . ':' . $memcache['port']);
            ini_set("session.gc_maxlifetime", 36000);
            session_start();
            self::$instance = true;
        }
    }

    //解决session死锁问题
    public function closeSession(){
        session_write_close();
        self::$instance = null;
    }

    public function setLastURL($url)
    {
        self::initSession();
        $_SESSION['glasturl'] = $url;
    }

    public function getLastURL()
    {
        self::initSession();
        return isset($_SESSION['glasturl']) ? $_SESSION['glasturl'] : false;
    }

    public function clearLastURL()
    {
        self::initSession();
        unset($_SESSION['glasturl']);
    }
}