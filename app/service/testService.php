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
        $DAO = $this->testDAO->Join($this->anotherDAO, ['userId'=> $this->anotherDAO->getPk()]);
        $filter1 = $DAO->filter([[], ['<='=>['id'=>2]]]);
//        $filter2 = $DAO->filter([[], ['id'=>2, 'name' => 'billge']]);
//        $filter3 = $filter1->merge($filter2)->filter([['name'=>'dddd', 'id'=>3]]);
        TXLogger::info($filter1->group([['sum'=>['id', 'userId'=>'uId'], 'avg'=>['type']]]));
////        $filter2 = $this->testDAO->filter(['id'=>2, 'name' => 'billge']);
////        $filter3 = $filter1->merge($filter2);
//
////        $filter = $DAO->merge([['name'=>'dddd', 'id'=>3]])->filter([['userId'=>2, 'name' => 'dfdf']])->merge([['name'=>'dddd', 'id'=>3]]);
//
////        return $filter3->query([], [], [['id', 'name']]);
        TXLogger::info($this->testDAO->avg('userId'));
        $result = $this->testDAO->query();
        $array = [
            ['id'=>1, 'type'=>12, 'name'=>'3434s'],
            ['id'=>12, 'type'=>22, 'name'=>'dsfewf'],
            ['id'=>12, 'type'=>37, 'name'=>'sss'],
            ['id'=>4, 'type'=>38, 'name'=>'fdf'],
        ];
        TXLogger::info($this->sortArray($array, ['id'=>SORT_ASC, 'type'=>SORT_ASC]));
        return $result;
    }
}