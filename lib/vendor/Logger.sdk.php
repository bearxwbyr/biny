<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 16-6-13
 * Time: 下午5:14
 */

namespace Biny;

class Logger
{
    private static $_key = '_log_uin_';
    private static $_uin;
    private static $_instance = null;

    private function __construct()
    {
        self::setUin();
    }

    public static function debug($data, $trace=array())
    {
        self::instance()->logger($data, 'debug', $trace);
    }

    public static function error($data, $trace=array())
    {
        self::instance()->logger($data, 'error', $trace);
    }

    public static function warning($data, $trace=array())
    {
        self::instance()->logger($data, 'warning', $trace);
    }

    public static function info($data, $trace=array())
    {
        self::instance()->logger($data, 'info', $trace);
    }

    /**
     * 获取调用
     * @param int $level
     * @return array
     */
    private static function getTrace($level=2)
    {
        $trace = debug_backtrace();
        for ($i=0; $i<$level; $i++){
            array_shift($trace);
        }
        $traceInfo = array();
        $i = 0;
        foreach ($trace as $t){
            $class = isset($t['class'])? $t['class'].$t['type'].$t['function'] : $t['function'];
            $file = isset($t['file']) ? $t['file'].'('.$t['line'].')' : '{main}';
            $args = array();
            foreach ($t['args'] as $a){
                if (is_string($a)){
                    $args[] = "'$a'";
                } elseif (is_array($a)){
                    $args[] = "Array";
                } else {
                    $name = get_class($a);
                    $args[] = "Object($name)";
                }
            }
            $traceInfo[] = sprintf('#%d %s: %s(%s)', ++$i,  $file, $class, join(', ',$args));
        }
        $file = explode('/', $trace[0]['file']);
        $trace = array('file'=>end($file).":".$trace[0]['line'], 'trace'=>$traceInfo);
        return $trace;
    }

    private static function instance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function logger($data, $level="debug", $trace=array())
    {
        if (isset($trace['trace']) && is_string($trace['trace'])){
            $trace['trace'] = explode("\n", $trace['trace']);
        }
        $trace = $trace ?: self::getTrace();
        $trace['time'] = $this->udate('H:i:s.u');
        $data = array('data'=>$data, 'trace'=>$trace);
        $data['level'] = $level;
        $this->send('http://logger.oa.com:8125/', array('uin'=>self::$_uin, 'data'=>json_encode($data)));
    }

    private function send($url, $data=array(), $timeout=0)
    {
        if(is_string($data)) {
            $real_url = $url. (strpos($url, '?') === false ? '?' : ''). $data;
        } else {
            $real_url = $url. (strpos($url, '?') === false ? '?' : ''). http_build_query($data);
        }
        $ch = curl_init($real_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($timeout){
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        }
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }

    private function udate($format = 'u', $utimestamp = null) {
        if (is_null($utimestamp))
            $utimestamp = microtime(true);

        $timestamp = floor($utimestamp);
        $milliseconds = str_pad(round(($utimestamp - $timestamp) * 1000000), 6, '0', STR_PAD_LEFT);

        return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
    }

    public static function setUin($uin=null)
    {
        if ($uin !== NULL){
            setcookie(self::$_key, $uin, '/');
            self::$_uin = $uin;
        } elseif (self::$_uin !== NULL){
            return self::$_uin;
        } elseif (isset($_GET[self::$_key]) && $_GET[self::$_key]){
            setcookie(self::$_key, $_GET[self::$_key], '/');
            self::$_uin = $_GET[self::$_key];
        } elseif (isset($_COOKIE[self::$_key]) && $_COOKIE[self::$_key]){
            self::$_uin = $_COOKIE[self::$_key];
        } else {
            self::$_uin = self::generateStr();
            setcookie(self::$_key, self::$_uin, '/');
        }
        return self::$_uin;
    }

    /**
     * 获取随机字符串
     * @param int $len
     * @return string
     */
    private static function generateStr($len = 16)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < $len; $i++) {
            $code .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $code;
    }
}


