<?php
/**
 * 主页Action
 * @property TestService $TestService
 */
class IndexAction extends BaseAction
{
    public function execute($id=10, $type)
    {
        $iId = $this->getParam('iId', 10);
        TXLogger::info($iId, 'iId');
        $person = $this->TestService->test();
        TXLogger::info($person);
        $params = array(

        );

        return $this->display('Main/index', $params);
    }
}