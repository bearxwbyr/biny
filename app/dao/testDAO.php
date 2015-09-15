<?php
/**
 * 测试表
 */
class testDAO extends baseDAO
{
    protected $dbConfig = ['database', 'slaveDb'];
    protected $table = 'Biny_Test';
    protected $_pk = 'id';
}