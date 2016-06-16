<?php
/* @var TXResponse $this */
/* @var TXArray $PRM */
?>
<? include dirname(__DIR__) . "/base/common.tpl.php" ?>
<? include dirname(__DIR__) . "/base/header.tpl.php" ?>

<div class="container">
    <?if (count($PRM['testArr'])){?>
    <table>
        <?foreach ($PRM['testArr'] as $arr){?>
        <tr>
            <td><?=$arr['name']?></td>
            <td><?=date("Y-m-d H:i", $arr['time'])?></td>
        </tr>
        <?}?>
    </table>
    <?}?>
    <div id="csrf"><?=$this->getCsrfToken()?></div>
</div>

<? include dirname(__DIR__) . "/base/footer.tpl.php" ?>
<script type="text/javascript" src="//logger.oa.com/sdk/Logger.sdk.js"></script>
<script type="text/javascript">
    var src;
    $(function(){
        src = parseInt('<?=$src?>');
        var string = <?=$PRM['testArr']->json_encode()?>;
        var xxx = '<?=$PRM['testArr'][4]['name']?>';
        test();
    });

    function test(){
        $.ajax({
            url: '/biny/ajax/main/',
            type: "POST",
            dataType: "json",
            success: function(data){
                console.log(data);
            }
        });
    }
</script>


