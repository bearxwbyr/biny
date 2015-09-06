<?php
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
        if (!$this->handler->addserver($config['host'], $config['port'])){
            throw new TXException(1011, array($config['host'], $config['port']));
        }
    }

    public function set($key, $value, $expire=0)
    {
        return $this->handler->set($key, $value, MEMCACHE_COMPRESSED, $expire);
    }

    public function get($key)
    {
        return $this->handler->get($key);
    }

    public function delete($key)
    {
        return $this->handler->delete($key);
    }
}