<?php
$a = array(
    'host'     => '127.0.0.1',
    'user'     => 'root',
    'password' => 'root',
    'encode' => 'utf8',
    'port' => 3306,
);
$b = array(
    'host'     => '127.0.0.1',
    'user'     => 'root',
    'password' => 'root',
    'encode' => 'utf8',
    'port' => 3306,
);
var_dump(array_diff($a, $b));