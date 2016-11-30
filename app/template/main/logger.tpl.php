<? include TXApp::$view_root . "/base/common.tpl.php" ?>
<? include TXApp::$view_root . "/base/header.tpl.php" ?>

<div class="container">
    logger
</div>

<? include TXApp::$view_root . "/base/footer.tpl.php" ?>
<script type="text/javascript" src="<?=$CDN_ROOT?>static/js/Logger.sdk.js"></script>

<script type="text/javascript">
    Logger.getUin('billge');

    Logger.info(['dfdf']);
</script>
