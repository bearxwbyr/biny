<?php
class TXRequest {
    private $module;
    private $method=null;
    private $id;
    private $csrfToken = null;
    private $_hostInfo = null;
    private $_securePort = null;
    private $_port = null;
    private $_isSecure = null;

    /**
     * @var null|TXRequest
     */
    private static $_instance = null;

    /**
     * 单例模式
     * @param $module
     * @param null $method
     * @return null|TXRequest
     */
    public static function create($module, $method=null)
    {
        if (NULL === self::$_instance){
            self::$_instance = new self($module, $method);
        }
        return self::$_instance;
    }

    /**
     * @return null|TXRequest
     */
    public static function getInstance()
    {
        if (NULL === self::$_instance){
            return new self(null);
        }
        return self::$_instance;
    }

    private function __construct($module, $method=null)
    {
        $this->id = crc32(microtime(true));
        $this->module = $module;
        $this->method = $method ?: 'index';
        $this->csrfToken = $this->getCookie(TXConfig::getConfig('csrfToken'));
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
        if (!$this->csrfToken){
            $trueToken = $this->generateCsrf();
            $this->csrfToken = md5($trueToken);
            $trueKey = TXConfig::getConfig('trueToken');
            $csrfKey = TXConfig::getConfig('csrfToken');
            $this->setCookie($trueKey, $trueToken);
            $this->setCookie($csrfKey, $this->csrfToken);
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
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
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

    public function getMethod($row=false)
    {
        return $row ? $this->method : 'action_' . $this->method;
    }

    public function isShowTpl()
    {
        return isset($_SERVER['HTTP_X_SHOW_TEMPLATE']);
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
     * @return mixed|string
     * @throws TXException
     */
    public function getUrl()
    {
        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // IIS
            $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
            if ($requestUri !== '' && $requestUri[0] !== '/') {
                $requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $requestUri);
            }
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0 CGI
            $requestUri = $_SERVER['ORIG_PATH_INFO'];
            if (!empty($_SERVER['QUERY_STRING'])) {
                $requestUri .= '?' . $_SERVER['QUERY_STRING'];
            }
        } else {
            throw new TXException(6000);
        }

        return $requestUri;
    }

    /**
     * 获取根URL
     * @param bool $host
     * @return string
     */
    public function getBaseUrl($host=false)
    {
        if (RUN_SHELL){
            global $argv;
            return $argv[1];
        } else {
            return $host ? $this->getHostInfo().TXApp::$base->router->rootPath : TXApp::$base->router->rootPath;
        }
    }

    /**
     * @return null|string
     */
    public function getHostInfo()
    {
        if ($this->_hostInfo === null) {
            $secure = $this->getIsSecureConnection();
            $http = $secure ? 'https' : 'http';
            if (isset($_SERVER['HTTP_HOST'])) {
                $this->_hostInfo = $http . '://' . $_SERVER['HTTP_HOST'];
            } else {
                $this->_hostInfo = $http . '://' . $_SERVER['SERVER_NAME'];
                $port = $secure ? $this->getSecurePort() : $this->getPort();
                if (($port !== 80 && !$secure) || ($port !== 443 && $secure)) {
                    $this->_hostInfo .= ':' . $port;
                }
            }
        }

        return $this->_hostInfo;
    }

    /**
     * @return int|null
     */
    public function getPort()
    {
        if ($this->_port === null) {
            $this->_port = !$this->getIsSecureConnection() && isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : 80;
        }

        return $this->_port;
    }

    /**
     * @return int|null
     */
    public function getSecurePort()
    {
        if ($this->_securePort === null) {
            $this->_securePort = $this->getIsSecureConnection() && isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : 443;
        }

        return $this->_securePort;
    }

    /**
     * @return bool|null
     */
    public function getIsSecureConnection()
    {
        if ($this->_isSecure === null){
            $this->_isSecure = isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1)
                || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
        }
        return $this->_isSecure;
    }

    /**
     * @return mixed
     */
    public function getServerName()
    {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * @return int
     */
    public function getServerPort()
    {
        return (int) $_SERVER['SERVER_PORT'];
    }

    /**
     * @return null
     */
    public function getReferrer()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    }

    /**
     * @return null
     */
    public function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
    }

    /**
     * 获取ip
     * @param bool $remote
     * @return null
     */
    public function getUserIP($remote=false)
    {
        if ($remote){
            return isset($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null);
        } else {
            return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        }
    }

    /**
     * 通关ua判断是否为手机
     * @return bool
     */
    public function isMobile()
    {
        //正则表达式,批配不同手机浏览器UA关键词。
        $regex_match = "/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";
        $regex_match .= "htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";
        $regex_match .= "blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";
        $regex_match .= "symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
        $regex_match .= "jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320×320|240×320|176×220";
        $regex_match .= "|mqqbrowser|juc|iuc|ios|ipad";
        $regex_match .= ")/i";

        return isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']) or preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
    }

    /**
     * 获取ContentType
     * @return mixed
     */
    public function getContentType()
    {
        return $_SERVER['CONTENT_TYPE'];
    }

    //设置默认编码
    public function setCharset($charset = 'UTF-8')
    {
        header('charset: ' . $charset);
    }

    public function setContentType($contentType='text/html')
    {
        header('Content-type: ' . $contentType);
    }

    public function redirect($url)
    {
        header("Location:$url");
        exit();
    }
}