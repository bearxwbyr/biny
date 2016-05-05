<?php
/**
 * Test Action
 */
class testAction extends baseAction
{
    public function action_index()
    {
        TXConfig::getConfig('config');
        $params = array(

        );
//        TXLogger::display("dfsfasdf");
//        return $this->display('test/test', $params);

    }

    public function action_form()
    {
        $form = $this->getForm('test');
        TXLogger::info($form->values());
        TXLogger::info($form->check());
        TXLogger::info($form->status);
        return $this->display('main/index', []);
    }
}
