<?php
/* @var $this TXResponse */
/* @var $PRM TXArray */
?>

<?if (!TXApp::$base->request->isAjax()){?>
<? include dirname(__DIR__) . "/base/common.tpl.php" ?>
<? include dirname(__DIR__) . "/base/header.tpl.php" ?>

<div class="container">
<?}?>

<div class="messageImage">
    <img src="<?=$CDN_ROOT?>static/images/source/error.gif" />
</div>
<div class="messageInfo"><?=$PRM['msg']?></div>
<div class="messageUrl">
    现在您可以：
    <a href="/" class='mlink'>[返回首页]</a>
</div>

<?if (!TXApp::$base->request->isAjax()){?>
</div>
<? include dirname(__DIR__) . "/base/footer.tpl.php" ?>
<?}?>