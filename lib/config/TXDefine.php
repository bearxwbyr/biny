<?php
/**
 * 全局变量操作静态类
 *
 * @author billge
 */
class TXDefine
{
    /**
     * 设置全局变量
     */
    public static function init()
    {
        defined('ENV_DEV') or define('ENV_DEV', SYS_ENV === 'dev');
        defined('ENV_PRE') or define('ENV_PRE', SYS_ENV === 'pre');
        defined('ENV_PUB') or define('ENV_PUB', SYS_ENV === 'pub');

        defined('isLog') or define('isLog', true);

        //TXEvent 默认事件
        defined('beforeAction') or define('beforeAction', 1);
        defined('afterAction') or define('afterAction', 2);
        defined('onException') or define('onException', 3);
        defined('onError') or define('onError', 4);
    }
}