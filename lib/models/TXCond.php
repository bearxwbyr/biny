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
    public $where;
    public $limit=array();
    public $orderby=array();
    public $additions=array();
    public $groupby=array();
    public $having=array();

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
     * @param bool $clone
     * @return $this
     */
    public function limit($len, $start=0, $clone=true)
    {
        $cond = $clone ? clone $this : $this;
        $cond->limit = array(intval($start), intval($len));
        return $cond;
    }

    /**
     * 构建order
     * @param $orderby
     * @param bool $clone
     * @return $this
     */
    public function order($orderby, $clone=true)
    {
        $cond = $clone ? clone $this : $this;
        foreach ($orderby as $key => $val){
            if (is_array($val)){
                if (!isset($cond->orderby[$key])){
                    $cond->orderby[$key] = array();
                }
                if (is_string($cond->orderby[$key])){
                    $cond->orderby[$key] = $val;
                } else {
                    foreach ($val as $k => $v){
                        $cond->orderby[$key][$k] = $v;
                    }
                }
            } else {
                $cond->orderby[$key] = $val;
            }
        }
        return $cond;
    }

    /**
     * 构建group
     * @param $groupby
     * @param bool $clone
     * @return $this
     */
    public function group($groupby, $clone=true)
    {
        $cond = $clone ? clone $this : $this;
        foreach ($groupby as $key => $val){
            if (is_array($val)){
                if (!isset($cond->groupby[$key])){
                    $cond->groupby[$key] = array();
                }
                foreach ($val as $k => $v){
                    $cond->groupby[$key][$k] = $v;
                }
            } else {
                $cond->groupby[$key] = $val;
            }
        }
        return $cond;
    }

    /**
     * 构建having
     * @param $having
     * @param bool $clone
     * @return $this
     */
    public function having($having, $clone=true)
    {
        $cond = $clone ? clone $this : $this;
        foreach ($having as $key => $val){
            foreach ($val as $k => $v){
                $cond->having[$key][$k] = $v;
            }
        }
        return $cond;
    }

    /**
     * 构建additions
     * @param $additions
     * @param bool $clone
     * @return $this
     */
    public function addition($additions, $clone=true)
    {
        $cond = $clone ? clone $this : $this;
        foreach ($additions as $key => $val){
            if (is_array($val)){
                if (!isset($cond->additions[$key])){
                    $cond->additions[$key] = array();
                }
                foreach ($val as $k => $v){
                    $cond->additions[$key][$k] = $v;
                }
            } else {
                $cond->additions[$key] = $val;
            }
        }
        return $cond;
    }
}