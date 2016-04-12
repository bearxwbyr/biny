<?php
/**
 * 数据库
 * @method TXSingleCond group($groupby)
 * @method TXSingleCond having($having)
 * @method TXSingleCond order($orderby)
 * @method TXSingleCond addition($additions)
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
                    throw new TXException(3008, array($slave, $master));
                }
            }
        }
        $this->table = $this->database.".`{$table}`";
    }

    /**
     * 返回Log
     * @return string
     */
    public function getDAO()
    {
        return $this->table;
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
            throw new TXException(3002, "DAOs must be the same Host");
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
     * buildWhere
     * @param $cond
     * @param string $type
     * @return string
     */
    public function buildWhere($cond, $type='and')
    {
        if (empty($cond)) {
            return '';
        } else {
            $where = array();
            foreach($cond as $key => $value) {
                $key = $this->real_escape_string($key);
                if (in_array(strtolower($key), $this->extracts)){
                    foreach ($value as $arrk => $arrv){
                        $arrk = $this->real_escape_string($arrk);
                        if (is_null($arrv)){
                            $where[] = "`{$arrk}`{$key} NULL";
                        }elseif (is_string($arrv)){
                            $arrv = $this->real_escape_string($arrv);
                            $where[] = "`{$arrk}`{$key}'{$arrv}'";
                        } else {
                            $where[] = "`{$arrk}`{$key}{$arrv}";
                        }
                    }
                } elseif ($key === '__like__'){
                    foreach ($cond[$key] as $arrk => $arrv){
                        $arrk = $this->real_escape_string($arrk);
                        if (is_array($arrv)){
                            foreach ($arrv as $like){
                                $like = $this->real_like_string($like);
                                if (substr($like, 0, 1) !== '^'){
                                    $like = '%'.$like;
                                }
                                if (substr($like, -1, 1) !== '$'){
                                    $like .= '%';
                                }
                                $like = trim($like, "^$");
                                $where[] = "`{$arrk}` like '{$like}'";
                            }
                        } else {
                            $arrv = $this->real_like_string($arrv);
                            if (substr($arrv, 0, 1) !== '^'){
                                $arrv = '%'.$arrv;
                            }
                            if (substr($arrv, -1, 1) !== '$'){
                                $arrv .= '%';
                            }
                            $arrv = trim($arrv, "^$");
                            $where[] = "`{$arrk}` like '{$arrv}'";
                        }
                    }
                } elseif (is_null($value)){
                    $where[] = "`{$key}`is NULL";
                } elseif (is_string($value)) {
                    $value = $this->real_escape_string($value);
                    $where[] = "`{$key}`='{$value}'";
                } elseif (is_array($value)){
                    if (!$value){
                        $where[] = 'FALSE';
                        continue;
                    }
                    foreach ($value as &$val){
                        if (is_string($val)){
                            $val = "'{$this->real_escape_string($val)}'";
                        }
                    }
                    unset($val);
                    $value = "(". join(',', $value).")";
                    $where[] = "`{$key}` in {$value}";
                } else {
                    $where[] = "`{$key}`={$value}";
                }
            }

            return join(" {$type} ", $where);
        }
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
     * @throws TXException
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
                    throw new TXException(3011, array($key));
                }
                foreach ($values as $k => $value){
                    $value = $this->real_escape_string($value);
                    if (is_string($k)){
                        $k = $this->real_escape_string($k);
                        if ($key == 'distinct'){
                            $groups[] = "COUNT(DISTINCT `{$k}`) as '{$value}'";
                        } else {
                            $groups[] = "{$key}(`{$k}`) as '{$value}'";
                        }
                    } else {
                        if ($key == 'distinct'){
                            $groups[] = "COUNT(DISTINCT `{$value}`) as '{$value}'";
                        } else {
                            $groups[] = "{$key}(`{$value}`) as '{$value}'";
                        }
                    }
                }
            }
            return join(',', $groups);
        }
        return $fields ?: "*";
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
     * @param TXSingleCond $cond
     * @return bool
     */
    public function update($sets, $cond=null)
    {
        $where = $cond && $cond->where ? " WHERE ".$cond->where : "";
        $set = $this->buildSets($sets);
        $sql = sprintf("UPDATE %s SET %s%s", $this->table, $set, $where);
        TXEvent::trigger('onSql', [$sql]);

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
        TXEvent::trigger('onSql', [$sql]);
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
                if ($val === null) {
                    $val = "NULL";
                } else {
                    $val = is_string($val) ? "'{$this->real_escape_string($val)}'" : $val;
                }
            }
            unset($val);
            $columns[] = '('.join(',', $value).')';
        }
        $columns = join(',', $columns);
        $sql = sprintf("INSERT INTO %s %s VALUES  %s", $this->table, $fields, $columns);
        TXEvent::trigger('onSql', [$sql]);
        return $this->execute($sql, false);
    }

    /**
     * 删除数据
     * @param TXSingleCond $cond
     * @return bool
     */
    public function delete($cond=null)
    {
        $where = $cond && $cond->where ? " WHERE ".$cond->where : "";
        $sql = sprintf("DELETE FROM %s%s", $this->table, $where);
        TXEvent::trigger('onSql', [$sql]);

        return $this->execute($sql);
    }

    /**
     * 添加数量 count=count+1
     * @param $sets
     * @param TXSingleCond $cond
     * @return bool|string
     */
    public function addCount($sets, $cond=null)
    {
        $where = $cond && $cond->where ? " WHERE ".$cond->where : "";
        $set = $this->buildCount($sets);
        $sql = sprintf("UPDATE %s SET %s%s", $this->table, $set, $where);
        TXEvent::trigger('onSql', [$sql]);
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
        TXEvent::trigger('onSql', [$sql]);

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
        TXEvent::trigger('onSql', [$sql]);
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

    /**
     * 构建limit
     * @param $len
     * @param int $start
     * @return TXSingleCond
     */
    public function limit($len, $start=0)
    {
        $cond = new TXSingleCond($this);
        return $cond->limit($len, $start, false);
    }
}