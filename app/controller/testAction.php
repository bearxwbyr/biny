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

        $ids = array_keys($this->projectDAO
            ->rightJoin($this->testDAO, ['type'=>'type'])
            ->filter([['name'=>'test'], ['>='=>['time'=>time()]]])
            ->query([['id'=>'projectId']], 'projectId'));
        $this->userDAO->filter(['projectId'=>[2,3,4]])->query();


        $this->userDAO
            ->leftJoin($this->projectDAO, ['projectId'=>'id'])
            ->rightJoin($this->testDAO, [[], ['type'=>'type']])
            ->filter([[], ['name'=>'test'], ['>='=>['time'=>time()]]])
            ->query(['*']);

        $result = $this->userDAO->filter(['>='=>['id'=>20], 'count'=>2])->limit(2)->query();
        TXLogger::info($result);
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
        \Biny\Logger::info(['test'=>2]);
        return $this->display('main/logger');
    }

    public function action_view($id)
    {
        TXLogger::display($id);exit;
    }
}
