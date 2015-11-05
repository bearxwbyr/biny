<?php
/**
 * Action config class
 */
class TXAjax extends TXBase
{
    /**
     * 构造函数
     * @param $params
     */
    public function __construct($params)
    {
        if (isMaintenance){
            echo $this->error("网站维护中，请稍候再试");
            exit;
        }
        $this->params = $params;
    }

    /**
     * Display to template
     * @param $view
     * @param array $params
     * @param array $ignores
     * @return string
     */
    public function display($view, $params = array(), $ignores=array())
    {
        $response = new TXResponse($view, $params, $ignores);
        return $response->getContent();
    }


    /**
     * display to json
     * @param $data
     * @return TXJSONResponse
     */
    public function json($data)
    {
        return new TXJSONResponse($data);
    }

    /**
     * @param $ret
     * @return TXJSONResponse
     */
    public function correct($ret=array())
    {
        $data = array("flag" => true, "ret" => $ret);
        return $this->json($data);
    }

    /**
     * @param $msg
     * @return TXJSONResponse
     */
    public function error($msg)
    {
        TXEvent::trigger(onError, array($msg));
        $data = array("flag" => false, "error" => $msg);
        return $this->json($data);
    }
}