<?php
/**
 * 主页Action
 * @property testService $testService
 */
class mainAction extends baseAction
{
    public function action_index($id=10, $type)
    {
        $arr = $this->testService->test();
        $params = [
            'testArr' => $arr,
            'string' => 'bb<>',
            'src' => 'alert(1)'
        ];
        $view = $this->display('main/test', $params, array('src'=>'<f333>'));
        $view->title = "主页标题";
        return $view;
    }

    public function action_test($id, $type="ss")
    {
        TXLogger::info($id, '$id');
        TXLogger::info($type, '$type');
        return $this->error('出大事了');
    }

    public function ajax_xxx($aaa=10, $bbb)
    {
        TXLogger::info($aaa, 'aaa');
        TXLogger::info($bbb, 'bbb');
        return $this->error("errrrrrror!!!");
    }

    /**
     * @param $event
     * @param array $param
     */
    public function testEvent($event, $param=array())
    {
        TXLogger::info("tigger in beforeAction".$event);
    }

    /**
     * @param $event
     * @param array $param
     */
    public function testEvent2($event, $param=array())
    {
        TXLogger::info("tigger in afterAction".$event);
    }
}