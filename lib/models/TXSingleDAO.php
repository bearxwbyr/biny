<?php
/**
 * 数据库
 */
class TXSingleDAO extends TXDAO
{
    const FETCH_TYPE_ALL = 0;
    const FETCH_TYPE_ONE = 1;
    /**
     * 表格名称
     * @var string
     */
    protected $table;

    private $s_table;

    private $database = null;

    public function __construct()
    {
        $this->s_table = $this->table;
        $this->setDbTable($this->table);
    }

    protected function setDbTable($table)
    {
        if (null === $this->database){
            if (is_string($this->dbConfig) && $db = TXConfig::getAppConfig($this->dbConfig, 'dns')['database']){
                $this->database = $db;
            } else if (is_array($this->dbConfig)){
                $master = TXConfig::getAppConfig($this->dbConfig[0], 'dns')['database'];
                $slave = TXConfig::getAppConfig($this->dbConfig[1], 'dns')['database'];
                if ($master === $slave){
                    $this->database = $master;
                } else {
                    throw new TXException(1018, array($slave, $master));
                }
            }
        }
        $this->table = $this->database.".`{$table}`";
    }

    /**
     * 返回表名
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * 纯表名
     * @return string
     */
    public function tableName()
    {
        return $this->s_table;
    }

    /**
     * 选分表
     * @param $id
     * @return $this
     */
    public function choose($id)
    {
        $this->setDbTable($this->s_table.$id);
        return $this;
    }

    /**
     * 表是否存在
     * @return bool
     */
    public function exist()
    {
        return $this->isExist();
    }

    /**
     * 链接表
     * @param $dao TXSingleDAO
     * @param $relate
     * @param string $type
     * @return $this|TXDoubleDAO
     * @throws TXException
     */
    protected function _join($dao, $relate, $type='join')
    {
        $selfClass = substr(get_called_class(), 0, -3);
        $relateClass = substr(get_class($dao), 0, -3);
        if ($selfClass == $relateClass){
            return $this;
        }
        if (!$this->checkConfig($dao)){
            throw new TXException(1012, "DAOs must be the same Host");
        }
        $DAOs = [
            $selfClass => $this->table,
            $relateClass => $dao->table
        ];
        $relates = [];
        $join = [];
        foreach ($relate as $key => $value){
            $join[$selfClass.".".$key] = $relateClass.".".$value;
        }
        $relates[] = [$type => $join];
        return new TXDoubleDAO($DAOs, $relates, $this->dbConfig);
    }

    /**
     * 拼装Limit
     * @param $limit
     * @return string
     */
    protected function buildLimit($limit)
    {
        if (empty($limit)) {
            return '';
        } else {
            return sprintf(' LIMIT %d,%d', $limit[0], $limit[1]);
        }
    }

    /**
     * 拼接fields
     * @param $fields
     * @param array $group
     * @return string
     */
    protected function buildFields($fields, $group=array()){
        if (is_array($fields)){
            foreach ($fields as &$field){
                $field = $this->real_escape_string($field);
            }
            unset($field);
            $fields = '`'.join('`,`', $fields).'`';
        }
        if ($group){
            if ($fields){
                $groups = [$fields];
            } else {
                $groups = [];
            }
            foreach ($group as $key => $values){
                if (!in_array(strtolower($key), $this->calcs)){
                    continue;
                }
                foreach ($values as $k => $value){
                    $value = $this->real_escape_string($value);
                    if (is_string($k)){
                        $k = $this->real_escape_string($k);
                        $groups[] = "{$key}(`{$k}`) as '{$value}'";
                    } else {
                        $groups[] = "{$key}(`{$value}`) as '{$value}'";
                    }
                }
            }
            return join(',', $groups);
        }
        return $fields;
    }

    /**
     * 拼装Sets
     * @param $set
     * @return string
     */
    protected function buildSets($set)
    {
        $sets = array();
        foreach($set as $key => $value) {
            $key = $this->real_escape_string($key);
            if ($value === null) {
                $sets[] = "`{$key}`=NULL";
            }
            elseif (is_string($value)) {
                $value = $this->real_escape_string($value);
                $sets[] = "`{$key}`='{$value}'";
            } else {
                $sets[] = "`{$key}`={$value}";
            }
        }
        return join(', ', $sets);
    }

    /**
     * count=count+1
     * @param $set
     * @return string
     */
    protected function buildCount($set)
    {
        $sets = array();
        foreach($set as $key => $value) {
            if (!is_int($value) || $value <= 0 ) {
                continue;
            }
            $key = $this->real_escape_string($key);
            $sets[] = "`{$key}`=`{$key}`+{$value}";
        }
        return join(', ', $sets);
    }

    /**
     * 组合groupBy
     * @param $groupBy
     * @param array $having
     * @return string
     */
    protected function buildGroupBy($groupBy, $having=array())
    {
        if (!$groupBy){
            return '';
        }
        if (is_array($groupBy)){
            foreach ($groupBy as &$group){
                $group = $this->real_escape_string($group);
            }
            unset($group);
            $groupBy = '`'.join('`,`', $groupBy).'`';
        }
        if ($having){
            $havings = [];
            foreach ($having as $ys => $value){
                if (!in_array(strtolower($ys), $this->extracts)){
                    continue;
                }
                foreach ($value as $arrk => $arrv){
                    $arrk = $this->real_escape_string($arrk);
                    if (is_null($arrv)){
                        $havings[] = "`{$arrk}`{$ys} NULL";
                    }elseif (is_string($arrv)){
                        $arrv = $this->real_escape_string($arrv);
                        $havings[] = "`{$arrk}`{$ys}'{$arrv}'";
                    } else {
                        $havings[] = "`{$arrk}`{$ys}{$arrv}";
                    }
                }
            }
            if ($havings){
                $groupBy .= " HAVING ".join(' AND ', $havings);
            }
        }
        return ' GROUP BY '.$groupBy;
    }

