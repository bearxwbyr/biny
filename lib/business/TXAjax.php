<?php
/**
 * Action config class
 */
class TXAjax {

    /**
     * 请求参数
     * @var array
     */
    protected $params;

    /**
     * 字符串验证
     * @var bool
     */
    protected $valueCheck = true;

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
     * 获取Service
     * @param $obj
     * @return TXService
     */
    public function __get($obj)
    {
        if (substr($obj, -7) == 'Service') {
            return TXService::getService($obj);
        }
    }

    /**
     * 获取请求参数
     * @param $key
     * @param null $default
     * @param bool $check
     * @return float|int|mixed|null
     */
    public function getParam($key, $default=null, $check=true)
    {
        if (isset($this->params[$key])){
            //参数验证
            return $check ? $this->checkParam($key) : $this->params[$key];
        } else {
            return $default;
        }
    }

    /**
     * 参数名验证法
     * @param $key
     * @return float|int|mixed
     * @throws TXException
     */
    private function checkParam($key)
    {
        $t = substr($key, 0, 1);
        switch ($t){
            //数字
            case 'i':
                if (!is_numeric($this->params[$key]) && $this->valueCheck){
                    throw new TXException(2005, array($key, gettype($this->params[$key])));
                }
                if (strstr($this->params[$key], '.')){
                    return intval($this->params[$key]);
                } else {
                    return doubleval($this->params[$key]);
                }

            //字符串
            case 's':
                if (!is_string($this->params[$key]) && $this->valueCheck){
                    throw new TXException(2005, array($key, gettype($this->params[$key])));
                } else {
                    return $this->params[$key];
                }

            //数组
            case 'o':
                if (!is_array($this->params[$key]) && $this->valueCheck){
                    throw new TXException(2005, array($key, gettype($this->params[$key])));
                }
                return $this->params[$key];

            //bool
            case 'b':
                if ($this->params[$key] !== "true" && $this->params[$key] !== "false" && $this->valueCheck){
                    throw new TXException(2005, array($key, gettype($this->params[$key])));
                }
                return json_decode($this->params[$key], true);

            //日期格式
            case 'd':
                if (!strtotime($this->params[$key]) && $this->valueCheck){
                    throw new TXException(2005, array($key, gettype($this->params[$key])));
                }
                return $this->params[$key];

            default:

                return $this->params[$key];
        }
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
        $data = array("flag" => false, "error" => $msg);
        return $this->json($data);
    }
}