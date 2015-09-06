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

        $params = array(

        );

        $html = $this->display('main/index', $params);
        return $this->correct($html);

    }

    public function xxx()
    {
        return $this->error("errrrrrror!!!");
    }
}