<?php
class TXRequest {
    private $module;

    private $params;

    private $isAjax=false;

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
     * @return TXRequest
     */
    public static function create($module, $params, $isAjax=false)
    {
        if (NULL === self::$_instance){
            self::$_instance = new self($module, $params, $isAjax);
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

    public function __construct($module, $params, $isAjax=false)
    {
        $this->id = crc32(microtime(true));
        $this->module = $module;
        $this->params = $params;
        $this->isAjax = $isAjax;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getAjax()
    {
        return $this->isAjax;
    }

    public function getParams()
    {
        return $this->params;
    }
}