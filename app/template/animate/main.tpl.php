<? include dirname(__DIR__) . "/base/common.tpl.php" ?>
<? include dirname(__DIR__) . "/base/header.tpl.php" ?>

<canvas id="testCanvas" width="1024" height="768"></canvas>

<? include dirname(__DIR__) . "/base/footer.tpl.php" ?>
<script type="text/javascript" src="<?=$rootPath?>static/js/createjs.min.js"></script>
<script type="text/javascript">
    var stage;
    $(function(){
        var canvas = document.getElementById("testCanvas");
        stage = new createjs.Stage("testCanvas");
        stage.autoClear = true;


        //添加背景图
        var bg = new createjs.Bitmap(rootPath + 'static/images/animate/bg.jpg');
        stage.addChild(bg);

        var rect = new createjs.Shape();
        rect.graphics.beginFill("#ff0000");
        rect.graphics.drawRect(10, 20, 100, 200);
        rect.graphics.endFill();
        rect.alpha = 0.4;
        stage.addChild(rect);

        var container = new createjs.Container();
        stage.addChild(container);
A
        //增加5个小人，不断做旋转和放缩
        for (var i = 0; i < 5; i++) {
            var man = new createjs.Bitmap(rootPath + 'static/images/animate/gui.png');
            man.regX = 64;
            man.regY = 60;
            man.x = canvas.width / 6 * (i + 1);
            man.y = canvas.height / 5 * 4;
            man.scaleX = man.scaleY = 1;
            container.addChild(man);
            createjs.Tween.get(man, {loop: true}, true)
                .to({rotation: 360, scaleX: 1, scaleY: 2}, 2000).to({rotation: 360, scaleX: 2, scaleY: 1}, 2000);

            man.addEventListener("click", function (event) {
                console.log("click", event.currentTarget);
            }); //监听点击非常方便，位图的透明区域忽略鼠标事件
        }

        stage.update();
        createjs.Ticker.setFPS(60);
        createjs.Ticker.addEventListener("tick", tick);
    });

    //这里有点猥琐，需要用户自行控制舞台不断update更新
    function tick(event) {
        stage.update(event);
    }

</script>
