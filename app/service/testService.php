<?php
/**
 * Test service
 * @author billge
 * @property testDAO $testDAO
 * @property userDAO $userDAO
 * @property projectDAO $projectDAO
 */
class testService extends baseService
{
    public function test()
    {
        $DAO = $this->testDAO->leftJoin($this->userDAO, ['userId'=> $this->userDAO->getPk()]);
        $filter1 = $DAO->filter([['name'=>'dddd', 'id'=>["d',sleep(10),fe", 3]]]);
//        $filter2 = $DAO->filter([[], ['id'=>2, 'name' => 'billge']]);
//        $filter3 = $filter1->merge($filter2)->filter([['name'=>'dddd', 'id'=>3]]);
        TXLogger::info($filter1->query());
//        $filter2 = $this->testDAO->filter(['id'=>2, 'name' => 'billge']);
//        $filter3 = $filter1->merge($filter2);

//        $filter = $DAO->merge([['name'=>'dddd', 'id'=>3]])->filter([['userId'=>2, 'name' => 'dfdf']])->merge([['name'=>'dddd', 'id'=>3]]);

//        return $filter3->query([], [], [['id', 'name']]);
        return $this->testDAO->sum('id');
    }
}