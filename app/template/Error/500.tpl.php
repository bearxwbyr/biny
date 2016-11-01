<? include TXApp::$view_root . '/base/common.tpl.php' ?>
<div class="container center">
    <div class="messageImage">
        <img src="<?=$CDN_ROOT?>static/images/source/error.gif" />
    </div>
    <div class="messageInfo">网站有一个异常，请稍候再试</div>
    <div class="messageUrl">
        现在您可以：
        <a href="javascript:window.history.go(-1);" class='mlink'>[后退]</a>
        <a href="/" class='mlink'>[返回首页]</a>
    </div>

</div>