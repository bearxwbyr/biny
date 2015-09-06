<?php
/* @var $this TXResponse */
?>

<?if (!$this->isAjax()){?>
<? include dirname(__DIR__) . "/base/common.tpl.php" ?>
<? include dirname(__DIR__) . "/base/header.tpl.php" ?>

<div class="container">
<?}?>

<div class="messageImage">
    <img src="<?=$rootPath?>static/images/source/error.gif" />
</div>
<div class="messageInfo"><?=$msg?></div>
<div class="messageUrl">
    现在您可以：
    <a href="/" class='mlink'>[返回首页]</a>
</div>

<?if (!$this->isAjax()){?>
</div>
<? include dirname(__DIR__) . "/base/footer.tpl.php" ?>
<?}?>