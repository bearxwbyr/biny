<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 16-4-12
 * Time: 下午12:11
 * @method int sum($field)
 * @method int max($field)
 * @method int min($field)
 * @method int avg($field)
 * @method array distinct($field)
 * @method array find($field='')
 * @method array count($field='')
 */
class TXCond
{
    private $DAO;
    protected $where;
    protected $limit=array();
    protected $orderby=array();
    protected $additions=array();
    protected $groupby=array();
    protected $having=array();

    protected $methods = ['distinct', 'find', 'count'];
    protected $calcs = ['max', 'min', 'sum', 'avg', 'count'];

    /**
     * 构造函数
     * @param TXDAO $DAO
     */
    public function __construct($DAO)
    {
        $this->DAO = $DAO;
    }

    /**
     * 设置wheres
     * @param $where
     */
    public function setWhere($where)
    {
        $this->where = $where;
    }

    /**
     * 获取字段
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->$key;
    }

    /**
     * 查询条件
     * @param $method
     * @param $args
     * @return mixed
     * @throws TXException
     */
    public function __call($method, $args)
    {
        if (in_array($method, $this->methods) || in_array($method, $this->calcs)){
            $args = $args ? $args : [''];
            $args[] = $this;
            return call_user_func_array([$this->DAO, $method], $args);
        } else {
            throw new TXException(3009, array($method, __CLASS__));
        }
    }

    /**
     * query
     * @param string $field
     * @param null $key
     * @return array
     */
    public function query($field='', $key=null)
    {
        return $this->DAO->query($field, $key, $this);
    }

    /**
     * 构建limit
     * @param $len
     * @param int $start
     * @return $this
     */
    public function limit($len, $start=0)
    {
        $this->limit = array(intval($start), intval($len));
        return $this;
    }

    /**
     * 构建order
     * @param $orderby
     * @return $this
     */
    public function order($orderby)
    {
        foreach ($orderby as $key => $val){
            if (is_array($val)){
                if (!isset($this->orderby[$key])){
                    $this->orderby[$key] = array();
                }
                if (is_string($this->orderby[$key])){
                    $this->orderby[$key] = $val;
                } else {
                    foreach ($val as $k => $v){
                        $this->orderby[$key][$k] = $v;
                    }
                }
            } else {
                $this->orderby[$key] = $val;
            }
        }
        return $this;
    }

    /**
     * 构建group
     * @param $groupby
     * @return $this
     */
    public function group($groupby)
    {
        foreach ($groupby as $key => $val){
            if (is_array($val)){
                if (!isset($this->groupby[$key])){
                    $this->groupby[$key] = array();
                }
                foreach ($val as $k => $v){
                    $this->groupby[$key][$k] = $v;
                }
            } else {
                $this->groupby[$key] = $val;
            }
        }
        return $this;
    }

    /**
     * 构建having
     * @param $having
     * @return $this
     */
    public function having($having)
    {
        foreach ($having as $key => $val){
            foreach ($val as $k => $v){
                $this->having[$key][$k] = $v;
            }
        }
        return $this;
    }

    /**
     * 构建additions
     * @param $additions
     * @return $this
     */
    public function addition($additions)
    {
        foreach ($additions as $key => $val){
            if (is_array($val)){
                if (!isset($this->additions[$key])){
                    $this->additions[$key] = array();
                }
                foreach ($val as $k => $v){
                    $this->additions[$key][$k] = $v;
                }
            } else {
                $this->additions[$key] = $val;
            }
        }
        return $this;
    }
}