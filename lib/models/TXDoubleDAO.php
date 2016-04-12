<?php
/**
 * 多表数据库
 * @method TXDoubleCond limit($len, $start=0)
 * @method TXDoubleCond group($groupby)
 * @method TXDoubleCond having($having)
 * @method TXDoubleCond order($orderby)
 * @method TXDoubleCond addition($additions)
 */
class TXDoubleDAO extends TXDAO
{
    /**
     * 表格名称
     * @var string
     */
    protected $table = null;

    /**
     * 连表位
     */
    protected $doubles;

    /**
     * 关联表
     */
    protected $DAOs;

    /**
     * 管理结构
     */
    protected $relates;

    /**
     * 构造函数
     */
    public function __construct($DAOs, $relates, $db)
    {
        $this->dbConfig = $db;
        if (count($relates) !== count($DAOs)-1){
            throw new TXException(3002, array(json_encode($DAOs)));
        }
        $this->DAOs = $DAOs;
        $this->doubles = array_keys($DAOs);
        $this->relates = $relates;
    }

    /**
     * 获取Log DAO
     * @return string
     */
    public function getDAO()
    {
        $tables = array_values($this->DAOs);
        return join("--", $tables);
    }

    /**
     * 获取连表
     * @return string
     */
    protected function getTable()
    {
        if (!$this->table){
            $dbtbs = [];
            $i = 0;
            foreach ($this->DAOs as $name => $table){
                if (!$dbtbs){
                    $dbtbs[] = "{$table} `$name`";
                } else {
                    $relate = $this->relates[$i++];
                    $join = array_keys($relate);
                    $on = array_values($relate);
                    $dbtbs[] = $join[0];
                    $dbtbs[] = "{$table} `$name`";
                    $ons = [];
                    foreach ($on[0] as $key=>$value){
                        $ons[] = "{$key}={$value}";
                    }
                    $dbtbs[] = "on ".join(' and ', $ons);
                }
            }
            $this->table = join(" ", $dbtbs);
        }
        return $this->table;
    }

    /**
     * 链接表
     * @param $dao TXSingleDAO
     * @param $relateD
     * @param string $type
     * @return $this|TXDoubleDAO
     * @throws TXException
     */
    protected function _join($dao, $relateD, $type='join')
    {
        $daoClass = substr(get_class($dao), 0, -3);
        if (isset($this->doubles[$daoClass])){
            return $this;
        }
        if (!$this->checkConfig($dao)){
            throw new TXException(3002, "DAOs must be the same Host");
        }
        $DAOs = $this->DAOs;
        $DAOs[$daoClass] = $dao->getTable();

        $relates = $this->relates;
        $join = [];
        foreach ($relateD as $k => $relate){
            if (is_string($k) && in_array($k, $this->doubles)){
                $table = $k;
            } else if (isset($this->doubles[$k])){
                $table = $this->doubles[$k];
            } else {
                continue;
            }
            foreach ($relate as $key => $value){
                $join[$table.".".$key] = $daoClass.".".$value;
            }
        }
        $relates[] = [$type => $join];
        return new TXDoubleDAO($DAOs, $relates, $this->dbConfig);
    }

    /**
     * buildWhere
     * @param $conds
     * @param string $type
     * @return string
     */
    public function buildWhere($conds, $type='and')
    {
        if (empty($conds)) {
            return '';
        }
        $doubles = $this->doubles;
        $where = array();
        foreach ($conds as $k => $cond){
            if (is_string($k) && in_array($k, $doubles)){
                $table = $k;
            } else if (isset($doubles[$k])){
                $table = $doubles[$k];
            } else {
                continue;
            }
            foreach($cond as $key => $value) {
                $key = $this->real_escape_string($key);
                if (in_array(strtolower($key), $this->extracts)){
                    foreach ($value as $arrk => $arrv){
                        $arrk = $this->real_escape_string($arrk);
                        if (is_null($arrv)){
                            $where[] = "`{$table}`.`{$arrk}`{$key} NULL";
                        }
                        else if (is_string($arrv)){
                            $arrv = $this->real_escape_string($arrv);
                            $where[] = "`{$table}`.`{$arrk}`{$key}'{$arrv}'";
                        } else {
                            $where[] = "`{$table}`.`{$arrk}`{$key}{$arrv}";
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
                                $where[] = "`{$table}`.`{$arrk}` like '{$like}'";
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
                            $where[] = "`{$table}`.`{$arrk}` like '{$arrv}'";
                        }
                    }
                } elseif (is_null($value)){
                    $where[] = "`{$table}`.`{$key}`is NULL";
                } elseif (is_string($value)) {
                    $value = $this->real_escape_string($value);
                    $where[] = "`{$table}`.`{$key}`='{$value}'";
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
                    $value = "(". join(",", $value).")";
                    $where[] = "`{$table}`.`{$key}` in {$value}";
                } else {
                    $where[] = "`{$table}`.`{$key}`={$value}";
                }
            }
        }
        return join(" {$type} ", $where);
    }

