<?php
/**
 * 数据库
 * @method int sum($field)
 * @method int max($field)
 * @method int min($field)
 * @method int avg($field)
 */
class TXDAO
{
    const FETCH_TYPE_ALL = 0;
    const FETCH_TYPE_ONE = 1;

    protected $extracts = ['=', '>', '>=', '<', '<=', '!=', '<>', 'is', 'not is'];
    protected $calcs = ['max', 'min', 'sum', 'avg', 'count', 'distinct'];

    protected $dbConfig = 'database';

    protected $where;
    protected $limit=array();
    protected $orderby=array();
    protected $additions=array();
    protected $groupby=array();
    protected $having=array();

    /**
     * @return string
     */
    public function getDbConfig()
    {
        return $this->dbConfig;
    }

    /**
     * 判断是否可以合表
     * @param $dao TXDAO
     * @return bool
     */
    protected function checkConfig($dao)
    {
        $tMaster = is_array($this->dbConfig) ? $this->dbConfig[0] : $this->dbConfig;
        $tSlave = is_array($this->dbConfig) ? $this->dbConfig[1] : $this->dbConfig;
        $dDbConfig = $dao->getDbConfig();
        $dMaster = is_array($dDbConfig) ? $dDbConfig[0] : $dDbConfig;
        $dSlave = is_array($dDbConfig) ? $dDbConfig[1] : $dDbConfig;
        if ($tMaster === $dMaster && $tSlave === $dSlave){
            return true;
        }
        if ($tMaster !== $dMaster){
            $tConfig = TXConfig::getAppConfig($tMaster, 'dns');
            $dConfig = TXConfig::getAppConfig($dMaster, 'dns');
            unset($tConfig['database']);
            unset($dConfig['database']);
            if (array_diff($tConfig, $dConfig)){
                return false;
            }
        } else if ($tSlave !== $dSlave){
            $tConfig = TXConfig::getAppConfig($tSlave, 'dns');
            $dConfig = TXConfig::getAppConfig($dSlave, 'dns');
            unset($tConfig['database']);
            unset($dConfig['database']);
            if (array_diff($tConfig, $dConfig)){
                return false;
            }
        }
        return true;
    }

    /**
     * 左联接
     * @param $dao
     * @param $relate
     * @return $this|TXDoubleDAO
     */
    public function leftJoin($dao, $relate)
    {
        return $this->_join($dao, $relate, 'left join');
    }

    /**
     * 右联接
     * @param $dao
     * @param $relate
     * @return $this|TXDoubleDAO
     */
    public function rightJoin($dao, $relate)
    {
        return $this->_join($dao, $relate, 'right join');
    }

    /**
     * 联接
     * @param $dao
     * @param $relate
     * @return $this|TXDoubleDAO
     */
    public function join($dao, $relate)
    {
        return $this->_join($dao, $relate, 'join');
    }

    /**
     * 存在表
     * @return bool
     */
    protected function isExist()
    {
        List($db, $table) = explode('.', $this->getTable());
        $sql = sprintf("select table_name from INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA='%s' and TABLE_NAME='%s' ;", $db, trim($table, '`'));
        $result = $this->sql($sql);
        return count($result) ? true : false;
    }

    /**
     * Filter预设where (不建议直接调用)
     * @param $where
     */
    public function setWhere($where)
    {
        $this->where = $where;
    }

    /**
     * 执行sql
     * @param $sql
     * @param bool $id
     * @return bool|int|mysqli_result|string
     */
    protected function execute($sql, $id=false) {
        $dns = is_array($this->dbConfig) ? $this->dbConfig[0] : $this->dbConfig;
        return TXDatabase::instance($dns)->execute($sql, $id);
    }

    /**
     * 从库查询SQL
     * @param $sql
     * @param $key
     * @param int $mode
     * @return array
     */
    public function sql($sql, $key=null, $mode=self::FETCH_TYPE_ALL) {
        $dns = is_array($this->dbConfig) ? $this->dbConfig[1] : $this->dbConfig;
        return TXDatabase::instance($dns)->sql($sql, $key, $mode);
    }

