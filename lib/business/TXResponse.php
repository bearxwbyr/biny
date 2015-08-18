<?php
class TXResponse {
    /**
     * @var 视图名称
     */
    private $view;

    private $params;

    private $ignores;

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
    private function isAsyn()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * 获得模板渲染后的内容
     * @return string
     */
    public function getContent()
    {
        //防XSS注入 todo
        foreach ($this->params as $key => &$param) {
            if (is_string($param) && !in_array($key, $this->ignores)){
                $param = htmlspecialchars($param, ENT_QUOTES);
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
