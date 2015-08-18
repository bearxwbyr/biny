<? include dirname(__DIR__).'/Base/common.tpl.php' ?>
<div class="container">
    <div class="messageImage">
        <img src="<?=$rootPath?>static/images/source/error.gif" />
    </div>
    <div class="messageInfo">查询服务器连接失败！！！请稍候或与管理员联系</div>
    <div class="messageUrl">
        现在您可以：
        <a href="javascript:window.history.go(-1);" class='mlink'>[后退]</a>
        <a href="/" class='mlink'>[返回首页]</a>
        <a href="/connect_us/" class='mlink'>[联系管理员]</a>
    </div>

</div>