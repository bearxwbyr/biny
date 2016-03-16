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
        TXEvent::one('onSql');
        $DAO = $this->userDAO;
        $result = $DAO
            ->addition(array('avg'=>array('cash'=>'a_c')))
            ->group(array('projectId'))
            ->having(array('>'=>array('a_c'=>10)))
            ->order(array('name'=>array('desc', 'gbk')))->order(array('name'=>'asc'))
            ->limit(10)->query(array('projectId'));
        TXLogger::info($result);
        $test = $this->testDAO->query();
        return $test;
    }
}