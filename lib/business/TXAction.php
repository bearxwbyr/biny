<?php
/**
 * Action config class
 */
class TXAction extends TXBase
{
    /**
     * 构造函数
     * @param $params
     */
    public function __construct($params)
    {

        if (isMaintenance){
            echo $this->display('Main/maintenance');
            exit;
        }
        $this->params = $params;
        $this->setCharset();
        $this->setContentType();
    }

    /**
     * Display to template
     * @param $view
     * @param array $params
     * @param array $ignores
     * @return TXResponse
     */
    public function display($view, $params = array(), $ignores=array())
    {
        return new TXResponse($view, $params, $ignores);
    }

    /**
     * @param $msg
     * @return TXResponse
     */
    public function error($msg)
    {
        TXEvent::trigger(onError, array($msg));
        return $this->display('error/msg', ['message'=> $msg]);
    }

    public function redirect($url)
    {
        header("Location:$url");
        exit();
    }

    public function redirectAction($action, $params = array())
    {
        $url = TXRouter::$rootPath.DS.$action. "?" . http_build_query($params);
//        echo $url;
        $this->redirect($url);
    }

    protected function setContentType($contentType='text/html')
    {
        header('Content-type: ' . $contentType);
    }

    protected function setCharset($charset = 'UTF-8')
    {
        header('charset: ' . $charset);
    }
}