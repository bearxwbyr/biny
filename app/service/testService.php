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
//        $fields = array('name', 'time', 'maxCount', 'type');
//        $values = array();
//        for ($i=0; $i<10; $i++){
//            $values[] = array(md5(rand()), rand(), rand(), rand());
//        }
//        \TXLogger::log($this->projectDAO->addList($fields, $values));
////        $DAO = $this->testDAO->Join($this->anotherDAO, ['userId'=> $this->anotherDAO->getPk()]);
////        TXLogger::info($this->testDAO->query([], ['id'=>'desc']));
////        $filter2 = $DAO->filter([[], ['id'=>2, 'name' => 'billge']]);
////        $filter3 = $filter1->merge($filter2)->filter([['name'=>'dddd', 'id'=>3]]);
////        TXLogger::info($filter1->group([['sum'=>['id', 'userId'=>'uId'], 'avg'=>['type']]]));
////        $filter2 = $this->testDAO->filter(['id'=>2, 'name' => 'billge']);
////        $filter3 = $filter1->merge($filter2);
////
////        $filter = $DAO->merge([['name'=>'dddd', 'id'=>3]])->filter([['userId'=>2, 'name' => 'dfdf']])->merge([['name'=>'dddd', 'id'=>3]]);
////
////        return $filter3->query([], [], [['id', 'name']]);
////        $result = $this->testDAO->query();
////        if ($result()){
////            TXLogger::info($result, 'data');
////        } else {
////            TXLogger::info($result, 'empty');
////        }
//        $this->testDAO->filter(['id'=>2])->count();
        $result = $this->testDAO->getByPk([1, 2]);
        return $result;
    }
}