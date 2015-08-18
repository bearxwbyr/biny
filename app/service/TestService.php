<?php
/**
 * Test service
 * @author billge
 * @property TestDAO $TestDAO
 * @property UserDAO $UserDAO
 * @property ProjectDAO $ProjectDAO
 */
class TestService extends BaseService
{
    public function test()
    {
//        $DAO = $this->TestDAO->leftJoin($this->UserDAO, ['userId'=> $this->UserDAO->getPk()]);
//        $filter1 = $DAO->filter([['name'=>'dddd', '>'=>['id'=>1]]]);
//        $filter2 = $DAO->filter([[], ['id'=>2, 'name' => 'billge']]);
//        $filter3 = $filter1->merge($filter2)->filter([['name'=>'dddd', 'id'=>3]]);
        $filter1 = $this->TestDAO->filter(['__like__'=>['id'=>'d_f%efe', 'name'=>'dfe'], '>='=>['id'=>1, 'name'=>0]]);
//        $filter2 = $this->TestDAO->filter(['id'=>2, 'name' => 'billge']);
//        $filter3 = $filter1->merge($filter2);

//        $filter = $DAO->merge([['name'=>'dddd', 'id'=>3]])->filter([['userId'=>2, 'name' => 'dfdf']])->merge([['name'=>'dddd', 'id'=>3]]);

//        return $filter3->query([], [], [['id', 'name']]);
        return $filter1->group(['name'], ['count'=>['id'=>'s_id']], ['name'],
            ['>='=>['s_id'=>2]], [0, 2], ['name'=>['asc', 'utf8'], 's_id'=>'desc']);
    }
}