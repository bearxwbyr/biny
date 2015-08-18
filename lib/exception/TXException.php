<?php
/**
 * Core Exception
 */
class TXException extends Exception
{
    /**
     * 构造函数
     * @param string $code
     * @param array $params
     * @param string $html
     */
    public function __construct($code, $params = array(), $html="404")
    {
        try{
            if ($httpCode = TXConfig::getConfig($html, 'http')){
                header($httpCode);
            }
            if (SYS_DEBUG){
                $message = $this->fmt_code($code, $params);
                $message = $this->escapeString(htmlspecialchars($message, ENT_QUOTES));
                if (!TXRequest::getInstance()->getAjax()){
                    echo "<pre>";
                    parent::__construct($message, $code);
                } else {
                    $data = array("flag" => false, "error" => $message);
                    echo new TXJSONResponse($data);
                }

            } else {
                if (!TXRequest::getInstance()->getAjax()){
                    ob_clean();
                    $params = [
                        'rootPath' => TXConfig::getAppConfig('rootPath', 'dns')
                    ];
                    extract($params);
                    $file = sprintf('%s/template/%s.tpl.php', TXApp::$app_root, "Error/".$html);
                    include $file;
                } else {
                    $data = array("flag" => false, "error" => "系统数据异常：$html");
                    echo new TXJSONResponse($data);
                }
            }
        } catch (TXException $ex) {
            //防止异常的死循环
            echo "system Error";
            exit;
        }


    }

    public function __destruct()
    {
        if (!TXRequest::getInstance()->getAjax()){
            echo '</pre>';
        }
    }

    /**
     * 引号转义
     * @param $message
     * @return mixed
     */
    private function escapeString($message){
        return str_replace('`', '&#96;', $message);
    }

    /**
     * 格式化代码为字符串
     * @param int $code
     * @param array $params
     * @return string
     */
    private function fmt_code($code, $params)
    {
        try {
            $msgtpl = TXConfig::getConfig($code, 'exception');
        } catch (TXException $ex) { //防止异常的死循环
            $msgtpl = $ex->getMessage();
        }
        return vsprintf($msgtpl, $params);
    }
}