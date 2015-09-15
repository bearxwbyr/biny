<?php
/**
 * Database
 */
class TXDatabase {
    private static $instance = [];

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


    /**
     * @var PDO
     */
    private $handler;

    public function __construct($config)
    {
        if (!$config || !isset($config['host']) || !isset($config['user']) || !isset($config['password']) || !isset($config['port'])){
            throw new TXException(1004, array('unKnown'));
        }
        $this->handler = mysqli_connect($config['host'], $config['user'], $config['password'], '', $config['port']);
        if (!$this->handler) {
            throw new TXException(1004, array($config['host']));
        }

//        $ret = mysqli_select_db($this->handler, $config['database']);

        mysqli_query($this->handler, "set NAMES {$config['encode']}");

//        if (!$ret) {
//            throw new TXException(1005, array($config['database']));
//        }
    }

    /**
     * sql query data
     * @param string $sql
     * @param int $mode
     * @return TXSqlData|TXObject
     */
    public function sql($sql, $mode = self::FETCH_TYPE_ALL)
    {
        $rs = mysqli_query($this->handler, $sql);
        if ($rs) {
            if ($mode == self::FETCH_TYPE_ALL) {
                $result = new TXSqlData();
                while($row = mysqli_fetch_assoc($rs)) {
                    $result->append(new TXObject($row));
                }
                return $result;
            } else {
                $result = new TXObject(mysqli_fetch_assoc($rs));
            }
            return $result;
        } else {
            TXLogger::error($sql, 'sql Error:');
            return new TXObject();
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
            TXLogger::error($sql, 'sql Error:');
            return false;
        }
    }

    public function real_escape_string($str)
    {
        return mysqli_real_escape_string($this->handler, $str);
    }

    public function real_like_string($str)
    {
        return str_replace(["_", "%"], ["\\_", "\\%"], mysqli_real_escape_string($this->handler, $str));
    }

}