    /**
     * 防sql注入 #sprintf
     * @params array/string
     * @return mixed
     */
    public function _sprintf(){
        $args = func_get_args();
        $datas = array();
        $datas[] = array_shift($args);
        foreach ($args as $arg){
            if (is_string($arg)){
                $datas[] = $this->real_escape_string($arg, false);
            } elseif (is_array($arg)) {
                foreach ($arg as &$value){
                    $value = "'{$this->real_escape_string($value)}'";
                }
                unset($value);
                $datas[] = join(",", $arg);
            } else {
                $datas[] = $arg;
            }
        }
        return call_user_func_array('sprintf', $datas);
    }

    /**
     * real_escape_string
     * @param $string
     * @param bool $ignore
     * @return mixed|string
     */
    protected function real_escape_string($string, $ignore=true){
        $string = addslashes($string);
        return $ignore ? str_replace('`', '\`', $string) : $string;
    }

    /**
     * real_like_string\
     * @param $str
     * @return mixed
     */
    protected function real_like_string($str){
        return str_replace(["_", "%"], ["\\_", "\\%"], addslashes($str));
    }

    /**
     * 构建limit
     * @param $start
     * @param $len
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

    /**
     * 查找不重复的项
     * @param $fields
     * @return array
     */
    public function distinct($fields){
        $where = $this->where ? " WHERE ".$this->where : "";
        $limit = $this->buildLimit($this->limit);
        $orderBy = $this->buildOrderBy($this->orderby);
        $fields = $this->buildFields($fields);
        $sql = sprintf("SELECT distinct %s FROM %s%s%s%s", $fields, $this->getTable(), $where, $orderBy, $limit);
        TXEvent::trigger('onSql', [$sql]);

        return $this->sql($sql);
    }

    /**
     * 找单条数据
     * @param string $fields
     * @return array
     */
    public function find($fields='')
    {
        $where = $this->where ? " WHERE ".$this->where : "";
        $fields = $this->buildFields($fields, $this->additions);
        $sql = sprintf("SELECT %s FROM %s%s", $fields, $this->getTable(), $where);
        TXEvent::trigger('onSql', [$sql]);
        $result = $this->sql($sql, null, self::FETCH_TYPE_ONE);
        return $result;
    }

    /**
     * 查询数据
     * @param string $fields
     * @param null $key
     * @return array
     */
    public function query($fields='', $key=null)
    {
        $where = $this->where ? " WHERE ".$this->where : "";
        $limit = $this->buildLimit($this->limit);
        $orderBy = $this->buildOrderBy($this->orderby);
        $fields = $this->buildFields($fields, $this->additions);
        $groupBy = $this->buildGroupBy($this->groupby, $this->having);
        $sql = sprintf("SELECT %s FROM %s%s%s%s%s", $fields, $this->getTable(), $where, $groupBy, $orderBy, $limit);
        TXEvent::trigger('onSql', [$sql]);

        return $this->sql($sql, $key);
    }

    /**
     * 计算数量
     * @param string $field
     * @return int
     */
    public function count($field='')
    {
        $where = $this->where ? " WHERE ".$this->where : "";
        $field = $field ? 'DISTINCT '.$this->buildFields($field) : '0';
        $sql = sprintf("SELECT COUNT(%s) as count FROM %s%s", $field, $this->getTable(), $where);
        TXEvent::trigger('onSql', [$sql]);

        $ret = $this->sql($sql);
        return $ret[0]['count'] ?: 0;
    }

    /**
     * 查询条件
     * @param $method ['max', 'min', 'sum', 'avg']
     * @param $args
     * @return mixed
     * @throws TXException
     */
    public function __call($method, $args)
    {
        if (in_array($method, $this->calcs)){
            $where = $this->where ? " WHERE ".$this->where : "";
            $sql = sprintf("SELECT %s(%s) as %s FROM %s%s", $method, $args[0], $method, $this->getTable(), $where);
            TXEvent::trigger('onSql', [$sql]);

            $ret = $this->sql($sql, null, self::FETCH_TYPE_ONE);
            return $ret[$method] ?: $ret;
        } else {
            throw new TXException(3009, array($method, get_called_class()));
        }
    }
}