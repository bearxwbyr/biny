<?php
/**
 * Database
 */
class TXDatabase {
    private static $instance = null;
    private static $slave = null;

    public static function instance()
    {
        if (null === self::$instance) {
            $dbconfig = TXConfig::getAppConfig('database', 'dns');

            self::$instance = new self($dbconfig);
        }

        return self::$instance;
    }

    public static function SlaveDB(){
        if (null === self::$slave) {
            $slaveDB = TXConfig::getAppConfig('slaveDb', 'dns');

            self::$slave = new self($slaveDB);
        }
        return self::$slave;
    }

    const FETCH_TYPE_ALL = 0;
    const FETCH_TYPE_ONE = 1;


    /**
     * @var PDO
     */
    private $handler;

    public function __construct($config)
    {
        $this->handler = mysqli_connect($config['host'], $config['user'], $config['password'], $config['database'], $config['port']);
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
     * @return mixed
     */
    public function sql($sql, $mode = self::FETCH_TYPE_ALL)
    {

        $rs = mysqli_query($this->handler, $sql);
        if ($rs) {
            if ($mode == self::FETCH_TYPE_ALL) {
                $result = array();
                while($row = mysqli_fetch_assoc($rs)) {
                    $result[] = $row;
                }
                return $result;
            } else {
                $result = mysqli_fetch_assoc($rs);
            }
            return $result;
        } else {
            TXLogger::error($sql, 'sql Error:');
            return array();
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