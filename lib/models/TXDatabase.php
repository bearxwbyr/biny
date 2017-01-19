<?php
/**
 * Database
 */
class TXDatabase {
    private static $instance = [];
    private static $autocommit = true;

    /**
     * @param string $name
     * @return TXDatabase
     */
    public static function instance($name)
    {
        if (!isset(self::$instance[$name])) {
            $dbconfig = TXConfig::getAppConfig($name, 'dns');

            self::$instance[$name] = new self($dbconfig);
        }

        return self::$instance[$name];
    }

    const FETCH_TYPE_ALL = 0;
    const FETCH_TYPE_ONE = 1;
    const FETCH_TYPE_CURSOR = 2;


    /**
     * @var PDO
     */
    private $handler;

    public function __construct($config)
    {
        if (!$config || !isset($config['host']) || !isset($config['user']) || !isset($config['password']) || !isset($config['port'])){
            throw new TXException(3001, array('unKnown'));
        }
        if (isset($config['keep-alive']) && $config['keep-alive']){
            $config['host'] = 'p:'.$config['host'];
        }
        $this->handler = mysqli_connect($config['host'], $config['user'], $config['password'], '', $config['port']);
        if (!$this->handler) {
            throw new TXException(3001, array($config['host']));
        }
        $this->handler->autocommit(self::$autocommit);
        $this->handler->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);

        mysqli_query($this->handler, "set NAMES {$config['encode']}");
    }

    /**
     * 开始事务
     */
    public static function start()
    {
        self::$autocommit = false;
        foreach (self::$instance as $db){
            $db->handler->autocommit(false);
        }
    }

    /**
     * 结束事务
     */
    public static function end()
    {
        self::rollback();
        self::$autocommit = true;
        foreach (self::$instance as $db){
            $db->handler->autocommit(true);
        }
    }

    /**
     * 回滚事务
     */
    public static function rollback()
    {
        foreach (self::$instance as $db){
            if (!self::$autocommit){
                $db->handler->rollback();
            }
        }
    }

    /**
     * 提交事务
     */
    public static function commit()
    {
        foreach (self::$instance as $db){
            if (!self::$autocommit){
                $db->handler->commit();
            }
        }
    }

    /**
     * 游标更新
     * @param $rs
     * @return array|null
     */
    public static function step($rs)
    {
        return mysqli_fetch_assoc($rs);
    }

    /**
     * sql query data
     * @param string $sql
     * @param $key
     * @param int $mode
     * @return array
     */
    public function sql($sql, $key=null, $mode = self::FETCH_TYPE_ALL)
    {
        $start = microtime(true);
        $rs = mysqli_query($this->handler, $sql);
        $time = (microtime(true)-$start)*1000;
        if ($time > (TXConfig::getConfig('slowQuery') ?: 1000)){
            TXLogger::addError(sprintf('Slow Query: %s [%sms]', $sql, $time), WARNING);
            TXLogger::warn(sprintf('Slow Query: %s [%sms]', $sql, $time));
        }
        if ($rs) {
            if ($mode == self::FETCH_TYPE_ALL) {
                $result = array();
                while($row = mysqli_fetch_assoc($rs)) {
                    if ($key){
                        $result[$row[$key]] = $row;
                    } else {
                        $result[] = $row;
                    }

                }
                return $result;
            } else if ($mode == self::FETCH_TYPE_CURSOR){
                return $rs;
            } else {
                $result = mysqli_fetch_assoc($rs) ?: [];
            }
            return $result;
        } else {
            TXLogger::addError(sprintf("sql Error: %s [%s]", mysqli_error($this->handler), $sql));
            TXLogger::error(sprintf("%s [%s]", mysqli_error($this->handler), $sql), 'sql Error:');
            return [];
        }
    }

    /**
     * sql execute
     * @param $sql
     * @param bool $id
     * @return bool|int|mysqli_result|string
     */
    public function execute($sql, $id=false)
    {
        if (mysqli_query($this->handler, $sql)){
            if ($id){
                return mysqli_insert_id($this->handler);
//            return mysql_insert_id();
            }
            return true;
        } else {
            TXLogger::addError(sprintf("sql Error: %s [%s]", mysqli_error($this->handler), $sql));
            TXLogger::error($sql, 'sql Error:');
            return false;
        }
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        if (!self::$autocommit){
            $this->handler->rollback();
            $this->handler->autocommit(true);
        }
    }

}