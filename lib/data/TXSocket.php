<?php
/**
 * Socket
 */
class TXSocket {
    /**
     * @var TXSocket
     */
    private static $instance = null;

    public static function instance()
    {
        if (null === self::$instance) {
            $SocketConfig = TXConfig::getAppConfig('socket', 'dns');

            self::$instance = new self($SocketConfig);
        }

        return self::$instance;
    }


    /**
     * @var PDO
     */
    private $handler;

    public function __construct($config)
    {
        if( ($this->handler = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
            throw new TXException(4001);
        }
        socket_set_option($this->handler, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>$config['timeout'], "usec"=>0));
        if (@socket_connect($this->handler, $config['host'], $config['port']) === false) {
            throw new TXException(4002, array($config['host'], $config['port']));
        }
    }

    /**
     * 发送buff
     * @param $buff
     * @return bool
     */
    public function sendBuff($buff){
        if (is_array($buff)){
            $buff = json_encode($buff);
        }
        $len = strlen($buff);
        @socket_send($this->handler, $buff, $len, 0);
        return true;
    }


    /**
     * 收Buff
     * @return bool|mixed|string
     * @throws TXException
     */
    public function revBuff(){
        $nCnt = @socket_recv($this->handler, $buf, 4, 0);
        if ($nCnt === false){
            return -1;//timeout
        }
        if ($nCnt != 4) {
            return false;
        }
        $ret = unpack("LLen", $buf);
        $len = $ret['Len'];
        $data='';
        $recvlen = $len;
        $i = 0;
        while ($recvlen > 0) {
            if (++$i == 100){
                throw new TXException(4003);
            }
            $nCnt = @socket_recv($this->handler, $buf, $recvlen, 0);
            $data .= $buf;
            $recvlen -= $nCnt;
        }
//        $data = chop($data);
        if ($result = json_decode($data, true)){
            return $result;
        } else {
            return $data;
        }
    }

    /**
     * 发送socket请求
     * @param $buff
     * @return bool|mixed|string
     */
    public function send($buff){
        if ($this->sendBuff($buff)){
            return $this->revBuff();
        }
        return false;
    }
}