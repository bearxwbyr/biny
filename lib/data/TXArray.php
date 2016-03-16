<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-12-1
 * Time: 上午11:12
 * @method bool in_array($key)
 * @method bool array_key_exists($key)
 */
class TXArray extends ArrayObject
{
    private $storage = [];
    private $encodes = [];

    public function __construct($storage=array())
    {
        $this->storage = $storage;
    }

    public function __toString()
    {
        return 'TXArray';
    }

    public function getIterator()
    {
        foreach ($this->storage as $key => $value){
            $key = $this->encode($key);
            if (!isset($this->encodes[$key])){
                $this->encodes[$key] = $this->encode($value);
            }
        }
        return new ArrayIterator($this->encodes);
    }

    public function __get($k)
    {
        return isset($this->storage[$k]) ? $this->storage[$k] : null;
    }

    public function get($key)
    {
        return $this->__get($key);
    }

    public function __set($k, $value)
    {
        $this->storage[$k] = $value;
        $this->encodes[$k] = $this->encode($value);
    }

    public function __isset($k)
    {
        return isset($this->storage[$k]);
    }

    public function __unset($k)
    {
        unset($this->storage[$k]);
        $k = $this->encode($k);
        unset($this->encodes[$k]);
    }

    public function offsetGet($k)
    {
        if (isset($this->storage[$k])){
            $key = $this->encode($k);
            if (!isset($this->encodes[$key])){
                $this->encodes[$key] = $this->encode($this->storage[$k]);
            }
            return $this->encodes[$key];
        }
        return null;
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

    private function encode($value)
    {
        if (is_string($value)){
            $value = TXString::encode($value);
        } elseif (is_array($value)){
            $value = new self($value);
        }
        return $value;
    }

    public function __call($method, $args)
    {
        $args[] = &$this->storage;
        return call_user_func_array($method, $args);
    }

    public function serialize()
    {
        return serialize($this->storage);
    }

    public function __invoke()
    {
        return $this->storage ? true : false;
    }

    public function keys()
    {
        return array_keys($this->values(false));
    }

    /**
     * 完全转义
     * @param bool $inner
     * @return array
     */
    public function values($inner=true)
    {
        $values = array();
        foreach ($this->storage as $key => $value){
            $key = $this->encode($key);
            if (!isset($this->encodes[$key])){
                $this->encodes[$key] = $this->encode($value);
            }
            if ($this->encodes[$key] instanceof TXArray){
                $values[$key] = $inner ? $this->encodes[$key]->values() : $this->encodes[$key];
            } else {
                $values[$key] = $this->encodes[$key];
            }
        }
        return $values;
    }

    public function json_encode($encode=true)
    {
        return $encode ? json_encode($this->values()) : json_encode($this->storage);
    }
}