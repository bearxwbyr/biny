<? include dirname(__DIR__) . "/base/common.tpl.php" ?>
<? include dirname(__DIR__) . "/base/header.tpl.php" ?>

<div class="container">
    <div class="error-msg" style="margin-top: 100px; text-align: center">
        <img src="<?=$rootPath?>static/images/source/error.gif">
        <p style="font-size: 20px">
            <?=$message?>
        <p>
        <button class="btn btn-default" type="button" onclick="window.open('http://wpa.qq.com/msgrd?v=3&uin=924708266&site=qq&menu=yes')">联系客服</button>
        <button class="btn btn-default" type="button" onclick="window.location.href='/'">返回主页</button>
    </div>
</div>

<? include dirname(__DIR__) . "/base/footer.tpl.php" ?>