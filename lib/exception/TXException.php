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
    public function __construct($code, $params=array(), $html="404")
    {
        $message = $this->fmt_code($code, $params);
        TXEvent::trigger(onException, array($code, array($message, $this->getTraceAsString())));
        try{
            if ($httpCode = TXConfig::getConfig($html, 'http')){
                header($httpCode);
            }
            if (SYS_DEBUG){
                $message = TXString::encode($message);
                echo "<pre>";
                parent::__construct($message, $code);

            } else {
                if (!TXApp::$base->request->isAjax){
                    $params = [
                        'webRoot' => TXConfig::getAppConfig('webRoot', 'dns')
                    ];
                    echo new TXResponse("error/$html", array(), $params);
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
        if (SYS_DEBUG || !TXApp::$base->request->isAjax){
            echo '</pre>';
        }
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

    /**
     * @param $event
     * @param $code
     * @param $params
     */
    public static function event($event, $code, $params)
    {
        TXLogger::addError("ERROR CODE: $code\n".join("\n", $params));
    }
}