<?php
/**
 * Test Action
 * @property TXSingleDAO $testDAO
 * @property userDAO $userDAO
 * @property projectDAO $projectDAO
 * @property testService $testService
 */

class testAction extends baseAction
{
    protected $csrfValidate = false;

    // 权限配置
    protected function privilege()
    {
        return array(
            'my_required' => array(
                'requires' => [
                    ['actions'=>['index'], 'params'=>[123]],
                    ['actions'=>['form','index'], 'params'=>[234]],
                ],
                'callBack' => [], //验证失败回调函数
            ),
        );
    }

    public function action_index()
    {
        TXEvent::on(onSql);
//        $result = $this->userDAO->filter(array('>'=>array('id'=>10)))
//            ->group(array('type'))->having(array('>='=>array('cash'=>100)))->order(array('id'=>'desc'))
//            ->addition(array('sum'=>array('cash'=>'cash')))
//            ->select('select id,:addition from :table WHERE :where :group :order;');

        $rs = $this->userDAO->join($this->testDAO, ['id'=>'id'])->filter([['<'=>['id'=>50]]])->order([['id'=>'desc']])->cursor();
        while ($data = TXDatabase::step($rs)){
            TXLogger::info($data);
        }

        $data = $this->getParam('test');
        $params = array(
            'test'=>$data
        );
//        TXDatabase::start();
//        $this->testDAO->add(['name'=>'rollback', 'userId'=>10, 'time'=>time(), 'type'=>2]);
//        $this->userDAO->add(['name'=>'rollback']);
//        TXDatabase::commit();
        return $this->display('main/test', $params);

    }

    public function action_form()
    {
        $form = $this->getForm('test');
        TXLogger::info($form->values());
        TXLogger::info($form->check());
        TXLogger::info($form->getError());
        return $this->error();
    }

    public function action_logger()
    {
        return $this->display('main/logger');
    }

    public function action_view($id)
    {
        TXLogger::display($id);exit;
    }
}
