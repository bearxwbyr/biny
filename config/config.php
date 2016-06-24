<?php
return array(
    'router' => array(
        'base_action' => 'demo'
    ),

    'csrfWhiteIps' => [
        '10.24.196.0/24'
    ],

    'routeRule' => array(
        'test/<id:\d+>.html' => 'test/view'
    ),

    'unable_modules' => array(

    ),
    'autoPath' => 'config/autoload.php',
    'pkCache' => 'tb:%s',

    //csrf
    'trueToken' => 'biny-csrf',
    'csrfToken' => 'csrf-token',
    'csrfPost' => '_csrf',
    'csrfHeader' => 'X-CSRF-TOKEN',

    //cookie
    'session_name' => 'biny_sessionid'
);