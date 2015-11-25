<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-7-28
 * Time: 下午5:37
 */
class Person extends TXModel
{
    private static $_cache = [];

    protected $_data;
    protected $DAO;
    protected $_pk;

    public static function get($id)
    {
        if (!array_key_exists($id, self::$_cache)){
            self::$_cache[$id] = new self($id);
        }
        return self::$_cache[$id];
    }

    private function __construct($id)
    {
        $this->DAO = TXFactory::create('userDAO');
        $this->_data = $this->DAO->getByPk($id);
        $this->_pk = $id;
    }

    public function getProject()
    {
        $projectDAO = TXFactory::create('userDAO');
        return $projectDAO->getByPk($this->projectId);

    }
}