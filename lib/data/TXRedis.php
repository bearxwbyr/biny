<?php
/**
 * Redis class
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
     * @param array $config
     */
    private function __construct($config)
    {
        $this->handler = new Redis();
        $this->handler->connect($config['host'], $config['port']);
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