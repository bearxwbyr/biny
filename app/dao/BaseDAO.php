<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-7-30
 * Time: 下午7:55
 */

class BaseDAO extends TXSingleDAO
{
    protected $_pk;

    public function getPk()
    {
        return $this->_pk;
    }

    /**
     * 组合PK
     * @param $pk
     * @return array
     */
    private function buildPK($pk)
    {
        if (is_array($this->_pk)){
            return array_combine($this->_pk, $pk);
        } else {
            return [$this->_pk => $pk];
        }
    }

    /**
     * 获取主键
     * @param $pk
     * @return array|bool|int|string
     */
    public function getByPk($pk)
    {
        $cond = $this->buildPK($pk);
        return $this->filter($cond)->find();
    }

    /**
     * 更新主键
     * @param $pk
     * @param $sets
     * @return bool
     */
    public function updateByPk($pk, $sets)
    {
        $cond = $this->buildPK($pk);
        return $this->filter($cond)->update($sets);
    }

    /**
     * 删除主键
     * @param $pk
     * @return bool
     */
    public function deleteByPk($pk)
    {
        $cond = $this->buildPK($pk);
        return $this->filter($cond)->delete();
    }

    /**
     * count = count+2
     * @param $pk
     * @param $sets ["num"=>3]
     * @return bool|string
     */
    public function addCountByPk($pk, $sets)
    {
        $cond = $this->buildPK($pk);
        return $this->filter($cond)->addCount($sets);
    }

}