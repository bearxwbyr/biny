<?php
class TXRequest {
    private $module;
    private $params;
    private $method=null;
    public $isAjax=false;
    private $id;

    /**
     * @var null|TXRequest
     */
    private static $_instance = null;

    /**
     * @var $csrfToken
     */
    public static $csrfToken = null;

    /**
     * 获取对应csrfToken
     * @return null|string
     */
    public static function createCsrfToken()
    {
        if (!self::$csrfToken){
            $trueToken = self::generateCsrf();
            self::$csrfToken = md5($trueToken);
            $trueKey = TXConfig::getConfig('trueToken');
            $csrfKey = TXConfig::getConfig('csrfToken');
            setcookie($trueKey, $trueToken, null, '/');
            setcookie($csrfKey, self::$csrfToken, null, '/');
        }
        return self::$csrfToken;
    }

    /**
     * 获取随机字符串
     * @param int $len
     * @return string
     */
    private static function generateCsrf($len = 16)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < $len; $i++) {
            $code .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $code;
    }

    /**
     * 验证csrfToken
     */
    public static function validateCsrfToken()
    {
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        } else {
            $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
        }
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return true;
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

    public function __construct($module, $params, $isAjax=false, $method=null)
    {
        $this->id = crc32(microtime(true));
        $this->module = $module;
        $this->params = $params;
        $this->isAjax = $isAjax;
        $this->method = $method;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getMethod()
    {
        return ($this->method && $this->method != "execute") ? 'action_'.$this->method : "execute";
    }

    public function getParams()
    {
        return $this->params;
    }
}