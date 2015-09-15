<?php
/**
 * 基础类 service
 * @author geyuchen
 */
class baseService extends TXService
{
    /**
     * @param $objects
     * @param $sorts ['id'=>SORT_DESC, 'type'=>SORT_ASC]
     * @return mixed
     */
    public function sortArray($objects, $sorts)
    {
        $avgs = array();
        foreach ($sorts as $key => $type){
            $sortKey = array();
            foreach ($objects as $k => $object){
                $sortKey[$k] = $object[$key];
            }
            $avgs[] = $sortKey;
            $avgs[] = $type;
        }
        $avgs[] = &$objects;
        call_user_func_array('array_multisort', $avgs);
        return $objects;
    }


}