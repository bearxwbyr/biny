<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-8-3
 * Time: 上午11:50
 * @method TXSingleCond group($groupby)
 * @method TXSingleCond having($having)
 * @method TXSingleCond limit($len, $start=0)
 * @method TXSingleCond order($orderby)
 * @method TXSingleCond addition($additions)
 */
class TXSingleFilter extends TXFilter
{
    /**
     * @var TXSingleDAO
     */
    protected $DAO;
    protected $conds = [];

    /**
     * and 操作
     * @param $cond
     * @return TXSingleFilter
     */
    public function filter($cond=array())
    {
        return $cond ? new self($this->DAO, $cond, "__and__", $this->conds[0]) : $this;
    }

    /**
     * or 操作
     * @param $cond
     * @return TXSingleFilter
     */
    public function merge($cond)
    {
        return $cond ? new self($this->DAO, $cond, "__or__", $this->conds[0]) : $this;
    }

    /**
     * 删除数据
     * @return bool
     */
    public function delete()
    {
        $cond = new TXSingleCond($this->DAO);
        $cond->setWhere($this->buildWhere($this->conds));
        return $cond->delete();
    }

    /**
     * 更新数据
     * @param array $sets
     * @return bool
     */
    public function update($sets)
    {
        $cond = new TXSingleCond($this->DAO);
        $cond->setWhere($this->buildWhere($this->conds));
        return $cond->update($sets);
    }

    /**
     * 添加数量 count=count+1
     * @param $sets
     * @return bool|string
     */
    public function addCount($sets)
    {
        $cond = new TXSingleCond($this->DAO);
        $cond->setWhere($this->buildWhere($this->conds));
        return $cond->addCount($sets);
    }


}