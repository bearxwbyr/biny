<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-11-4
 * Time: 下午3:05
 */
class TXForm
{
    const typeInt = 1;
    const typeBool = 2;
    const typeArray = 3;
    const typeObject = 4;
    const typeDate = 5;
    const typeDatetime = 6;
    const typeNonEmpty = 7;

    protected $_params = array();
    protected $_values = array();
    protected $_rules = array();
    protected $_dateFormats = array("Y-m-d", "Y/m/d");
    protected $_datetimeFormats = array("Y-m-d H:i", "Y/m/d H:i", "Y-m-d H:i:s", "Y/m/d H:i:s");

    protected $_datas = array();

    private $_errorMsg = array();

    /**
     * 构造函数
     * @param array $params
     */
    public function __construct($params=array())
    {
        $this->_params = $params;
    }

    /**
     * 构造form
     */
    public function init()
    {
        foreach ($this->_values as $key => $default){
            if (is_int($key)){
                $this->_datas[$default] = isset($this->_params[$default]) ? $this->_params[$default] : null;
            } else {
                $this->_datas[$key] = isset($this->_params[$key]) ? $this->_params[$key] : $default;
            }
        }
    }

    /**
     * 获取form
     * @return array
     */
    public function values()
    {
        return $this->_datas;
    }

    /**
     * 获取form字段
     * @param $name
     * @return mixed
     * @throws TXException
     */
    public function __get($name)
    {
        if (!array_key_exists($name, $this->_datas)){
            throw new TXException(5001, array($name, get_class($this)));
        }
        return $this->_datas[$name];
    }

    /**
     * 返回正确
     * @return bool
     */
    protected function correct()
    {
        return true;
    }

    /**
     * 返回错误
     * @param $arr
     * @return bool
     */
    protected function error($arr=array())
    {
        $this->_errorMsg = $arr;
        return false;
    }

    /**
     * 获取错误信息
     * @return bool
     */
    public function getError()
    {
        return $this->_errorMsg ?: false;
    }

    /**
     * 检测form合法性
     * @return bool
     * @throws TXException
     */
    public function check()
    {
        foreach ($this->_rules as $key => $value){
            if (is_int($key)){
                continue;
            }
            switch ($value){
                case self::typeInt:
                    if (!is_numeric($this->__get($key))){
                        return $this->error(array($key=>"type Error"));
                    }
                    break;

                case self::typeBool:
                    if ($this->__get($key) !== "true" && $this->__get($key) !== "false"){
                        return $this->error(array($key=>"type Error"));
                    }
                    break;

                case self::typeArray:
                    if (!is_array($this->__get($key))){
                        return $this->error(array($key=>"type Error"));
                    }
                    break;

                case self::typeObject:
                    if (!is_object($this->__get($key))){
                        return $this->error(array($key=>"type Error"));
                    }
                    break;

                case self::typeDate:
                    $str = $this->__get($key);
                    $time = strtotime($this->__get($key));
                    if (!$time){
                        return $this->error(array($key=>"type Error"));
                    }
                    $match = false;
                    foreach ($this->_dateFormats as $format){
                        if (date($format, $time) == $str){
                            $match = true;
                        }
                    }
                    if (!$match){
                        return $this->error(array($key=>"type Error"));
                    }
                    break;

                case self::typeDatetime:
                    $str = $this->__get($key);
                    $time = strtotime($this->__get($key));
                    if (!$time){
                        return $this->error(array($key=>"type Error"));
                    }
                    $match = false;
                    foreach ($this->_datetimeFormats as $format){
                        if (date($format, $time) !== $str){
                            $match = true;
                        }
                    }
                    if (!$match){
                        return $this->error(array($key=>"type Error"));
                    }
                    break;

                case self::typeNonEmpty:
                    if (!$this->__get($key)){
                        return $this->error(array($key=>"type Error"));
                    }
                    break;

                default:
                    $value = 'valid_'.$value;
                    if (!method_exists($this, $value)){
                        throw new TXException(5002, array($value, get_class($this)));
                    }
                    return $this->$value();
            }
        }
        return true;
    }
}