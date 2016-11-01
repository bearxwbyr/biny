<?php
/**
 * @method bool delete($key)
 * @method mixed get($key)
 */
class TXMemcache
{
    /**
     * @var TXMemcache
     */
    private static $instance = null;

    public static function instance()
    {
        if (null === self::$instance) {
            $memcacheCfg = TXConfig::getAppConfig('memcache', 'dns');

            self::$instance = new self($memcacheCfg);
        }

        return self::$instance;
    }


    /**
     * @var Memcache
     */
    private $handler;

    public function __construct($config)
    {
        $this->handler = new Memcache();
        if (isset($config['keep-alive']) && $config['keep-alive']){
            $fd = $this->handler->pconnect($config['host'], $config['port'], TXConst::minute);
        } else {
            $fd = $this->handler->connect($config['host'], $config['port']);
        }
        if (!$fd){
            throw new TXException(4004, array($config['host'], $config['port']));
        }
    }

    public function set($key, $value, $expire=0)
    {
        return $this->handler->set($key, $value, MEMCACHE_COMPRESSED, $expire);
    }

    /**
     * 调用redis
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array(array($this->handler, $method), $arguments);
    }
}