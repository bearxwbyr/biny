<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 16-4-18
 * Time: 下午12:18
 */
class TXCommon
{
    /**
     * url请求
     * @param $url
     * @param array $data
     * @param string $method
     * @param string $refererUrl
     * @param int $timeout
     * @param bool $proxy
     * @return bool|mixed
     */
    public static function UrlRequest($url, $data = array(), $method = 'GET', $refererUrl = '', $timeout = 10, $proxy = false) {
        $ch = null;
        if('POST' === strtoupper($method)) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER,0 );
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            if ($refererUrl) {
                curl_setopt($ch, CURLOPT_REFERER, $refererUrl);
            }
            if(is_string($data)){
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            }
        } else if('GET' === strtoupper($method)) {
            if(is_string($data)) {
                $real_url = $url. (strpos($url, '?') === false ? '?' : ''). $data;
            } else {
                $real_url = $url. (strpos($url, '?') === false ? '?' : ''). http_build_query($data);
            }
            $ch = curl_init($real_url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            if ($refererUrl) {
                curl_setopt($ch, CURLOPT_REFERER, $refererUrl);
            }
        } else {
            return false;
        }

        if($proxy) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        }
        $ret = curl_exec($ch);
        curl_close($ch);
        return json_decode($ret, true);
    }

    /**
     * @param $objects
     * @param $sorts ['id'=>SORT_DESC, 'type'=>SORT_ASC]
     * @return mixed
     */
    public static function sortArray($objects, $sorts)
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

    /**
     * 获取随机字符串
     * @param int $len
     * @return string
     */
    public static function generateCode($len = 16)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < $len; $i++) {
            $code .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $code;
    }
}