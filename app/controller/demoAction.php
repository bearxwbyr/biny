<?php
/**
 * 演示Action
 * @property demoDAO $demoDAO
 */
class demoAction extends baseAction
{
//    // 权限配置
//    protected function privilege()
//    {
//        return array(
//            'login_required' => array(
//                'actions' => '*', //绑定action
//            ),
//        );
//    }

    /**
     * demo首页
     */
    public function action_index()
    {
//        //UV统计
//        $date = date('Y-m-d', time());
//        $rtx = TXApp::$base->person->name;
//        $this->demoDAO->createOrAdd(['date'=>$date, 'rtx'=>$rtx, 'count'=>1], ['count'=>1]);

        $view = $this->display('demo/demo');
        $view->title = "Biny演示页面";
        return $view;
    }
}