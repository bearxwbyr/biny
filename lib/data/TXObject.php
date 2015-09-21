<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-9-9
 * Time: 下午3:30
 */
class TXObject extends ArrayObject
{
    private $storage = [];

    public function __construct($storage=array())
    {
        $this->storage = $storage;
    }

    public function __toString()
    {
        return 'TXObject';
    }

    /**
     * 原数据
     * @param $k
     * @return null
     */
    public function __get($k)
    {
        return isset($this->storage[$k]) ? $this->storage[$k] : null;
    }

    public function __set($k, $value)
    {
        $this->storage[$k] = $value;
    }

    public function __isset($k)
    {
        return isset($this->storage[$k]);
    }

    public function __unset($k)
    {
        unset($this->storage[$k]);
    }

    public function offsetGet($k)
    {
        return isset($this->storage[$k]) ? $this->encode($this->storage[$k]) : null;
    }

    public function offsetExists($k)
    {
        return $this->__isset($k);
    }

    public function offsetUnset($k)
    {
        $this->__unset($k);
    }

    public function offsetSet($k, $value)
    {
        $this->__set($k, $value);
    }

    public function count()
    {
        return count($this->storage);
    }

    public function __toLogger()
    {
        return $this->storage;
    }

    public function values()
    {
        return $this->storage;
    }

    private function encode($value)
    {
        return TXString::encode($value);
    }

    public function __call($method, $args)
    {
        $args[] = $this->storage;
        return call_user_func_array($method, $args);
    }

    public function serialize()
    {
        return serialize($this->storage);
    }
}