<?php
/**
 * Redis class
 * @method bool delete($key)
 * @method bool hdel($key, $hash)
 * @method int incr($key)
 * @method int incrBy($key, $value)
 * @method bool expire($key, $value)
 */
class TXRedis
{
    /**
     * @var Redis
     */
    private $handler;

    private static $_instance = null;

    public static function instance()
    {
        if (null === self::$_instance){
            $config = TXConfig::getAppConfig('redis', 'dns');
            self::$_instance = new self($config);
        }
        return self::$_instance;
    }

    /**
     * @param $config
     * @throws TXException
     */
    private function __construct($config)
    {
        $this->handler = new Redis();
        if ($config['keep-alive']){
            $fd = $this->handler->pconnect($config['host'], $config['port'], TXConst::minute);
        } else {
            $fd = $this->handler->connect($config['host'], $config['port']);
        }
        if (!$fd){
            throw new TXException(4005, array($config['host'], $config['port']));
        }
    }

    public function get($key, $serialize=true)
    {
        return $serialize ? unserialize($this->handler->get($key)) : $this->handler->get($key);
    }

    public function set($key, $value, $timeout=0, $serialize=true)
    {
        $value = $serialize ? serialize($value) : $value;
        return $this->handler->set($key, $value, $timeout);
    }

    public function hget($key, $hash, $serialize=true)
    {
        return $serialize ? unserialize($this->handler->hget($key, $hash)) : $this->handler->hget($key, $hash);
    }

    public function hset($key, $hash, $value, $serialize=true)
    {
        $value = $serialize ? serialize($value) : $value;
        return $this->handler->hset($key, $hash, $value);
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