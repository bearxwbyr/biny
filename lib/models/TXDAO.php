<?php
/**
 * 数据库
 */
class TXDAO
{
    const FETCH_TYPE_ALL = 0;
    const FETCH_TYPE_ONE = 1;

    private static $_cache = [];

    protected $extracts = ['=', '>', '>=', '<', '<=', '!=', '<>', 'is', 'not is'];
    protected $calcs = ['max', 'min', 'sum', 'avg', 'count'];

    protected $dbConfig = 'database';

    /**
     * @param $obj
     * @return BaseDAO
     */
    public static function getDAO($obj)
    {
        if (!isset(self::$_cache[$obj])){
            self::$_cache[$obj] = new $obj();
        }
        return self::$_cache[$obj];
    }

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
        List($db, $table) = explode('.', $this->table);
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
    public function execute($sql, $id=false) {
        $dns = is_array($this->dbConfig) ? $this->dbConfig[0] : $this->dbConfig;
        return TXDatabase::instance($dns)->execute($sql, $id);
    }

    /**
     * 从库查询SQL
     * @param $sql
     * @param int $mode
     * @return TXSqlData|TXObject
     */
    public function sql($sql, $mode = self::FETCH_TYPE_ALL) {
        $dns = is_array($this->dbConfig) ? $this->dbConfig[1] : $this->dbConfig;
        return TXDatabase::instance($dns)->sql($sql, $mode);
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
                $datas[] = $this->real_escape_string($arg);
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
     * @return mixed
     */
    protected function real_escape_string($string){
        return addslashes($string);
    }

    /**
     * 查找不重复的项
     * @param string $fields
     * @return TXSqlData
     */
    public function distinct($fields){
        $params = func_get_args();
        $where = isset($params[1]) ? " WHERE ".$params[1] : "";
        $fields = $this->buildFields($fields);
        $sql = sprintf("SELECT distinct %s FROM %s%s", $fields, $this->table, $where);

        return $this->sql($sql);
    }

    /**
     * 找单条数据
     * @param string $fields
     * @return TXObject
     */
    public function find($fields = '*')
    {
        $params = func_get_args();
        $where = isset($params[1]) ? " WHERE ".$params[1] : "";
        $fields = $this->buildFields($fields);
        $sql = sprintf("SELECT %s FROM %s%s", $fields, $this->table, $where);
//        TXLogger::info($sql);
        $result = $this->sql($sql);
        return $result ? $result[0] : $result;
    }

    /**
     * 查询数据
     * @param array $limit array(40, 20)
     * @param array $orderBy array("id"=>"desc", "name"=>"asc")
     * @param string $fields id,name,fields
     * @return TXSqlData
     */
    public function query($limit = array(), $orderBy = array(), $fields = '*')
    {
        $params = func_get_args();
        $where = isset($params[3]) ? " WHERE ".$params[3] : "";
        $limit = $this->buildLimit($limit);
        $orderBy = $this->buildOrderBy($orderBy);
        $fields = $this->buildFields($fields);
        $sql = sprintf("SELECT %s FROM %s%s%s%s", $fields, $this->table, $where, $orderBy, $limit);
        TXLogger::info($sql);

        return $this->sql($sql);
    }

    /**
     * group语句
     * @param array $adds ['sum'=>['id'=>'s_id']]
     * @param $fields
     * @param array $groupBy ['id']
     * @param array $having ['>='=>['s_id'=>10]]
     * @param array $limit [10, 10]
     * @param array $orderBy ['id'=>'desc'] ['id'=>['desc', 'gbk']]
     * @return TXSqlData
     */
    public function group($adds=array(), $fields='', $groupBy=array(), $having=array(), $limit = array(), $orderBy = array())
    {
        $params = func_get_args();
        $where = isset($params[6]) ? " WHERE ".$params[6] : "";
        $limit = $this->buildLimit($limit);
        $orderBy = $this->buildOrderBy($orderBy);
        $fields = $this->buildFields($fields, $adds);
        $groupBy = $this->buildGroupBy($groupBy, $having);
        $sql = sprintf("SELECT %s FROM %s%s%s%s%s", $fields, $this->table, $where, $groupBy, $orderBy, $limit);
        TXLogger::info($sql);

        return $this->sql($sql);
    }

//    /**
//     * 查询数量
//     * @return int
//     */
//    public function total()
//    {
//        $params = func_get_args();
//        $where = isset($params[0]) ? " WHERE ".$params[0] : "";
//        $sql = sprintf("SELECT COUNT(0) as total FROM %s%s", $this->table, $where);
//
//        $ret = $this->sql($sql);
//        return $ret ? $ret[0]['total'] : $ret;
//    }

    /**
     * 查询条件
     * @param $method ['max', 'min', 'sum', 'avg', 'count']
     * @param $method
     * @param $args
     * @return mixed
     * @throws TXException
     */
    public function __call($method, $args)
    {
        if (in_array($method, $this->calcs)){
            if (!$args){
                $args = [0];
            } else {
                $args[0] = $args[0] ? "`{$args[0]}`" : $args[0];
            }
            $where = isset($args[1]) ? " WHERE ".$args[1] : "";
            $sql = sprintf("SELECT %s(%s) as %s FROM %s%s", $method, $args[0], $method, $this->table, $where);
//            TXLogger::info($sql);

            $ret = $this->sql($sql);
            return $ret ? $ret[0][$method] : $ret;
        } else {
            throw new TXException(2020, array($method, __CLASS__));
        }
    }
}