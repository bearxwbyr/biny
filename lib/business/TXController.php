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
        TXEvent::trigger(onRequest, array($requests));
        $result = $this->call($requests);
        return $result;
    }

    /**
     * @param $module
     * @param $request
     * @return mixed
     * @throws TXException
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
        $args = $this->getArgs($module, $method);

        $object = $this->getAction($module, $request);
        if ($object instanceof TXResponse || $object instanceof TXJSONResponse){
            TXEvent::trigger(afterAction, array($request));
            return $object;
        }

        if ($object instanceof TXAction) {
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
        $params = TXRouter::$ARGS;
        $args = [];
        if (!method_exists($obj, $method)){
            throw new TXException(2002, array($method, $obj));
        }
        $action = new ReflectionMethod($obj, $method);
        if ($action->getName() !== $method){
            throw new TXException(2002, array($method, $obj));
        }
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