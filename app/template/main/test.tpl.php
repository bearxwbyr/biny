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


