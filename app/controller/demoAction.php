<?php
/**
 * 演示Action
 * @property testService $testService
 */
class demoAction extends baseAction
{
    /**
     * demo首页
     */
    public function action_index()
    {
        $view = $this->display('demo/demo');
        $view->title = "Biny演示页面";
        return $view;
    }
}