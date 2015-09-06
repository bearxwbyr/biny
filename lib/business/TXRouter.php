<?php
/**
 * Router class
 */
class TXRouter {
    /**
     * @var TXRequest
     */
    private $requests;

    public static $rootPath = '';

    /**
     * @return TXRequest
     */
    public function getRequests()
    {
        return $this->requests;
    }

    private $routerInfo;

    function __construct()
    {
        $this->routerInfo = TXConfig::getConfig('router');
    }

    /**
     * 设置资源路径
     * @param $pathInfo
     */
    private static function buildRootPath($pathInfo)
    {
        foreach ($pathInfo as $path){
            if ($path !== "index.php"){
                self::$rootPath .= "/$path";
            }
        }
    }

    /**
     * 获取路由信息
     * @return array|bool
     */
    private function getRouterInfo()
    {
        $scriptInfo = explode("/", trim($_SERVER['SCRIPT_NAME'], '/'));
        self::buildRootPath($scriptInfo);
        if (substr($_SERVER['REQUEST_URI'], 0, strlen(self::$rootPath."/static")) == self::$rootPath."/static"){
            header('HTTP/1.1 404 Not Found');
            header("status: 404 Not Found");
            echo 'Source File Not Found';
            exit;
        }
        TXConfig::setAlias('web', self::$rootPath);
        $pathRoot = strpos($_SERVER['REQUEST_URI'], '?') ? strstr($_SERVER['REQUEST_URI'], '?', true) : $_SERVER['REQUEST_URI'];
        $pathRoot = explode(self::$rootPath, $pathRoot)[1];
        $pathInfo = trim($pathRoot, '/') ? explode("/", trim($pathRoot, '/')) : false;
        if (!$pathInfo){
            return false;
        }
        $isAjax = false;
        if ($pathInfo[0] == "action" || $pathInfo[0] == "ajax"){
            $isAjax = array_shift($pathInfo) == "ajax";
        }
        List($module, $method) = $pathInfo;

        return array($module, $method, $isAjax);

    }

    /**
     * 路由入口
     */
    public function router()
    {
        $isAjax = false;
        if ($pathInfo = $this->getRouterInfo()){
            List($module, $method, $isAjax) = $pathInfo;
        } else {
            $module = $this->routerInfo['base_action'];
            $method = null;
        }
        $this->requests = TXRequest::create($module, $_REQUEST, $isAjax, $method);
    }
}