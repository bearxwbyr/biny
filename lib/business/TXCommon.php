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
     * 发送邮件
     * @param $receiver
     * @param $subject
     * @param $body
     * @param bool $isHtml
     * @param array $Images
     * @return bool
     */
    public static function sendMail($receiver, $subject, $body, $isHtml=true, $Images=array())
    {
        /**
         * @var PHPMailer $mail
         */
        $mail = TXFactory::create('PHPMailer');
        $mail->IsSMTP();
        $mailConfig = TXConfig::getAppConfig('smtp', 'dns');
        if (!$mailConfig){
            return false;
        }
        $mail->Host = $mailConfig['smtp_server'];   // SMTP servers
        if (isset($mailConfig['smtp_port'])){
            $mail->Port = $mailConfig['smtp_port'];
        }
        $mail->SMTPAuth = true;           // turn on SMTP authentication
        $mail->Username = $mailConfig['server_user'];     // SMTP username  注意：普通邮件认证不需要加 @域名
        $mail->Password = $mailConfig['server_pass']; // SMTP password
        $mail->From = $mailConfig['mail_from'];      // 发件人邮箱
        $mail->CharSet = $mailConfig['mail_code'];   // 这里指定字符集！
        foreach($receiver as $value){
            $mail->AddAddress($value);  // 收件人邮箱
        }
        if ($Images){
            foreach ($Images as $Image){
                $mail->AddEmbeddedImage($Image['path'], $Image['cid'], $Image['name']);
            }
        }
        $mail->IsHTML($isHtml);  // send as HTML
        $mail->Subject = $subject;// 邮件主题
        $mail->Body = $body;// 邮件内容
        $mail->AltBody ="text/html";
        if ($mail->Send()){
            return true;
        } else {
            return false;
        }
    }

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