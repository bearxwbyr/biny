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