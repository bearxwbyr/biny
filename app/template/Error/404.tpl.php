<? include dirname(__DIR__) . '/base/common.tpl.php' ?>
<div class="container">
    <div class="messageImage">
        <img src="<?=$rootPath?>static/images/source/error.gif" />
    </div>
    <div class="messageInfo">404 参数异常！！！</div>
    <div class="messageUrl">
        现在您可以：
        <a href="javascript:window.history.go(-1);" class='mlink'>[后退]</a>
        <a href="/" class='mlink'>[返回首页]</a>
    </div>

</div>