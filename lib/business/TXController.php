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
        $this->router = new TXRouter();
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
        $requests = $this->router->getRequests();
//        $Module = $requests->getModule();
//        $unable_module = TXConfig::getConfig('unable_modules');
//        if ($unable_module && in_array($Module, $unable_module)) {
//            echo "Url Forbidden!";
//            throw new TXException(2010);
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
     * @param $action
     * @param $params
     * @return TXAction
     */
    private function getAction($action, $params)
    {
        $actionObject = new $action($params);

        return $actionObject;
    }

    /**
     * 执行请求
     * @param TXRequest $request
     * @throws TXException
     * @return mixed
     */
    private function call(TXRequest $request)
    {
        $action = $request->getModule() . 'Action';
        $params = $request->getParams();

        $actionObject = $this->getAction($action, $params);

        if ($actionObject instanceof TXAction) {
            $args = $this->getArgs($actionObject, $params);
            $result = call_user_func_array([$actionObject, 'execute'], $args);
            return $result;
        } else {
            throw new TXException(2000, array($request->getModule()));
        }
    }

    /**
     * 获取默认参数
     * @param $obj
     * @param $params
     * @return array
     */
    private function getArgs($obj, $params)
    {
        $args = [];
        $method = new ReflectionMethod($obj, 'execute');
        foreach ($method->getParameters() as $param) {
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
        } else {    //flash remote
        }
    }
}