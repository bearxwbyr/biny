<?php
/**
 * Class TXDispatcher
 */
class TXController {
    /**
     * @var TXRouter
     */
    private $router;

    public function __construct()
    {
        $this->router = TXApp::$base->router;
    }

    /**
     * router
     */
    private function router()
    {
        $this->router->router();
    }

    /**
     * 执行Action
     * @throws TXException
     * @return mixed
     */
    private function execute()
    {
        $requests = TXApp::$base->request;
//        $Module = $requests->getModule();
//        $unable_module = TXConfig::getConfig('unable_modules');
//        if ($unable_module && in_array($Module, $unable_module)) {
//            echo "Url Forbidden!";
//            throw new TXException(2004);
//        }
        $result = array();
        if ($requests instanceof TXRequest) {   //web view
            $result = $this->call($requests);
        } else {    //flash mulit request
            foreach ($requests as $request) {
                $result[] = $this->call($request);
            }
        }

        return $result;
    }

    /**
     * @param $module
     * @param $request
     * @return mixed
     */
    private function getAction($module, $request)
    {
        $object = new $module();
        TXEvent::trigger(beforeAction, array($request));
        if (method_exists($object, 'init')){
            $result = $object->init();
            if ($result instanceof TXResponse || $result instanceof TXJSONResponse){
                return $result;
            }
        }
        return $object;
    }

    /**
     * 执行请求
     * @param TXRequest $request
     * @throws TXException
     * @return mixed
     */
    private function call(TXRequest $request)
    {
        $module = $request->getModule() . 'Action';
        $method = $request->getMethod();

        $object = $this->getAction($module, $request);
        if ($object instanceof TXResponse || $object instanceof TXJSONResponse){
            TXEvent::trigger(afterAction, array($request));
            return $object;
        }

        if ($object instanceof TXAction) {
            $args = $this->getArgs($object, $method);
            $result = call_user_func_array([$object, $method], $args);
            TXEvent::trigger(afterAction, array($request));
            return $result;
        } else {
            throw new TXException(2001, $request->getModule());
        }
    }

    /**
     * 获取默认参数
     * @param $obj
     * @param $method
     * @return array
     * @throws TXException
     */
    private function getArgs($obj, $method)
    {
        $params = $_GET;
        $args = [];
        if (!method_exists($obj, $method)){
            throw new TXException(2002, array($method, get_class($obj)));
        }
        $action = new ReflectionMethod($obj, $method);
        foreach ($action->getParameters() as $param) {
            $name = $param->getName();
            $args[] = isset($params[$name]) ? $params[$name] : ($param->isDefaultValueAvailable() ? $param->getDefaultValue() : null);
        }
        return $args;
    }

    /**
     * Dispatcher method
     */
    public function dispatcher()
    {
        $this->router();    //router

        $result = $this->execute(); //execute
        if ($result instanceof TXResponse) {    //view
            echo $result;
        } elseif ($result instanceof TXJSONResponse) {  //json数据
            echo $result;
        } else {
            echo $result;
        }
    }
}