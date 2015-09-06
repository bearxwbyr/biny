<?php
/**
 * 主页Action
 * @property testService $testService
 */
class mainAction extends baseAction
{
    public function execute($id=10, $type)
    {
        $iId = $this->getParam('iId', 10);
        TXLogger::info($iId, 'iId');
        $person = $this->testService->test();
        TXLogger::info($person);

        $view = $this->display('main/index', []);
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