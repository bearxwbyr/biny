<?php
/**
 * 基础类 service
 * @author geyuchen
 */
class BaseService extends TXService
{
    /**
     * 根据key排序
     * @param $objects
     * @param $key
     * @param $sort
     * @return mixed
     */
    function SortArray($objects, $key, $sort)
    {
        $sortKey = array();
        foreach ($objects as $k => $object){
            $sortKey[$k] = $object[$key];
        }
        array_multisort($sortKey, $sort, $objects);
//    call_user_func_array('array_multisort', $params);
        return $objects;
    }


}