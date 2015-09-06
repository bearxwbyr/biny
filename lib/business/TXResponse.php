<?php
class TXResponse {
    /**
     * @var 视图名称
     */
    private $view;

    private $params;

    private $ignores;

    public $title=null;
    public $keywords=null;
    public $descript=null;

    public function __construct($view, $params = array(), $ignores=array())
    {
        $this->view = $view;
        $this->params = $params;
        $this->ignores = $ignores;
    }

    /**
     * 是否异步请求
     * @return bool
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * 实体化转义
     * @param $content
     * @return string
     */
    public function htmlEncode($content)
    {
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE);
    }

    /**
     * 获得模板渲染后的内容
     * @return string
     */
    public function getContent()
    {
        //防XSS注入
        foreach ($this->params as $key => &$param) {
            if (is_string($param) && !in_array($key, $this->ignores)){
                $param = $this->htmlEncode($param);
            }
        }
        unset($param);

        extract($this->params);

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
