<?php
date_default_timezone_set('Asia/Shanghai');

defined('SYS_DEBUG') or define('SYS_DEBUG', true);
defined('SYS_CONSOLE') or define('SYS_CONSOLE', true);
//dev pre pub
defined('SYS_ENV') or define('SYS_ENV', 'dev');
defined('isMaintenance') or define('isMaintenance', false);

if (SYS_DEBUG){
    ini_set('display_errors','On');
    error_reporting(E_ALL ^ E_NOTICE);
}

include 'lib/TXApp.php';

//include 'lib/logger/XHProf.php';
//XHProf::start();

TXApp::registry(__DIR__ . DS . 'app');
TXApp::run();

//$data = XHProf::end();
//XHProf::display($data);

//XHProf::consoles($data);