    /**
     * 获取复合表fields
     * @param $fields
     * @param array $group
     * @return string
     * @throws TXException
     */
    protected function buildFields($fields, $group=array()){
        if (is_array($fields)){
            $temps = [];
            foreach ($fields as $k => $field){
                if (is_string($k) && in_array($k, $this->doubles)){
                    $table = $k;
                } else if (isset($this->doubles[$k])){
                    $table = $this->doubles[$k];
                } else {
                    continue;
                }
                if ($field === "*"){
                    $temps[] = "`{$table}`.*";
                } else {
                    foreach ($field as $key => $column){
                        $column = $this->real_escape_string($column);
                        if (is_string($key)){
                            $key = $this->real_escape_string($key);
                            $temps[] = "`{$table}`.`".$key."` $column";
                        } else {
                            $temps[] = "`{$table}`.`".$column."`";
                        }
                    }
                }
            }
            $fields = join(',', $temps);
        }
        if ($group){
            if ($fields){
                $groups = [$fields];
            } else {
                $groups = [];
            }
            foreach ($group as $key => $values){
                if (is_string($key) && in_array($key, $this->doubles)){
                    $table = $key;
                } else if (isset($this->doubles[$key])){
                    $table = $this->doubles[$key];
                } else {
                    continue;
                }
                foreach ($values as $ck => $vals){
                    if (!in_array(strtolower($ck), $this->calcs)){
                        throw new TXException(3011, array($ck));
                    }
                    foreach ($vals as $k => $value){
                        $value = $this->real_escape_string($value);
                        if (is_string($k)){
                            $k = $this->real_escape_string($k);
                            if ($ck == 'distinct'){
                                $groups[] = "COUNT(DISTINCT `{$table}`.`{$k}`) as '{$value}'";
                            } else {
                                $groups[] = "{$ck}(`{$table}`.`{$k}`) as '{$value}'";
                            }
                        } else {
                            $groups[] = "{$ck}(`{$table}`.`{$value}`) as '{$value}'";
                        }
                    }
                }
            }
            return join(',', $groups);
        }
        return $fields ?: '*';
    }

    /**
     * 拼装Doubleorderby
     * @param $orderBys
     * @return string
     */
    protected function buildOrderBy($orderBys){
        $orders = array();
        foreach ($orderBys as $k => $orderBy){
            if (is_string($k) && in_array($k, $this->doubles)){
                $table = $k;
            } else if (isset($this->doubles[$k])){
                $table = $this->doubles[$k];
            } else if (is_string($k)) {
                $k = $this->real_escape_string($k);
                //外层循环
                if (is_array($orderBy)){
                    $asc = isset($orderBy[0]) ? $orderBy[0] : 'ASC';
                    $code = isset($orderBy[1]) ? $orderBy[1] : 'gbk';
                    if (!in_array(strtoupper($asc), array('ASC', 'DESC'))){
                        TXLogger::error("order must be ASC/DESC, {$asc} given", 'sql Error');
                        continue;
                    }
                    $orders[] = "CONVERT(`{$k}` USING {$code}) $asc";
                } else {
                    if (!in_array(strtoupper($orderBy), array('ASC', 'DESC'))){
                        TXLogger::error("order must be ASC/DESC, {$orderBy} given", 'sql Error');
                        continue;
                    }
                    $orders[] = '`'.$k."` ".$orderBy;
                }
                continue;
            } else {
                continue;
            }
            foreach ($orderBy as $key => $val){
                $key = $this->real_escape_string($key);
                if (is_array($val)){
                    $field = $table.".`".$key.'`';
                    $asc = isset($val[0]) ? $val[0] : 'ASC';
                    $code = isset($val[1]) ? $val[1] : 'gbk';
                    if (!in_array(strtoupper($asc), array('ASC', 'DESC'))){
                        TXLogger::error("order must be ASC/DESC, {$asc} given", 'sql Error');
                        continue;
                    }
                    $orders[] = "CONVERT({$field} USING {$code}) $asc";
                } else {
                    if (!in_array(strtoupper($val), array('ASC', 'DESC'))){
                        TXLogger::error("order must be ASC/DESC, {$val} given", 'sql Error');
                        continue;
                    }
                    $orders[] = $table.".`".$key."` ".$val;
                }
            }
        }
        if ($orders){
            return ' ORDER BY '.join(',', $orders);
        } else {
            return '';
        }
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
            $temps = [];
            foreach ($groupBy as $k => $group){
                if (is_string($k) && in_array($k, $this->doubles)){
                    $table = $k;
                } else if (isset($this->doubles[$k])){
                    $table = $this->doubles[$k];
                } else {
                    continue;
                }
                foreach ($group as $column){
                    $column = $this->real_escape_string($column);
                    $temps[] = $table.".`".$column."`";
                }
            }
            $groupBy = join(',', $temps);
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
     * and 操作
     * @param $cond
     * @return TXDoubleFilter
     */
    public function filter($cond=array())
    {
        return $cond ? new TXDoubleFilter($this, $cond, "__and__") : $this;
    }

    /**
     * or 操作
     * @param $cond
     * @return TXDoubleFilter
     */
    public function merge($cond=array())
    {
        return $cond ? new TXDoubleFilter($this, $cond, "__or__") : $this;
    }
}