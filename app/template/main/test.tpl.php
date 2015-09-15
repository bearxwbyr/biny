<? include dirname(__DIR__) . "/base/common.tpl.php" ?>
<? include dirname(__DIR__) . "/base/header.tpl.php" ?>

<div class="container">
    <table>
        <?foreach ($testArr as $key => $val){?>
            <tr>
                <td><?=$val->name?></td>
                <td><?=$val['name']?></td>
            </tr>
        <?}?>
    </table>
    <div><?=$string?></div>
</div>

<? include dirname(__DIR__) . "/base/footer.tpl.php" ?>

<script type="text/javascript">

</script>


