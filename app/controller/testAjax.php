<?php
/**
 * Test Action
 */
class testAjax extends TXAjax
{
    public function execute($aaa=10, $bbb)
    {
        TXLogger::info($aaa, 'aaa');
        TXLogger::info($bbb, 'bbb');
        return $this->error("errrrrrror!!!");
    }

    public function action_xxx()
    {
        $params = array(

        );

        $html = $this->display('main/index', $params);
        return $this->correct($html);
    }
}