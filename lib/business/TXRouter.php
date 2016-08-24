<?php
/**
 * Router class
 */
class TXRouter {
    public $rootPath = '';
    private $routerInfo;

    public static $ARGS;

    function __construct()
    {
        $this->routerInfo = TXConfig::getConfig('router');
        self::$ARGS = $_GET;
    }

    /**
     * 设置资源路径
     * @param $pathInfo
     */
    private function buildRootPath($pathInfo)
    {
        foreach ($pathInfo as $path){
            if ($path !== "index.php"){
                $this->rootPath .= "/$path";
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
        $this->buildRootPath($scriptInfo);
        if (substr($_SERVER['REQUEST_URI'], 0, strlen($this->rootPath."/static")) == $this->rootPath."/static"){
            header('HTTP/1.1 404 Not Found');
            header("status: 404 Not Found");
            echo 'Source File Not Found';
            exit;
        }
        TXConfig::setAlias('web', $this->rootPath);
        $pathRoot = strpos($_SERVER['REQUEST_URI'], '?') ? strstr($_SERVER['REQUEST_URI'], '?', true) : $_SERVER['REQUEST_URI'];
        if ($this->rootPath){
            $len = strpos($pathRoot, $this->rootPath) + strlen($this->rootPath);
            $pathRoot = substr($pathRoot, $len);
        }

        $path = $this->reRouter($pathRoot);
        if ($path !== NULL){
            $pathRoot = $path;
        }
        $pathInfo = trim($pathRoot, '/') ? explode("/", trim($pathRoot, '/')) : false;
        if (!$pathInfo){
            return false;
        }
        $isAjax = false;
        if ($pathInfo[0] == "action" || $pathInfo[0] == "ajax"){
            $isAjax = array_shift($pathInfo) == "ajax";
        }
        $module = isset($pathInfo[0]) ? $pathInfo[0] : null;
        $method = isset($pathInfo[1]) ? $pathInfo[1] : null;
        return array($module, $method, $isAjax);
    }

    /**
     * 路由重定向
     * @param $url
     * @return array
     */
    private function reRouter($url)
    {
        $path = NULL;
        $rules = TXConfig::getConfig('routeRule');
        foreach ($rules as $key => $value){
            if (preg_match_all("/<(\w+):([^>]+)>/", $key, $matchs)){
                foreach ($matchs[2] as &$val){
                    $val = '('.$val.')';
                }
                unset($val);
                $matchs[0][] = '/';
                $matchs[0][] = '.';
                $matchs[2][] = '\/';
                $matchs[2][] = '\.';
                $key = str_replace($matchs[0], $matchs[2], $key);
                if (preg_match('/'.$key.'$/', $url, $args)){
                    foreach ($matchs[1] as $key => $val){
                        self::$ARGS[$val] = $args[$key+1];
                    }
                    if (preg_match_all("/<(\w+)>/", $value, $matchs)){
                        $replaces = [];
                        foreach ($matchs[1] as &$val){
                            $replaces[] = isset(self::$ARGS[$val]) ? self::$ARGS[$val] : $val;
                        }
                        $value = str_replace($matchs[0], $replaces, $value);
                    }
                    $path = str_replace($args[0], $value, $url);
                    break;
                }
            }
        }
        return $path;
    }

    /**
     * 路由入口
     */
    public function router()
    {
        $isAjax = false;
        if ($pathInfo = $this->getRouterInfo()){
            List($module, $method, $isAjax) = $pathInfo;
            $module = $module ?: $this->routerInfo['base_action'];
        } else {
            $module = $this->routerInfo['base_action'];
            $method = null;
        }
        TXRequest::create($module, $isAjax, $method);
    }
}