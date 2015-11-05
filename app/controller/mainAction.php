<?php
/**
 * 主页Action
 * @property testService $testService
 */
class mainAction extends baseAction
{
    private $eventFH;
    public function __construct($params)
    {
        parent::__construct($params);
        TXEvent::on(array($this, 'testEvent'), beforeAction);
        TXEvent::on(array($this, 'testEvent2'), afterAction);
        TXEvent::on(array(TXFactory::create('testEvent'), 'another'), 'myEvent');
        $this->eventFH = TXEvent::on(array(TXFactory::create('testEvent'), 'before'), 'myEvent');
    }

    public function init()
    {
        TXLogger::info('init');
    }

    public function execute($id=10, $type)
    {
        TXLogger::info('start');
        TXEvent::trigger('myEvent', array('test'));
        TXEvent::off('myEvent', $this->eventFH);
        TXEvent::trigger('myEvent', array('test'));
        TXEvent::trigger('myEvent', array('test'));
        $arr = $this->testService->test();
//        TXLogger::display($arr);

        $params = [
            'testArr' => $arr,
            'string' => 'bb<>'
        ];
        $view = $this->display('main/test', $params);
        $view->title = "主页标题";
        return $view;
    }

    public function action_test($id, $type="ss")
    {
        TXLogger::info($id, '$id');
        TXLogger::info($type, '$type');
        return $this->error('出大事了');
    }

    /**
     * @param $event
     * @param array $param
     */
    public function testEvent($event, $param=array())
    {
        TXLogger::info("tigger in beforeAction".$event);
    }

    /**
     * @param $event
     * @param array $param
     */
    public function testEvent2($event, $param=array())
    {
        TXLogger::info("tigger in afterAction".$event);
    }
}