<?php
/**
 * Test Action
 * @property testDAO $testDAO
 * @property userDAO $userDAO
 * @property projectDAO $projectDAO
 * @property testService $testService
 */

class testAction extends baseAction
{
    protected $csrfValidate = false;

    protected $privilege = array(
        'login_required' => array(
            'actions' => '*', //绑定action
            'params' => [],   //传参
            'callBack' => [], //验证失败回调函数
        ),
    );

    public function action_index()
    {
        $data = $this->getParam('test');
        $params = array(
            'test'=>$data
        );
        return $this->display('main/test', $params);

    }

    public function ajax_form()
    {
        $form = $this->getForm('test');
        TXLogger::info($form->values());
        TXLogger::info($form->check());
        TXLogger::info($form->getError());
        return $this->correct();
    }

    public function action_view($id)
    {
        TXLogger::display($id);exit;
    }

    public function action_mail()
    {
        var_dump(TXCommon::sendMail(array('billge@tencent.com'), 'test', 'dfdfdfdfd'));
    }
}
