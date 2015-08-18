<?php
/**
 * JSON response class
 */
class TXJSONResponse {
    private $data;

    /**
     * 构造函数
     * @param array $data
     */
    public function __construct($data)
    {
        if (SYS_CONSOLE && TXLogger::$ConsoleOut){
            $data['__logs'] = TXLogger::$ConsoleOut;
            TXLogger::$ConsoleOut = array();
        }
        $this->data = $data;
    }

    function __toString()
    {
        return json_encode($this->data, JSON_UNESCAPED_UNICODE);
    }
}