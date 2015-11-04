<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-9-9
 * Time: 下午3:30
 */
class TXSqlData extends ArrayObject
{
    public function __toString()
    {
        return 'TXSqlData Array';
    }

    public function __toLogger()
    {
        $logger = array();
        foreach ($this->getIterator() as $key => $data){
            $logger[$key] = $data->__toLogger();
        }
        return $logger;
    }

    public function __invoke()
    {
        return $this->getArrayCopy() ? true : false;
    }

    /**
     * 获取数据
     * @param bool $inner
     * @return array|ArrayIterator
     */
    public function values($inner=false)
    {
        if (!$inner){
            return $this->getArrayCopy();
        }
        $values = array();
        foreach ($this->getIterator() as $key => $value){
            $values[$key] = $value->values();
        }
        return $values;
    }

    public function json_encode()
    {
        $array = array();
        foreach ($this->getIterator() as $key => $value){
            $array[$key] = $value->values();
        }
        return json_encode($array);
    }

    /**
     * 获取列表
     * @param $field
     * @param bool $dup
     * @return array
     */
    public function lists($field, $dup=true)
    {
        $lists = array();
        foreach ($this->getIterator() as $data){
            if ($dup || !in_array($data->$field, $lists)){
                $lists[] = $data->$field;
            }
        }
        return $lists;
    }

    /**
     * 获取字典
     * @param $field
     * @param null $value
     * @return $this
     */
    public function dict($field, $value=null)
    {
        $dict = array();
        foreach ($this->getIterator() as $data){
            if (!isset($data->$field)){
                break;
            }
            $dict[$data[$field]] = $value ? $data[$value] : $data;
        }
        if ($value){
            return $dict;
        }
        $this->exchangeArray($dict);
        return $this;
    }
}