    /**
     * 拼装Insert
     * @param $sets
     * @internal param $data
     * @return string $fileds
     */
    protected function buildInsert($sets)
    {
        $field = array();
        $value = array();
        foreach ($sets as $key => $val){
            $field[] = "`{$this->real_escape_string($key)}`";
            if ($val === null) {
                $value[] = "NULL";
            }
            elseif (is_string($val)) {
                $val = $this->real_escape_string($val);//mysqli_real_escape_string(null, $val);
                $value[] = "'{$val}'";
            } else {
                $value[] = "{$val}";
            }
        }
        $fields = '('.join(',', $field).') VALUES('.join(',', $value).')';
        return $fields;
    }

    /**
     * 拼装orderby
     * @param $orderBy
     * @return string
     */
    protected function buildOrderBy($orderBy)
    {
        $orders = array();
        foreach ($orderBy as $key => $val){
            $key = $this->real_escape_string($key);
            if (is_array($val)){
                $asc = isset($val[0]) ? $val[0] : 'ASC';
                $code = isset($val[1]) ? $val[1] : 'gbk';
                if (!in_array(strtoupper($asc), array('ASC', 'DESC'))){
                    TXLogger::error("order must be ASC/DESC, {$asc} given", 'sql Error');
                    continue;
                }
                $orders[] = "CONVERT(`{$key}` USING {$code}) $asc";
            } else {
                if (!in_array(strtoupper($val), array('ASC', 'DESC'))){
                    TXLogger::error("order must be ASC/DESC, {$val} given", 'sql Error');
                    continue;
                }
                $orders[] = '`'.$key."` ".$val;
            }
        }
        if ($orders){
            return ' ORDER BY '.join(',', $orders);
        } else {
            return '';
        }
    }

    /**
     * 更新数据
     * @param array $sets
     * @return bool
     */
    public function update($sets)
    {
        $params = func_get_args();
        $where = (isset($params[1]) && $params[1]) ? " WHERE ".$params[1] : "";
        $set = $this->buildSets($sets);
        $sql = sprintf("UPDATE %s SET %s%s", $this->table, $set, $where);
//        echo $sql; exit;

        return $this->execute($sql);
    }

    /**
     * 添加数据
     * @param $sets
     * @param bool $id
     * @return int
     */
    public function add($sets, $id=true)
    {
        $fields = $this->buildInsert($sets);
        $sql = sprintf("INSERT INTO %s %s", $this->table, $fields);
        return $this->execute($sql, $id);
    }

    /**
     * 批量添加
     * @param $fields
     * @param $values
     * @return bool|string
     */
    public function addList($fields, $values)
    {
        if (is_array($fields)){
            foreach ($fields as &$field){
                $field = $this->real_escape_string($field);
            }
            unset($field);
        }
        $fields = $fields ? '(`'.join('`,`', $fields).'`)' : "";
        $columns = array();
        foreach ($values as $value){
            foreach ($value as &$val){
                $val = is_string($val) ? "'{$this->real_escape_string($val)}'" : $val;
            }
            unset($val);
            $columns[] = '('.join(',', $value).')';
        }
        $columns = join(',', $columns);
        $sql = sprintf("INSERT INTO %s %s VALUES  %s", $this->table, $fields, $columns);
//        \TXLogger::info($sql);
        return $this->execute($sql, false);
    }

    /**
     * 删除数据
     * @return bool
     */
    public function delete()
    {
        $params = func_get_args();
        $where = (isset($params[0]) && $params[0]) ? " WHERE ".$params[0] : "";
        $sql = sprintf("DELETE FROM %s%s", $this->table, $where);

        return $this->execute($sql);
    }

    /**
     * 添加数量 count=count+1
     * @param $sets
     * @return bool|string
     */
    public function addCount($sets)
    {
        $params = func_get_args();
        $where = (isset($params[1]) && $params[1]) ? " WHERE ".$params[1] : "";
        $set = $this->buildCount($sets);
        $sql = sprintf("UPDATE %s SET %s%s", $this->table, $set, $where);
//        \TXLogger::info($sql);
        return $this->execute($sql);
    }

    /**
     * 更新数据或者插入数据
     * @param $inserts
     * @param $sets
     * @return bool|int|mysqli_result|string
     */
    public function createOrUpdate($inserts, $sets)
    {
        $set = $this->buildSets($sets);
        $fields = $this->buildInsert($inserts);
        $sql = sprintf("INSERT INTO %s %s ON DUPLICATE KEY UPDATE %s", $this->table, $fields, $set);
//        \TXLogger::info($sql);
        return $this->execute($sql, true);
    }

    /**
     * 更新数据或者插入数据
     * @param $inserts
     * @param $adds
     * @return bool|int|mysqli_result|string
     */
    public function createOrAdd($inserts, $adds)
    {
        $set = $this->buildCount($adds);
        $fields = $this->buildInsert($inserts);
        $sql = sprintf("INSERT INTO %s %s ON DUPLICATE KEY UPDATE %s", $this->table, $fields, $set);
//        \TXLogger::info($sql);
        return $this->execute($sql, true);
    }


    /**
     * and 操作
     * @param $cond
     * @return TXSingleFilter
     */
    public function filter($cond=array())
    {
        return $cond ? new TXSingleFilter($this, $cond, "__and__") : $this;
    }

    /**
     * or 操作
     * @param $cond
     * @return TXSingleFilter
     */
    public function merge($cond=array())
    {
        return $cond ? new TXSingleFilter($this, $cond, "__or__") : $this;
    }
}