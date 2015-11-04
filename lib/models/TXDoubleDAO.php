<?php
/**
 * 多表数据库
 */
class TXDoubleDAO extends TXDAO
{
    /**
     * 表格名称
     * @var string
     */
    protected $table;

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
        $this->buildTable($DAOs, $relates);
    }

    /**
     * 获得连表
     * @return string
     */
    public function getTable()
    {
        $tables = array_values($this->DAOs);
        return join("--", $tables);
    }

    /**
     * @return mixed
     */
    public function getDoubles()
    {
        return $this->doubles;
    }

    /**
     * 多表构建
     * @param $DAOs
     * @param $relates
     * @return string
     * @throws Exception
     */
    private function buildTable($DAOs, $relates){
        if (count($relates) !== count($DAOs)-1){
            throw new TXException(1012, array(json_encode($DAOs)));
        }
        $this->DAOs = $DAOs;
        $this->doubles = array_keys($DAOs);
        $this->relates = $relates;
        $dbtbs = [];
        $i = 0;
        foreach ($DAOs as $name => $table){
            if (!$dbtbs){
                $dbtbs[] = "{$table} `$name`";
            } else {
                $relate = $relates[$i++];
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
            throw new TXException(1012, "DAOs must be the same Host");
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
     * 获取复合表fields
     * @param $fields
     * @param array $group
     * @return string
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
                        if (is_string($key)){
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
                if (is_int($key)){
                    if (isset($this->doubles[$key])){
                        $table = $this->doubles[$key];
                    } else {
                        continue;
                    }
                    foreach ($values as $ck => $vals){
                        if (!in_array(strtolower($ck), $this->calcs)){
                            continue;
                        }
                        foreach ($vals as $k => $value){
                            if (is_string($k)){
                                $groups[] = "{$ck}(`{$table}`.{$k}) as {$value}";
                            } else {
                                $groups[] = "{$ck}(`{$table}`.{$value}) as {$value}";
                            }
                        }
                    }
                } else if (!in_array($key, $this->calcs)){
                    continue;
                } else {
                    foreach ($values as $k => $value){
                        $groups[] = "{$key}({$k}) as {$value}";
                    }
                }
            }
            return join(',', $groups);
        }
        return $fields;
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
            return sprintf(' LIMIT %s,%s', $limit[0], $limit[1]);
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