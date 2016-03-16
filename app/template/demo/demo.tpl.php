<? include dirname(__DIR__) . "/base/common.tpl.php" ?>
<? include dirname(__DIR__) . "/base/header.tpl.php" ?>
<link href="<?=$CDN_ROOT?>static/css/demo.css" rel="stylesheet" type="text/css"/>
<script>
    var _hmt = _hmt || [];
</script>

<a id="skippy" class="sr-only sr-only-focusable" href="#content"><div class="container"><span class="skiplink-text">Skip to main content</span></div></a>

<!-- Docs master nav -->
<header class="navbar navbar-static-top navbar-inverse" id="top" role="banner">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#bs-navbar" aria-controls="bs-navbar" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="<?=$webRoot?>/demo/" class="navbar-brand">Biny 演示页面</a>
        </div>
    </div>
</header>

<div class="container bs-docs-container">

<div class="row">
<div class="col-md-9" role="main">
    <div class="bs-docs-section">
        <h1 id="overview" class="page-header">概览</h1>
        <p>概述</p>

        <h2 id="overview-introduce">介绍</h2>
        <p>简单介绍</p>
        <div class="highlight">
            <pre>
                <code class="language-html" data-lang="html"><span class="cp">&lt;!DOCTYPE html&gt;</span><span class="nt">&lt;html</span> <span class="na">lang=</span><span class="s">"zh-CN"</span><span class="nt">&gt;</span>
    ...
    <span class="nt">&lt;/html&gt;</span></code>
            </pre>
        </div>
    </div>

    <div class="bs-docs-section">
        <h1 id="config" class="page-header">配置</h1>
        <p>配置</p>

        <h2 id="config-system">系统配置</h2>
        <p>系统配置</p>

        <div style="height: 200px"></div>
    </div>

    <div class="bs-docs-section">
        <h1 id="dao" class="page-header">数据库使用</h1>
        <p>数据库使用</p>

        <h2 id="dao-connect">连接</h2>
        <p>连接</p>

        <div style="height: 200px"></div>
    </div>

    <div class="bs-docs-section">
        <h1 id="event" class="page-header">事件</h1>
        <p>事件</p>

        <h2 id="event-trigger">触发事件</h2>
        <p>触发事件</p>

        <div style="height: 200px"></div>

    </div>

    <div class="bs-docs-section">
        <h1 id="forms" class="page-header">表单验证</h1>
        <p>表单验证</p>

        <h2 id="forms-type">验证类型</h2>
        <p>验证类型</p>

        <div style="height: 200px"></div>
    </div>

</div>

<div class="col-md-3" role="complementary">
    <nav class="bs-docs-sidebar hidden-print hidden-xs hidden-sm">
        <ul class="nav bs-docs-sidenav">

            <li>
                <a href="#overview">概览</a>
                <ul class="nav">
                    <li><a href="#overview-introduce">介绍</a></li>
                </ul>
            </li>
            <li>
                <a href="#config">配置</a>
                <ul class="nav">
                    <li><a href="#config-system">系统配置</a></li>
                </ul>
            </li>
            <li>
                <a href="#dao">数据库使用</a>
                <ul class="nav">
                    <li><a href="#dao-connect">连接</a></li>
                </ul>
            </li>
            <li>
                <a href="#event">事件</a>
                <ul class="nav">
                    <li><a href="#event-trigger">触发事件</a></li>
                </ul>
            </li>
            <li>
                <a href="#forms">表单验证</a>
                <ul class="nav">
                    <li><a href="#forms-type">验证类型</a></li>
                </ul>
            </li>

        </ul>
        <a class="back-to-top" href="#top">
            返回顶部
        </a>

    </nav>
</div>

</div>
</div>

<? include dirname(__DIR__) . "/base/footer.tpl.php" ?>
<script type="text/javascript" src="<?=$CDN_ROOT?>static/js/demo.js"></script>