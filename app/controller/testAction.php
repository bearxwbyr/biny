<?php
/**
 * Test Action
 */
class testAction extends baseAction
{
    public function execute($aaa=10, $bbb)
    {
        TXLogger::info($aaa, 'aaa');
        TXLogger::info($bbb, 'bbb');
        $person = $this->getUser();
        TXLogger::info($person->getProject());

        $params = array(

        );

        return $this->display('main/index', $params);

    }

    public function action_form()
    {
        $form = $this->getForm('test', 'get_user');
        TXLogger::info($form->values());
        TXLogger::info($form->check());
        TXLogger::info($form->status);
        return $this->display('main/index', []);
    }
}