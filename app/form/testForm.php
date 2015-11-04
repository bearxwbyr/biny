<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-11-4
 * Time: 下午4:37
 */
class testForm extends TXForm
{
    protected $_values = ['id'=>null, 'name'=>null, 'status'=>1];
    protected $_rules = [
        'id'=>self::typeInt,
        'name'=>self::typeNonEmpty,
        'status'=>'testCmp'
    ];

    public function get_user()
    {
        $this->_values['id'] = 2;
        $this->_rules['id'] = self::typeDate;
    }

    public function valid_testCmp()
    {
        if ($this->status == "2"){
            return $this->correct();
        } else {
            return $this->error();
        }
    }
}