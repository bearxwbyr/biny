<?php
/**
 * Test service
 * @author billge
 * @property testDAO $testDAO
 * @property userDAO $userDAO
 * @property projectDAO $projectDAO
 * @property anotherDAO $anotherDAO
 */
class testService extends baseService
{
    public function test()
    {
//        TXEvent::on(onSql);
//        $this->userDAO->filter(['id'=>1])->update(['name'=>'xx']);
//        $DAO = $this->userDAO->leftJoin($this->projectDAO, ['projectId'=>'id'])->leftJoin($this->testDAO, [['id'=>'id']]);
//        $result = $DAO->filter([[], ['id'=>[1,2,3]]])
//            ->addition([['avg'=>['cash'=>'a_c']]])
//            ->group([['projectId']])
//            ->having(['>'=>['a_c'=>10]])
//            ->order([['name'=>'asc']])
//            ->limit(10)->query([['projectId']]);
//        TXLogger::info($result);
        $test = $this->userDAO->query();
        return $test;
    }
}