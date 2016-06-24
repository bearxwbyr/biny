<?php
/**
 * Base action
 */
class baseAction extends TXAction
{
    protected $needLogin = true;

    public function __construct()
    {
        parent::__construct();
        if (!$this->checkUser()){
            if ($this->needLogin){
                TXApp::$base->session->lastUrl = $_SERVER['REQUEST_URI'];
                echo $this->display('main/login');
                exit;
            }
        }
    }

    /**
     * 验证用户登录
     * @return bool
     */
    public function checkUser()
    {
        $user = TXApp::$base->person;
        if ($user->exist()){
            return true;
        } else {
            return false;
        }

    }

    /**
     * @param $view
     * @param array $array
     * @param array $objects 直接使用参数
     * @return TXResponse
     */
    public function display($view, $array=array(), $objects=array())
    {
        $objects = array_merge(array(
            'webRoot' => TXConfig::getAppConfig('webRoot', 'dns'),
            'CDN_ROOT' => TXConfig::getAppConfig('CDN_ROOT', 'dns'),
        ), $objects);
        return parent::display($view, $array, $objects);
    }
}