<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 16-4-12
 * Time: 下午12:14
 */
class TXSingleCond extends TXCond
{
    /**
     * @var TXSingleDAO
     */
    protected $DAO;

    /**
     * 删除数据
     * @return bool
     */
    public function delete()
    {
        return $this->DAO->delete($this);
    }

    /**
     * 更新数据
     * @param array $sets
     * @return bool
     */
    public function update($sets)
    {
        return $this->DAO->update($sets, $this);
    }

    /**
     * 添加数量 count=count+1
     * @param $sets
     * @return bool|string
     */
    public function addCount($sets)
    {
        return $this->DAO->addCount($sets, $this);
    }
}