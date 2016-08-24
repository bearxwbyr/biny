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
    <br />

    <div style="height: 100px;width: 100px; background-color: #aaaaaa"
         onclick="test('<?=$PRM['test']?>')"></div>
    <br />

    <div style="height: 100px;width: 100px; background-color: #aaaaaa" data-test="<?=$PRM['test']?>" tips="<?=$PRM['test']?>"
         onclick="test2(this)"></div>
    <br />

    <select onchange="test(this.value, '<?=$PRM['test']?>')">
        <option></option>
        <option>1</option>
        <option>2</option>
    </select>
</div>

<? include dirname(__DIR__) . "/base/footer.tpl.php" ?>
<script type="text/javascript" src="//logger.oa.com/sdk/Logger.sdk.js"></script>
<script type="text/javascript">

    var src;
    $(function(){
        src = parseInt('<?=$src?>');
        test();
    });

    function test(){
        $.ajax({
            url: '/ajax/test/form',
            type: "POST",
            data: {id: 10},
            success: function(data){

            }
        })
    }

    function test2(obj){
        console.log($(obj).data('test'))
    }
</script>


