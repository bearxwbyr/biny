<?php
/**
 * Base action
 *  @property sessionService $sessionService
 */
abstract class baseAction extends TXAction
{
    /**
     * 验证登录
     * @param $params
     */
    public function __construct($params)
    {
        parent::__construct($params);
//        $person = $this->checkUser();
//        if (!$person){
//            if (!$this->isAjax){
//                $this->SessionService->setLastURL($_SERVER['REQUEST_URI']);
//                $param = array('type'=>'error', 'msg' => "用户未登录");
//                echo $this->display('Main/msg', $param);
//            } else {
//                echo $this->error("用户未登录，请登录后再试");
//            }
//            exit;
//        } else if (!$this->isAjax && $lastUrl = $this->SessionService->getLastURL()){
//            $this->sessionService->clearLastURL();
//            $this->redirect($lastUrl);
//        }
    }

    /**
     * 验证用户登录
     * @return bool
     */
    public function checkUser()
    {
        $user = $this->getUser();
        if (!$user){
            return false;
        } else {
            return true;
        }

    }

    /**
     * 获得当前用户信息
     * @return Person
     */
    protected function getUser()
    {
        return Person::get(1);
    }

    private function getRootPath()
    {
        return TXConfig::getAppConfig('rootPath', 'dns');
    }

    /**
     * @param $view
     * @param array $array
     * @param array $ignores 无需实体化转义
     * @return TXResponse
     */
    public function display($view, $array = array(), $ignores=array())
    {
        $array = array_merge(array(
            'isAjax' => $this->isAjax,
            'rootPath' => $this->getRootPath()
        ), $array);
        return parent::display($view, $array, $ignores);
    }
}