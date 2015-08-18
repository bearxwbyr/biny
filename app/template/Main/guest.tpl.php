<? include dirname(__DIR__)."/Base/common.tpl.php" ?>
<? include dirname(__DIR__)."/Base/header.tpl.php" ?>

<div class="container">
    <div class="error-msg" style="margin-top: 100px; text-align: center">
        <img src="<?=$rootPath?>static/images/source/error.gif">
        <p style="font-size: 20px">
            在使用平台功能前，用户需要进行邮箱验证。<br />
            那么多上流的功能等着你，你还在等什么呢？快去验证吧！
        <p>
        <button class="btn btn-default" type="button" onclick="window.location.href='/account/register/new_user/'">马上验证</button>
        <button class="btn btn-default" type="button" onclick="window.location.href='/'">返回主页</button>
    </div>
</div>

<? include dirname(__DIR__)."/Base/footer.tpl.php" ?>