<?php
return array(
    'router' => array(
        'base_action' => 'main'
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
);