<?php
return array(
    'database' => array(
        'host'     => 'localhost',
        'database' => 'Biny',
        'user'     => 'billge',
        'password' => 'billge',
        'encode' => 'utf8',
        'port' => 3306,
    ),
    'slaveDb' => array(
        'host'     => 'localhost',
        'database' => 'Biny',
        'user'     => 'billge',
        'password' => 'billge',
        'encode' => 'utf8',
        'port' => 3306,
    ),
    'memcache' => array(
        'host' => 'localhost',
        'port' => 12121
    ),
    'redis' => array(
        'host' => 'localhost',
        'port' => 6379
    ),

    'rootPath' => '@web@/'
);