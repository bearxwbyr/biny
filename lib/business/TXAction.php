<?php
/**
 * Action config class
 * @property privilegeService $privilegeService
 */
class TXAction
{
    /**
     * 请求参数
     * @var array
     */
    private $params;

    /**
     * POST参数
     * @var array
     */
    private $posts;

    /**
     * GET参数
     * @var array
     */
    private $gets;

    /**
     * JSON参数
     * @var array
     */
    private $jsons = NULL;

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
            echo $this->error("Unauthorized");
            exit;
        }
        // 权限验证
        $this->valid_privilege();
        TXApp::$base->request->createCsrfToken();
        TXApp::$base->request->setCharset();
        TXApp::$base->request->setContentType();
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
     * 路由验证
     */
    private function valid_privilege()
    {
        if (method_exists($this, 'privilege') && $privileges = $this->privilege()){
            $request = TXApp::$base->request;
            foreach ($privileges as $method => $privilege){
                if (is_callable([$this->privilegeService, $method])){
                    $actions = $privilege['actions'];
                    if ($actions === '*' || in_array($request->getMethod(true), $actions)){
                        $params = isset($privilege['params']) ? $privilege['params'] : [];
                        array_unshift($params, $this);
                        if (!call_user_func_array([$this->privilegeService, $method], $params)){
                            if (isset($privilege['callBack']) && is_callable($privilege['callBack'])){
                                call_user_func_array($privilege['callBack'], [$this]);
                            }
                            throw new TXException(6001, $method, $this->privilegeService->getError());
                        }
                    }
                }
            }
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
        $form = new $name($this->params, $method);
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
     * @return float|int|mixed|null
     */
    public function getParam($key, $default=null)
    {
        if (TXApp::$base->request->getContentType() == 'application/json' || TXApp::$base->request->getContentType() == 'text/json'){
            return $this->getJson($key, $default);
        } else {
            return isset($this->params[$key]) ? $this->params[$key] : $default;
        }
    }

    /**
     * 获取POST参数
     * @param $key
     * @param null $default
     * @return float|int|mixed|null
     */
    public function getPost($key, $default=null)
    {
        return isset($this->posts[$key]) ? $this->posts[$key] : $default;
    }

    /**
     * 获取GET参数
     * @param $key
     * @param null $default
     * @return float|int|mixed|null
     */
    public function getGet($key, $default=null)
    {
        return isset($this->gets[$key]) ? $this->gets[$key] : $default;
    }

    /**
     * 获取json数据
     * @param $key
     * @param null $default
     * @return float|int|mixed|null
     */
    public function getJson($key, $default=null){
        if ($this->jsons === NULL){
            $this->jsons = json_decode($this->getRowPost(), true) ?: [];
        }
        return isset($this->jsons[$key]) ? $this->jsons[$key] : $default;
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
        if (TXApp::$base->request->isShowTpl() || !TXApp::$base->request->isAjax()){
            return $this->display('error/msg', ['message'=> $msg]);
        } else {
            $data = array("flag" => false, "error" => $msg);
            return $this->json($data, $encode);
        }
    }
}