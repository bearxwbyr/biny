<?php
class TXResponse {
    /**
     * @var string 视图名称
     */
    private $view;

    private $params;

    private $objects;

    public $title=null;
    public $keywords=null;
    public $descript=null;

    /**
     * @param $view
     * @param array $params
     * @param array $objects 直接引用对象
     */
    public function __construct($view, $params = array(), $objects=array())
    {
        $this->view = $view;
        $this->params = $params;
        $this->objects = $objects;
    }

    /**
     * 获取csrf
     * @return null
     */
    private function getCsrfToken()
    {
        return TXApp::$base->request->getCsrfToken();
    }

    /**
     * 实体化转义
     * @param $content
     * @return string
     */
    private function encode($content)
    {
        return TXString::encode($content);
    }

    /**
     * 获得模板渲染后的内容
     * @return string
     */
    public function getContent()
    {
        //防XSS注入
        foreach ($this->objects as &$object) {
            if (is_string($object)){
                $object = $this->encode($object);
            } elseif (is_array($object)) {
                $object = new TXArray($object);
            }
        }
        unset($object);
        $this->objects['PRM'] = new TXArray($this->params);

        extract($this->objects);

        ob_start();
        //include template
        $file = sprintf('%s/template/%s.tpl.php', TXApp::$app_root, $this->view);
        include $file;
        TXLogger::showLogs();

        $content = ob_get_clean();
        return $content;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getContent();
    }
}
