<?php
/**
 * 演示Action
 * @property demoDAO $demoDAO
 */
class demoAction extends baseAction
{
    /**
     * demo首页
     */
    public function action_index()
    {
        //UV统计
        $date = date('Y-m-d', time());
        $code_id = TXApp::$base->request->getCookie('code_user_name') ?: '';
        $rtx = TXApp::$base->request->getCookie('tof_login_username') ?: TXApp::$base->request->getCookie('t_uid');
        $pk = [$date, $code_id];
        if ($data = $this->demoDAO->getByPk($pk)){
            if ($code_id && !isset($data['rtx']) && $rtx){
                $this->demoDAO->updateByPk($pk, ['rtx'=>$rtx, 'count'=>$data['count']+1]);
            } else {
                $this->demoDAO->addCountByPk($pk, ['count'=>1]);
            }
        } else {
            $sets = array('date'=>$date, 'code_id'=>$code_id ?: '', 'rtx'=>$rtx, 'count'=>1);
            $this->demoDAO->add($sets, false);
        }

        $view = $this->display('demo/demo');
        $view->title = "Biny演示页面";
        return $view;
    }
}