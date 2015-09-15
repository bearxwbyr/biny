<?php
/**
 * String helper class
 */
class TXString
{
    /**
     * 中英文长度截取
     * @param $str @截取字符串
     * @param int $len @需要截取的长度
     * @return string
     */
    public static function cut_chinese($str, $len=10)
    {
        $str = trim($str);
        $result = '';
        $length = 0;
        $inc = 1;
        $flag = 0; //1表示上个是中文，0表示上个非中文
        for ($j = 0; $j < strlen($str); $j += $inc) {
            if ($length < $len) {
                if (ord($str[$j]) > 128) {
                    // $result += ($str[$j]+$str[$j+1]+$str[$j+2]);
                    $result .= substr($str, $j, 3);
                    $inc = 3;
                    $length += 2;
                    $flag = 1;
                } else {
                    $result .= $str[$j];
                    $inc = 1;
                    $length += 1;
                    $flag = 0;
                }
            } else {
                if ($flag == 0) {
                    $result = substr($result, 0, strlen($result) - 1) . "...";
                    break;
                } else if ($flag == 1) {
                    $result = substr($result, 0, strlen($result) - 3) . "...";
                    break;
                }

            }
        }
        return $result;
//        return mb_substr( $str, $start, $len, 'utf-8');
    }

    /**
     * 实体化转义
     * @param $content
     * @return string
     */
    public static function encode($content)
    {
        return is_string($content) ? htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE) : $content;
    }

    /**
     * 实体化转义
     * @param $content
     * @return string
     */
    public static function decode($content)
    {
        return is_string($content) ? htmlspecialchars_decode($content, ENT_QUOTES) : $content;
    }
}