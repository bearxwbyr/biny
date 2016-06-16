<?php
/**
 * Action config class
 */
class TXAction
{
    /**
     * 请求参数
     * @var array
     */
    protected $params;

    /**
     * POST参数
     * @var array
     */
    protected $posts;

    /**
     * JSON参数
     * @var array
     */
    protected $jsons = NULL;

    /**
     * 字符串验证
     * @var bool
     */
    protected $valueCheck = false;

    /**
     * csrf验证
     * @var bool
     */
    protected $csrfValidate = true;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->posts = $_POST;
        $this->params = $_REQUEST;
        $this->gets = $_GET;
        //判断是否维护中
        if (isMaintenance){
            return $this->display('Main/maintenance');
        }
        if ($this->csrfValidate && !TXApp::$base->request->validateCsrfToken()){
            header(TXConfig::getConfig(401, 'http'));
            return $this->error("Unauthorized");
        }
        TXApp::$base->request->createCsrfToken();
        $this->setCharset();
        $this->setContentType();
    }

    /**
     * 获取Service|DAO
     * @param $obj
     * @return TXService | TXDAO
     */
    public function __get($obj)
    {
        if (substr($obj, -7) == 'Service' || substr($obj, -3) == 'DAO') {
            return TXFactory::create($obj);
        }
    }

    /**
     * Display to template
     * @param $view
     * @param array $params
     * @param array $objects
     * @return TXResponse
     */
    public function display($view, $params=array(), $objects=array())
    {
        return new TXResponse($view, $params, $objects);
    }


    /**
     * 获取Form
     * @param $name
     * @param null $method
     * @return TXForm
     */
    public function getForm($name, $method=null)
    {
        $name .= 'Form';
        $form = new $name($this->params);
        if ($method && method_exists($form, $method)){
            $form->$method();
        }
        $form->init();
        return $form;
    }

    /**
     * 获取原始Post数据
     * @return string
     */
    public function getRowPost()
    {
        return file_get_contents('php://input');
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
            return $check ? $this->checkParam($key, $this->params) : $this->params[$key];
        } else {
            return $default;
        }
    }

    /**
     * 获取POST参数
     * @param $key
     * @param null $default
     * @param bool $check
     * @return float|int|mixed|null
     */
    public function getPost($key, $default=null, $check=true)
    {
        if (isset($this->posts[$key])){
            //参数验证
            return $check ? $this->checkParam($key, $this->posts) : $this->posts[$key];
        } else {
            return $default;
        }
    }

    /**
     * 获取GET参数
     * @param $key
     * @param null $default
     * @param bool $check
     * @return float|int|mixed|null
     */
    public function getGet($key, $default=null, $check=true)
    {
        if (isset($this->gets[$key])){
            //参数验证
            return $check ? $this->checkParam($key, $this->gets) : $this->gets[$key];
        } else {
            return $default;
        }
    }

    /**
     * 获取json数据
     * @param $key
     * @param null $default
     * @param bool $check
     * @return float|int|mixed|null
     */
    public function getJson($key, $default=null, $check=false){
        if ($this->jsons === NULL){
            $this->jsons = json_decode($this->getRowPost(), true) ?: [];
        }
        if (isset($this->jsons[$key])){
            //参数验证
            return $check ? $this->checkParam($key, $this->jsons) : $this->jsons[$key];
        } else {
            return $default;
        }
    }

    /**
     * 参数名验证法
     * @param $key
     * @param $params
     * @return float|int|mixed
     * @throws TXException
     */
    private function checkParam($key, $params=null)
    {
        $params = ($params === null) ? $this->params : $params;
        $t = substr($key, 0, 1);
        switch ($t){
            //数字
            case 'i':
                if (!is_numeric($params[$key]) && $this->valueCheck){
                    throw new TXException(2003, array($key, gettype($params[$key])));
                }
                if (strstr($params[$key], '.')){
                    return intval($params[$key]);
                } else {
                    return doubleval($params[$key]);
                }

            //字符串
            case 's':
                if (!is_string($params[$key]) && $this->valueCheck){
                    throw new TXException(2003, array($key, gettype($params[$key])));
                } else {
                    return $params[$key];
                }

            //数组
            case 'o':
                if (!is_array($params[$key]) && $this->valueCheck){
                    throw new TXException(2003, array($key, gettype($params[$key])));
                }
                return $params[$key];

            //bool
            case 'b':
                if ($params[$key] !== "true" && $params[$key] !== "false" && $this->valueCheck){
                    throw new TXException(2003, array($key, gettype($params[$key])));
                }
                return json_decode($params[$key], true);

            //日期格式
            case 'd':
                if (!strtotime($params[$key]) && $this->valueCheck){
                    throw new TXException(2003, array($key, gettype($params[$key])));
                }
                return $params[$key];

            default:

                return $params[$key];
        }
    }

    /**
     * display to json
     * @param $data
     * @param bool $encode
     * @return TXJSONResponse
     */
    public function json($data, $encode=true)
    {
        return new TXJSONResponse($data, $encode);
    }

    /**
     * @param array $ret
     * @param bool $encode
     * @return TXJSONResponse
     */
    public function correct($ret=array(), $encode=true)
    {
        $data = array("flag" => true, "ret" => $ret);
        return $this->json($data, $encode);
    }

    /**
     * @param string $msg
     * @param bool $encode
     * @return string|TXJSONResponse
     */
    public function error($msg="数据异常", $encode=true)
    {
        TXEvent::trigger(onError, array($msg));
        if (TXApp::$base->request->isAjax){
            $data = array("flag" => false, "error" => $msg);
            return $this->json($data, $encode);
        } else {
            return $this->display('error/msg', ['message'=> $msg]);
        }
    }

    //设置默认编码
    protected function setCharset($charset = 'UTF-8')
    {
        header('charset: ' . $charset);
    }

    protected function setContentType($contentType='text/html')
    {
        header('Content-type: ' . $contentType);
    }

    public function redirect($url)
    {
        header("Location:$url");
        exit();
    }

    /**
     * beforeAction事件
     * @param $event
     * @param TXRequest $request
     */
    public static function beforeAction($event, $request)
    {
        TXLogger::addLog('router: '.$request->getServerName().$request->getUrl());
    }
}