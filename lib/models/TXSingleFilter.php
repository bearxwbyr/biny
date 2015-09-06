<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-8-3
 * Time: 上午11:50
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
    public function filter($cond)
    {
        return new self($this->DAO, $cond, "__and__", $this->conds[0]);
    }

    /**
     * or 操作
     * @param $cond
     * @return TXSingleFilter
     */
    public function merge($cond)
    {
        return new self($this->DAO, $cond, "__or__", $this->conds[0]);
    }

    /**
     * 删除数据
     * @return bool
     */
    public function delete()
    {
        $where = $this->buildWhere($this->conds);
        return $this->DAO->delete($where);
    }

    /**
     * 更新数据
     * @param array $sets
     * @return bool
     */
    public function update($sets)
    {
        $where = $this->buildWhere($this->conds);
        return $this->DAO->update($sets, $where);
    }

    /**
     * 添加数量 count=count+1
     * @param $sets
     * @return bool|string
     */
    public function addCount($sets)
    {
        $where = $this->buildWhere($this->conds);
        return $this->DAO->addCount($sets, $where);
    }


}