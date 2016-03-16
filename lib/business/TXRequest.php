<?php
class TXRequest {
    private $module;
    private $method=null;
    public $isAjax=false;
    private $id;
    private $csrfToken = null;

    /**
     * @var null|TXRequest
     */
    private static $_instance = null;

    /**
     * @param null $key
     * @return mixed
     */
    public function getCookie($key=null)
    {
        if ($key){
            return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
        } else {
            return $_COOKIE;
        }
    }

    /**
     * 设置cookie
     * @param $key
     * @param $value
     * @param $expire
     * @param string $path
     */
    public function setCookie($key, $value, $expire=86400, $path='/')
    {
        setcookie($key, $value, time()+$expire, $path);
    }

    /**
     * 获取对应csrfToken
     * @return null|string
     */
    public function createCsrfToken()
    {
        if (!$this->csrfToken && !$this->isAjax){
            $trueToken = $this->generateCsrf();
            $this->csrfToken = md5($trueToken);
            $trueKey = TXConfig::getConfig('trueToken');
            $csrfKey = TXConfig::getConfig('csrfToken');
            setcookie($trueKey, $trueToken, null, '/');
            setcookie($csrfKey, $this->csrfToken, null, '/');
        }
        return $this->csrfToken;
    }

    /**
     * 获取csrf
     * @return null
     */
    public function getCsrfToken()
    {
        return $this->csrfToken;
    }

    /**
     * 获取随机字符串
     * @param int $len
     * @return string
     */
    private function generateCsrf($len = 16)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < $len; $i++) {
            $code .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $code;
    }

    /**
     * 判断子网掩码是否一致
     * @param $addr
     * @param $cidr
     * @return bool
     */
    private function matchCIDR($addr, $cidr) {
        list($ip, $mask) = explode('/', $cidr);
        return (ip2long($addr) >> (32 - $mask) == ip2long($ip) >> (32 - $mask));
    }

    /**
     * 验证csrfToken
     */
    public function validateCsrfToken()
    {
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        } else {
            $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
        }
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true) && !$this->isAjax) {
            return true;
        }
        $ips = TXConfig::getConfig('csrfWhiteIps');
        foreach ($ips as $ip){
            if ($this->matchCIDR($this->getClientIp(), $ip)){
                return true;
            }
        }
        $trueToken = TXConfig::getConfig('trueToken');
        $csrfPost = TXConfig::getConfig('csrfPost');
        $csrfHeader = 'HTTP_'.str_replace('-', '_', TXConfig::getConfig('csrfHeader'));

        $trueToken = $_COOKIE[$trueToken];
        $token = isset($_POST[$csrfPost]) ? $_POST[$csrfPost] : (isset($_SERVER[$csrfHeader]) ? $_SERVER[$csrfHeader] : null);

        return md5($trueToken) === $token;

    }

     /**
     * 单例模式
     * @param $module
     * @param $params
     * @param bool $isAjax
     * @param null $method
     * @return null|TXRequest
     */
    public static function create($module, $params, $isAjax=false, $method=null)
    {
        if (NULL === self::$_instance){
            self::$_instance = new self($module, $params, $isAjax, $method);
        }
        return self::$_instance;
    }

    /**
     * @return null|TXRequest
     */
    public static function getInstance()
    {
        return self::$_instance;
    }

    private function __construct($module, $isAjax=false, $method=null)
    {
        $this->id = crc32(microtime(true));
        $this->module = $module;
        $this->isAjax = $isAjax;
        $this->method = $method ?: 'index';
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getMethod()
    {
        return ($this->isAjax ? 'ajax' : 'action') . '_' . $this->method;
    }

    /**
     * 是否异步请求
     * @return bool
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * 获取用户IP
     * @return mixed
     */
    public function getClientIp()
    {
        return getenv('HTTP_CLIENT_IP') ?: getenv('HTTP_X_FORWARDED_FOR') ?: getenv('REMOTE_ADDR') ?: $_SERVER['REMOTE_ADDR'];
    }
}