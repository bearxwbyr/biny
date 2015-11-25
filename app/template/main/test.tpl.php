<? include dirname(__DIR__) . "/base/common.tpl.php" ?>
<? include dirname(__DIR__) . "/base/header.tpl.php" ?>

<div class="container">
    <table>
    </table>
    <div><?=$string?></div>
    <div><?=$this->getCsrfToken()?></div>
</div>

<? include dirname(__DIR__) . "/base/footer.tpl.php" ?>

<script type="text/javascript">
    $(function(){
        $.ajax({
            url: '/biny/ajax/test/',
            type: "POST",
            dataType: "json",
            success: function(data){
//                console.log(data);
            }
        });
    });
</script>


