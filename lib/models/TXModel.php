<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-7-29
 * Time: 上午11:18
 */
class TXModel
{
    protected $_data;
    protected $_dirty = false;
    protected $DAO = null;
    protected $_pk;

    public function __get($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    public function __set($key, $value)
    {
        if (array_key_exists($key, $this->_data) && $this->_data[$key] !== $value){
            $this->_data[$key] = $value;
            $this->_dirty = true;
        }
    }

    public function __isset($key)
    {
        return isset($this->_data[$key]);
    }

    public function save()
    {
        if ($this->_data && $this->DAO){
            $this->DAO->updateByPK($this->_pk, $this->_data);
            $this->_dirty = false;
        }
    }



    public function __toLogger()
    {
        return $this->_data;
    }

}