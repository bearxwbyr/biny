<?php
return array(
    'autoSkipLoad' => 5,
    'router' => array(
        'base_action' => 'demo',
        'base_shell' => 'index'
    ),

    'csrfWhiteIps' => [
        '10.24.196.0/24'
    ],

    'routeRule' => array(
        '<method:\w+>/test/<id:\d+>.html' => 'test/<method>',
        'test/<id:[\w_%]+>.html' => 'test/view',
    ),
    'autoPath' => 'config/autoload.php',
    'pkCache' => 'tb:%s',

    //csrf
    'trueToken' => 'biny-csrf',
    'csrfToken' => 'csrf-token',
    'csrfPost' => '_csrf',
    'csrfHeader' => 'X-CSRF-TOKEN',

    //cookie
    'session_name' => 'biny_sessionid',

    //ErrorDisplay
    'errorLevel' => NOTICE,

    //sqlWarning
    'slowQuery' => 100,
);