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
    protected $methods = ['group', 'limit', 'order', 'addition', 'having'];

    protected $dbConfig = 'database';

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
     * 找单条数据
     * @param string $fields
     * @param TXCond $cond
     * @return array
     */
    public function find($fields='', $cond=null)
    {
        $where = $cond && $cond->where ? " WHERE ".$cond->where : "";
        $fields = $this->buildFields($fields, $cond ? $cond->additions : array());
        $sql = sprintf("SELECT %s FROM %s%s", $fields, $this->getTable(), $where);
        TXEvent::trigger('onSql', [$sql]);
        $result = $this->sql($sql, null, self::FETCH_TYPE_ONE);
        return $result;
    }

    /**
     * 查询数据
     * @param string $fields
     * @param null $key
     * @param TXCond $cond
     * @return array
     */
    public function query($fields='', $key=null, $cond=null)
    {
        $where = $cond && $cond->where ? " WHERE ".$cond->where : "";
        $limit = $this->buildLimit($cond ? $cond->limit : []);
        $orderBy = $this->buildOrderBy($cond ? $cond->orderby : []);
        $fields = $this->buildFields($fields, $cond ? $cond->additions : []);
        $groupBy = $this->buildGroupBy($cond ? $cond->groupby : [], $cond ? $cond->having : []);
        $sql = sprintf("SELECT %s FROM %s%s%s%s%s", $fields, $this->getTable(), $where, $groupBy, $orderBy, $limit);
        TXEvent::trigger('onSql', [$sql]);

        return $this->sql($sql, $key);
    }

    /**
     * 计算数量
     * @param string $field
     * @param TXCond $cond
     * @return int
     */
    public function count($field='', $cond=null)
    {
        $where = $cond && $cond->where ? " WHERE ".$cond->where : "";
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
        if (in_array($method, $this->methods)){
            if ($this instanceof TXSingleDAO){
                $cond = new TXSingleCond($this);
            } else {
                $cond = new TXDoubleCond($this);
            }
            $args[] = false;
            return call_user_func_array([$cond, $method], $args);
        } else if (in_array($method, $this->calcs)){
            $where = $args[1] && $args[1]->where ? " WHERE ".$args[1]->where : "";
            $sql = sprintf("SELECT %s(%s) as %s FROM %s%s", $method, $args[0], $method, $this->getTable(), $where);
            TXEvent::trigger('onSql', [$sql]);

            $ret = $this->sql($sql, null, self::FETCH_TYPE_ONE);
            return $ret[$method] ?: $ret;
        } else {
            throw new TXException(3009, array($method, get_called_class()));
        }
    }
}