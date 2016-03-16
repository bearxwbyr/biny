<?php
class Test
{
    public $aaa = 10;
}
$test = serialize(new Test());
var_dump($test);
var_dump(unserialize($test));