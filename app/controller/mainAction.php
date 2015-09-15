<?php
/**
 * 主页Action
 * @property testService $testService
 */
class mainAction extends baseAction
{
    public function execute($id=10, $type)
    {
        $arr = $this->testService->test();
        TXLogger::info($arr);

        $params = [
            'testArr' => $arr,
            'string' => 'bb<>'
        ];
        $view = $this->display('main/test', $params);
        $view->title = "主页标题";
        return $view;
    }

    public function action_test($id, $type="ss")
    {
        TXLogger::info($id, '$id');
        TXLogger::info($type, '$type');
        return $this->error('出大事了');
    }
}