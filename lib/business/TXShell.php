<?php
/**
 * Shell class
 */
class TXShell
{
    /**
     * 请求参数
     * @var array
     */
    private $params;

    /**
     * 构造函数
     */
    public function __construct()
    {
        global $argv, $argc;
        $this->params = $argc > 1 ? array_slice($argv, 2) : [];
    }

    /**
     * 获取Service|DAO
     * @param $obj
     * @return TXService | TXDAO
     */
    public function __get($obj)
    {
        if (substr($obj, -7) == 'Service' || substr($obj, -3) == 'DAO') {
            return TXFactory::create($obj);
        }
    }

    /**
     * 获取请求参数
     * @param $key
     * @param null $default
     * @return float|int|mixed|null
     */
    public function getParam($key, $default=null)
    {
        return isset($this->params[$key]) ? $this->params[$key] : $default;
    }

    /**
     * @param string $ret
     * @return mixed
     */
    public function correct($ret='success')
    {
        TXLogger::addLog($ret);
        return $ret;
    }

    /**
     * @param string $msg
     * @return string
     */
    public function error($msg="error")
    {
        TXEvent::trigger(onError, array($msg));
        TXLogger::addError($msg);
        return $msg;
    }
}