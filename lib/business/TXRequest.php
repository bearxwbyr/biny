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
     * 单例模式
     * @param $module
     * @param bool $isAjax
     * @param null $method
     * @return null|TXRequest
     */
    public static function create($module, $isAjax=false, $method=null)
    {
        if (NULL === self::$_instance){
            self::$_instance = new self($module, $isAjax, $method);
        }
        return self::$_instance;
    }

    /**
     * @return null|TXRequest
     */
    public static function getInstance()
    {
        if (NULL === self::$_instance){
            self::$_instance = new self(null);
        }
        return self::$_instance;
    }

    private function __construct($module, $isAjax=false, $method=null)
    {
        $this->id = crc32(microtime(true));
        $this->module = $module;
        $this->isAjax = $isAjax;
        $this->method = $method ?: 'index';
    }

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
            if ($this->matchCIDR($this->getUserIp(), $ip)){
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

    public function getUrl()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Returns the server name.
     * @return string server name
     */
    public function getServerName()
    {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * Returns the server port number.
     * @return integer server port number
     */
    public function getServerPort()
    {
        return (int) $_SERVER['SERVER_PORT'];
    }

    /**
     * Returns the URL referrer, null if not present
     * @return string URL referrer, null if not present
     */
    public function getReferrer()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    }

    /**
     * Returns the user agent, null if not present.
     * @return string user agent, null if not present
     */
    public function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
    }

    /**
     * Returns the user IP address.
     * @return string user IP address. Null is returned if the user IP address cannot be detected.
     */
    public function getUserIP()
    {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
    }

    /**
     * Returns the user host name, null if it cannot be determined.
     * @return string user host name, null if cannot be determined
     */
    public function getUserHost()
    {
        return isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : null;
    }
}