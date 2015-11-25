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
//        TXLogger::info($this->testDAO->query([], ['id'=>'desc']));
//        $DAO = $this->testDAO->Join($this->anotherDAO, ['userId'=> $this->anotherDAO->getPk()]);
        $filter1 = $this->testDAO->filter(['id'=>2]);
//        $filter2 = $this->testDAO->filter(['name' => 'billge']);
//        $filter3 = $filter1->merge($filter2);
        TXEvent::one([TXLogger::instance(), 'event'], 'onSql');
        $result = $filter1->query([], ['id'=>'desc'], ['id', 'name']);
        $result = $filter1->query([], ['id'=>'desc'], ['id', 'name']);
        $result = $filter1->query([], ['id'=>'desc'], ['id', 'name']);
////
////        $filter = $DAO->merge([['name'=>'dddd', 'id'=>3]])->filter([['userId'=>2, 'name' => 'dfdf']])->merge([['name'=>'dddd', 'id'=>3]]);
////
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