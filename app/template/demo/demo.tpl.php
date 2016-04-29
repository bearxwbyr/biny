<? include dirname(__DIR__) . "/base/common.tpl.php" ?>
<? include dirname(__DIR__) . "/base/header.tpl.php" ?>
<link href="<?=$CDN_ROOT?>static/css/demo.css" rel="stylesheet" type="text/css"/>
<script>
    var _hmt = _hmt || [];
</script>

<a id="skippy" class="sr-only sr-only-focusable" href="#content"><div class="container"><span class="skiplink-text">Skip to main content</span></div></a>

<!-- Docs master nav -->
<header class="navbar navbar-static-top navbar-inverse" id="top" role="banner">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target="#bs-navbar" aria-controls="bs-navbar" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="<?=$webRoot?>/demo/" class="navbar-brand">Biny 演示页面</a>
        </div>
    </div>
</header>

<div class="container bs-docs-container">

<div class="row">
<div class="col-md-9" role="main">
    <div class="bs-docs-section">
        <h1 id="overview" class="page-header">概览</h1>
        <p>Biny是一个轻量级易用性强的web Server框架</p>

        <h2 id="overview-introduce">介绍</h2>
        <p>支持跨库连表，条件复合筛选，PK缓存查询等</p>
        <p>同步异步请求分离，类的自动化加载管理</p>
        <p>支持Form表单验证</p>
        <p>支持事件触发机制</p>
        <p>具有sql防注入，html防xss等特性</p>
        <p>高性能，框架响应时间在1ms以内，tps轻松上3000</p>
        <p>公共组件地址：<a href="http://pub.code.oa.com/project/home?comeFrom=104&projectName=Biny">http://pub.code.oa.com/project/home?comeFrom=104&projectName=Biny</a></p>
        <p>GitHub 地址：<a href="https://github.com/billge1205/biny">https://github.com/billge1205/biny</a></p>

        <h2 id="overview-files">目录结构</h2>
        <div class="col-lg-3"><img src="http://r.photo.store.qq.com/psb?/V130E8h51JH2da/.9gsh.Yw9u4O9rrwwiJTWNYEVPxTBA0eCwr0fNvGjcE!/o/dGIAAAAAAAAA&bo=yQAVAskAFQIDACU!"></div>
        <div class="col-lg-8" style="margin-left: 20px">
            <p><code>/app/</code> 总工作目录</p>
            <p><code>/app/config/</code> 业务配置层</p>
            <p><code>/app/controller/</code> 路由入口Action层</p>
            <p><code>/app/dao/</code> 数据库表实例层</p>
            <p><code>/app/event/</code> 事件触发及定义层</p>
            <p><code>/app/form/</code> 表单定义及验证层</p>
            <p><code>/app/model/</code> 自定义模型层</p>
            <p><code>/app/service/</code> 业务逻辑层</p>
            <p><code>/app/template/</code> 页面渲染层</p>
            <p><code>/config/</code> 框架配置层</p>
            <p><code>/lib/</code> 系统Lib层</p>
            <p><code>/lib/vendor/</code> 自定义系统Lib层</p>
            <p><code>/logs/</code> 工作日志目录</p>
            <p><code>/plugins/</code> 插件目录</p>
            <p><code>/web/</code> 总执行入口</p>
            <p><code>/web/static/</code> 静态资源文件</p>
            <p><code>/web/index.php</code> 总执行文件</p>
        </div>
        <div style="clear: both"></div>

        <h2 id="overview-level">调用关系</h2>
        <p><code>Action</code>为总路由入口，<code>Action</code>可调用私有对象<code>Service</code>业务层 和 <code>DAO</code>数据库层</p>
        <p><code>Service</code>业务层 可调用私有对象<code>DAO</code>数据库层</p>
        <p>程序全局可调用lib库下系统方法，例如：<code>TXLogger</code>（调试组件），<code>TXConfig</code>（配置类），<code>TXConst</code>（常量类）等</p>
        <p><code>TXApp::$base</code>为全局单例类，可全局调用</p>
        <p><code>TXApp::$base->person</code> 为当前用户，可在<code>/app/model/Person.php</code>中定义</p>
        <p><code>TXApp::$base->request</code> 为当前请求，可获取当前类型，数据验证等</p>
        <p><code>TXApp::$base->session</code> 为系统session，可直接获取和复制，设置过期时间</p>
        <p><code>TXApp::$base->memcache</code> 为系统memcache，可直接获取和复制，设置过期时间</p>
        <p><code>TXApp::$base->redis</code> 为系统redis，可直接获取和复制，设置过期时间</p>

        <p>简单示例</p>
        <pre class="code"><span class="nc">/**
* 主页Action
* @property projectService $projectService
* @property projectDAO $projectDAO
*/  </span>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <note>//默认路由index</note>
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>// 获取当前用户</note>
        <prm>$person</prm> = TXApp::<prm>$base</prm>-><prm>person</prm>-><func>get</func>();
        <prm>$members</prm> = TXApp::<prm>$base</prm>-><prm>memcache</prm>-><func>get</func>(<str>'cache_'</str><sys>.</sys><prm>$person</prm>-><prm>project_id</prm>);
        <sys>if</sys> (!<prm>$members</prm>){
            <note>// 获取用户所在项目成员</note>
            <prm>$project</prm> = <prm>$this</prm>-><prm>$projectDAO</prm>-><func>find</func>(<sys>array</sys>(<str>'id'</str>=><prm>$person</prm>-><prm>project_id</prm>));
            <prm>$members</prm> = <prm>$this</prm>-><prm>$projectService</prm>-><func>getMembers</func>(<prm>$project</prm>[<str>'id'</str>]);
            TXApp::<prm>$base</prm>-><prm>memcache</prm>-><func>set</func>(<str>'cache_'</str><sys>.</sys><prm>$person</prm>-><prm>project_id</prm>, <prm>$members</prm>);
        }
        <note>//返回 project/members.tpl.php</note>
        <sys>return</sys> <prm>$this</prm>-><func>display</func>(<str>'project/members'</str>, <sys>array</sys>(<str>'members'</str>=><prm>$members</prm>));
    }
}</pre>
        <p>P.S: 示例中的用法会在下面具体展开介绍</p>

        <h2 id="overview-index">环境配置</h2>
        <p>PHP版本必须在<code>5.5</code>以上，包含<code>5.5</code></p>
        <p>如果需要用到数据库，则需要安装并启用<code>mysqli扩展</code></p>
        <p><code>php.ini</code>配置中则需要把<code>short_open_tag</code>打开</p>
        <p><code>/config/autoload.php</code> 为自动加载配置类，必须具有<code>写权限</code></p>
        <p><code>/logs/</code> 目录为日志记录文件夹，也必须具有<code>写权限</code></p>
        <p>本例子中主要介绍linux下nginx的配置</p>
        <p>nginx根目录需要指向<code>/web/</code>目录下，示例如下</p>
        <pre class="code"><sys>location</sys> / {
    <const>root</const>   /data/billge/biny/web/;
    <act>index</act>  index.php index.html index.htm;
    <act>try_files</act> $uri $uri/ /index.php?$args;
}                </pre>

        <p><code>/web/index.php</code>是程序的主入口，其中有几个关键配置</p>
        <pre class="code"><note>//默认时区配置</note>
<sys>date_default_timezone_set</sys>(<str>'Asia/Shanghai'</str>);
<note>// 开启debug调试模式（会报错）</note>
<sys>defined</sys>(<str>'SYS_DEBUG'</str>) <sys>or</sys> <sys>define</sys>(<str>'SYS_DEBUG'</str>, <sys>true</sys>);
<note>// 开启Logger页面调试</note>
<sys>defined</sys>(<str>'SYS_CONSOLE'</str>) <sys>or</sys> <sys>define</sys>(<str>'SYS_CONSOLE'</str>, <sys>true</sys>);
<note>// dev pre pub 当前环境</note>
<sys>defined</sys>(<str>'SYS_ENV'</str>) <sys>or</sys> <sys>define</sys>(<str>'SYS_ENV'</str>, <str>'dev'</str>);
<note>// 系统维护中。。。</note>
<sys>defined</sys>(<str>'isMaintenance'</str>) <sys>or</sys> <sys>define</sys>(<str>'isMaintenance'</str>, <sys>false</sys>);</pre>

        <p>其中<code>SYS_ENV</code>的环境值也有bool型，方便判断使用</p>
        <pre class="code"><note>// 在\lib\config\TXDefine.php 中配置</note>
<note>// 测试环境</note>
<sys>defined</sys>(<str>'ENV_DEV'</str>) <sys>or define</sys>(<str>'ENV_DEV'</str>, <const>SYS_ENV</const> === 'dev');
<note>// 预发布环境</note>
<sys>defined</sys>(<str>'ENV_PRE'</str>) <sys>or define</sys>(<str>'ENV_PRE'</str>, <const>SYS_ENV</const> === 'pre');
<note>// 线上正式环境</note>
<sys>defined</sys>(<str>'ENV_PUB'</str>) <sys>or define</sys>(<str>'ENV_PUB'</str>, <const>SYS_ENV</const> === 'pub');</pre>
    </div>

    <div class="bs-docs-section">
        <h1 id="router">路由</h1>
        <p>基本MVC架构路由模式，第一层对应<code>action</code>，第二层对应<code>method</code>（默认<code>index</code>）</p>
        <h2 id="router-rule">规则</h2>
        <p>在/app/controller 目录下，文件可以放在任意子目录或孙目录中。但必须确保文件名与类名一直，且不重复</p>
        <p>示例：/app/controller/Main/testAction.php</p>
        <pre class="code"><note>// http://user.openqa.qq.com/biny/test/</note>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <note>//默认路由index</note>
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>//返回 test/test.tpl.php</note>
        <sys>return</sys> <prm>$this</prm>-><func>display</func>(<str>'test/test'</str>);
    }
}</pre>
        <p>同时也能在同一文件内配置多个子路由</p>
        <pre class="code"><note>//子路由查找action_{$router}</note>
<note>// http://user.openqa.qq.com/biny/test/demo1</note>
<sys>public function</sys> <act>action_demo1</act>()
{
    <note>//返回 test/demo1.tpl.php</note>
    <sys>return</sys> <prm>$this</prm>-><func>display</func>(<str>'test/demo1'</str>);
}

<note>// http://user.openqa.qq.com/biny/test/demo2</note>
<sys>public function</sys> <act>action_demo2</act>()
{
    <note>//返回 test/demo2.tpl.php</note>
    <sys>return</sys> <prm>$this</prm>-><func>display</func>(<str>'test/demo2'</str>);
}</pre>

        <h2 id="router-ajax">异步请求</h2>
        <p>异步请求需要在路由中添加/ajax/，系统会自动进行异步验证（csrf）及处理，程序中响应方法则为ajax_{$router}</p>
        <pre class="code"><note>// http://user.openqa.qq.com/biny/ajax/test/demo3</note>
<sys>public function</sys> <act>ajax_demo3</act>()
{
    <prm>$ret</prm> = <sys>array</sys>(<str>'result'</str>=>1);
    <note>//返回 json {"flag": true, "ret": {"result": 1}}</note>
    <sys>return</sys> <prm>$this</prm>-><func>correct</func>(<prm>$ret</prm>);
}</pre>

        <h2 id="router-param">参数传递</h2>
        <p>方法可以直接接收 GET 参数，并可以赋默认值，空则返回null</p>
        <pre class="code"><note>// http://user.openqa.qq.com/biny/test/demo4/?id=33</note>
<sys>public function</sys> <act>action_demo4</act>(<prm>$id</prm>=10, <prm>$type</prm>, <prm>$name</prm>=<str>'biny'</str>)
{
    <note>// 33</note>
    <sys>echo</sys>(<prm>$id</prm>);
    <note>// NULL</note>
    <sys>echo</sys>(<prm>$type</prm>);
    <note>// 'biny'</note>
    <sys>echo</sys>(<prm>$name</prm>);
}</pre>

        <p>同时也可以调用<code>getParam</code>，<code>getGet</code>，<code>getPost</code> 方法获取参数。</p>
        <p><code>getParam($key, $default)</code> 获取GET/POST参数{$key}, 默认值为{$default}</p>
        <p><code>getGet($key, $default)</code> 获取GET参数{$key}, 默认值为{$default}</p>
        <p><code>getPost($key, $default)</code> 获取POST参数{$key}, 默认值为{$default}</p>
        <pre class="code"><note>// http://user.openqa.qq.com/biny/test/demo5/?id=33</note>
<sys>public function</sys> <act>action_demo5</act>()
{
    <note>// NULL</note>
    <sys>echo</sys>(<prm>$this</prm>-><func>getParam</func>(<str>'name'</str>));
    <note>// 'install'</note>
    <sys>echo</sys>(<prm>$this</prm>-><func>getPost</func>(<str>'type'</str>, <str>'install'</str>));
    <note>// 33</note>
    <sys>echo</sys>(<prm>$this</prm>-><func>getGet</func>(<str>'id'</str>, 1));
}</pre>

        <h2 id="router-check">参数验证</h2>
        <p>当<code>$valueCheck</code>字段开启时（默认<code>关闭</code>），
            <code>getParam</code>，<code>getGet</code>，<code>getPost</code> 方法会自动进行参数类型验证</p>
        <p>验证方式采用字符串命名法</p>
        <p>以<code>i</code>开头的参数 必须为数字</p>
        <p>以<code>s</code>开头的参数 必须为字符串</p>
        <p>以<code>o</code>开头的参数 必须为数组/Object</p>
        <p>以<code>b</code>开头的参数 必须为bool型（true/false）</p>
        <p>以<code>d</code>开头的参数 必须为日期时间格式（H:i:s）</p>
        <p>当参数不合法时，系统会抛出异常 <code>Uncaught exception 'TXException' with message 'param Key [itest] checkType Error; string given'</code></p>
        <p>但同时也会阻碍程序继续执行，如果需要关闭单个接口的保护，可以在action中覆写<code>$valueCheck</code>变量</p>
        <pre class="code"><note>// http://user.openqa.qq.com/biny/test/?iId=test</note>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <note>//关闭参数验证</note>
    <sys>protected</sys> <prm>$valueCheck</prm> = <sys>false</sys>;

    <sys>public function</sys> <act>action_index</act>()
    {
        <note>// 不会报错，但会返回0</note>
        <prm>$iId</prm> = <prm>$this</prm>-><func>getParam</func>(<str>'iId'</str>);
    }
}</pre>
        <p>如果全局都不需要该验证，可以在<code>/lib/business/TXAction.php</code>中将 <code>$valueCheck</code>置为<code>false</code></p>

    </div>

    <div class="bs-docs-section">
        <h1 id="config" class="page-header">配置</h1>
        <p>程序配置分两块，一块是系统配置，一块是程序配置</p>
        <p><code>/config/</code> 系统配置路径，用户一般不需要修改（除了默认路由，默认为indexAction，可替换）</p>
        <p><code>/app/config/</code> 程序逻辑配置路径</p>

        <h2 id="config-system">系统配置</h2>
        <p><code>/config/autoload.php</code> 系统自动加载类的配置，会根据用户代码自动生成，无需配置，但必须具有<code>写权限</code></p>
        <p><code>/config/exception.php</code> 系统异常配置类</p>
        <p><code>/config/http.php</code> HTTP请求基本错误码</p>
        <p>用户可通过<code>TXConfig::getConfig</code>方法获取</p>
        <p>简单例子：</p>
        <pre class="code"><note>/config/config.php</note>
<sys>return array</sys>(
    <str>'session_name'</str> => <str>'biny_sessionid'</str>
}

<note>// 程序中获取方式 第二个参数为文件名（默认为config可不传）第三个参数为是否使用别名（默认为true）</note>
TXConfig::<func>getConfig</func>(<str>'session_name'</str>, <str>'config'</str>, <sys>true</sys>);</pre>

        <h2 id="config-app">程序配置</h2>
        <p>程序配置目录在<code>/app/config/</code>中</p>
        <p>默认有<code>dns.php</code>（连接配置） 和 <code>config.php</code>（默认配置路径）</p>
        <p>使用方式也与系统配置基本一致</p>
        <pre class="code"><note>/app/config/dns.php</note>
<sys>return array</sys>(
    <str>'memcache'</str> => <sys>array</sys>(
        <str>'host'</str> => <str>'10.1.163.35'</str>,
        <str>'port'</str> => 12121
    )
}

<note>// 程序中获取方式 第二个参数为文件名（默认为config可不传）第三个参数为是否使用别名（默认为true）</note>
TXConfig::<func>getAppConfig</func>(<str>'memcache'</str>, <str>'dns'</str>);</pre>

        <h2 id="config-env">环境配置</h2>
        <p>系统对不同环境的配置是可以做区分的</p>
        <p>系统配置在<code>/web/index.php</code>中</p>
        <pre class="code"><note>// dev pre pub 当前环境</note>
<sys>defined</sys>(<str>'SYS_ENV'</str>) <sys>or</sys> <sys>define</sys>(<str>'SYS_ENV'</str>, <str>'dev'</str>);</pre>

        <p>当程序调用<code>TXConfig::getConfig</code>时，系统会自动查找对应的配置文件</p>
        <pre class="code"><note>// 当前环境dev 会自动查找 /config/config_dev.php文件</note>
TXConfig::<func>getConfig</func>(<str>'test'</str>, <str>'config'</str>);

<note>// 当前环境pub 会自动查找 /config/dns_pub.php文件</note>
TXConfig::<func>getConfig</func>(<str>'test2'</str>, <str>'dns'</str>);</pre>

        <p>公用配置文件可以放在不添加环境名的文件中，如<code>/config/config.php</code></p>
        <p>在系统中同时存在<code>config.php</code>和<code>config_dev.php</code>时，带有环境配置的文件内容会覆盖通用配置</p>
        <pre class="code"><note>/app/config/dns.php</note>
<sys>return array</sys>(
    <str>'test'</str> => <str>'dns'</str>,
    <str>'demo'</str> => <str>'dns'</str>,
}

<note>/app/config/dns_dev.php</note>
<sys>return array</sys>(
    <str>'test'</str> => <str>'dns_dev</str>
}

<note>// 返回 'dns_dev' </note>
TXConfig::<func>getAppConfig</func>(<str>'test'</str>, <str>'dns'</str>);

<note>// 返回 'dns' </note>
TXConfig::<func>getAppConfig</func>(<str>'demo'</str>, <str>'dns'</str>);</pre>
        <p>系统配置和程序配置中的使用方法相同</p>

        <h2 id="config-alias">别名使用</h2>
        <p>配置中是支持别名的使用的，在别名两边加上<code>@</code>即可</p>
        <p>系统默认有个别名 <code>web</code>会替换当前路径</p>
        <pre class="code"><note>/config/config.php</note>
<sys>return array</sys>(
    <str>'path'</str> => <str>'@web@/my-path/'</str>
}

<note>// 返回 '/biny/my-path/' </note>
TXConfig::<func>getConfig</func>(<str>'path'</str>);</pre>

        <p>用户也可以自定义别名，例如</p>
        <pre class="code"><note>// getConfig 之前执行</note>
TXConfig::<func>setAlias</func>(<str>'time'</str>, <sys>time</sys>());

<note>// config.php</note>
<sys>return array</sys>(
    <str>'path'</str> => <str>'@web@/my-path/?time=@time@'</str>
}

<note>// 返回 '/biny/my-path/?time=1461141347'</note>
TXConfig::<func>getConfig</func>(<str>'path'</str>);

<note>// 返回 '@web@/my-path/?time=@time@'</note>
TXConfig::<func>getConfig</func>(<str>'path'</str>, <str>'config'</str>, <sys>false</sys>);</pre>

        <p>当然如果需要避免别名转义，也可以在<code>TXConfig::getConfig</code>第三个参数传<code>false</code>，就不会执行别名转义了。</p>
    </div>

    <div class="bs-docs-section">
        <h1 id="dao" class="page-header">数据库使用</h1>
        <p>框架要求每个数据库表都需要建一个单独的类，放在<code>/dao</code>目录下。跟其他目录一样，支持多层文件结构，写在子目录或孙目录中，但类名<code>必须唯一</code>。</p>
        <p>所有传入DAO 方法的参数都会自动进行<code>转义</code>，可以完全避免<code>SQL注入</code>的风险</p>
        <p>例如：</p>
        <pre class="code"><note>// testDAO.php 与类名保持一致</note>
<sys>class</sys> testDAO <sys>extends</sys> baseDAO
{
    <note>// 链接库 数组表示主库从库分离：['database', 'slaveDb'] 对应dns里配置 默认为'database'</note>
    <sys>protected</sys> <prm>$dbConfig</prm> = <str>'database'</str>;
    <note>// 表名</note>
    <sys>protected</sys> <prm>$table</prm> = <str>'Biny_Test'</str>;
    <note>// 键值 多键值用数组表示：['id', 'type']</note>
    <sys>protected</sys> <prm>$_pk</prm> = <str>'id'</str>;
    <note>// 是否使用数据库键值缓存，默认false</note>
    <sys>protected</sys> <prm>$_pkCache</prm> = <sys>true</sys>;

    <note>// 分表逻辑，默认为表名直接加上分表id</note>
    <sys>public function</sys> <act>choose</act>(<prm>$id</prm>)
    {
        <prm>$sub</prm> = <prm>$id</prm> <sys>%</sys> 100;
        <prm>$this</prm>-><func>setDbTable</func>(<sys>sprintf</sys>(<str>'%s_%02d'</str>, <prm>$this</prm>-><prm>table</prm>, <prm>$sub</prm>));
        <sys>return</sys> <prm>$this</prm>;
    }
}</pre>

        <h2 id="dao-connect">连接配置</h2>
        <p>数据库库信息都配置在<code>/app/config/dns.php</code>中，也可根据环境配置在<code>dns_dev.php</code>/<code>dns_pre.php</code>/<code>dns_pub.php</code>里面</p>
        <p>基本参数如下：</p>
        <pre class="code"><note>/app/config/dns_dev.php</note>
<sys>return array</sys>(
    <str>'database'</str> => <sys>array</sys>(
        <note>// 库ip</note>
        <str>'host'</str> => <str>'127.0.0.1'</str>,
        <note>// 库名</note>
        <str>'database'</str> => <str>'Biny'</str>,
        <note>// 用户名</note>
        <str>'user'</str> => <str>'root'</str>,
        <note>// 密码</note>
        <str>'password'</str> => <str>'pwd'</str>,
        <note>// 编码格式</note>
        <str>'encode'</str> => <str>'utf8'</str>,
        <note>// 端口号</note>
        <str>'port'</str> => 3306,
    )
)</pre>
        <p>这里同时也可以配置多个，只需要在DAO类中指定该表所选的库即可（默认为<code>'database'</code>）</p>

        <h2 id="dao-simple">基础查询</h2>
        <p>DAO提供了<code>query</code>，<code>find</code>等基本查询方式，使用也相当简单</p>
        <pre class="code"><note>// testAction.php
/**
 * DAO 或者 Service 会自动映射 生成对应类的单例
 * @property testDAO $testDAO
 */</note>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>// 返回 testDAO所对应表的全部内容 格式为二维数组
            [['id'=>1, 'name'=>'xx', 'type'=>2], ['id'=>2, 'name'=>'yy', 'type'=>3]]</note>
        <prm>$datas</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();
        <note>// 第一个参数为返回的字段 [['id'=>1, 'name'=>'xx'], ['id'=>2, 'name'=>'yy']]</note>
        <prm>$datas</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>(<sys>array</sys>(<str>'id'</str>, <str>'name'</str>));
        <note>// 第二个参数返回键值，会自动去重 [1 => ['id'=>1, 'name'=>'xx'], 2 => ['id'=>2, 'name'=>'yy']]</note>
        <prm>$datas</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>(<sys>array</sys>(<str>'id'</str>, <str>'name'</str>), <str>'id'</str>);

        <note>// 返回 表第一条数据 格式为一维 ['id'=>1, 'name'=>'xx', 'type'=>2]</note>
        <prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>find</func>();
        <note>// 参数为返回的字段名 可以为字符串或者数组 ['name'=>'xx']</note>
        <prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>find</func>('name');
    }
}</pre>
        <p>同时还支持<code>count</code>，<code>max</code>，<code>sum</code>，<code>min</code>，<code>avg</code>等基本运算，count带参数即为<code>参数去重后数量</code></p>
        <pre class="code"><note>// count(*) 返回数量</note>
<prm>$count</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>count</func>();
<note>// count(distinct `name`) 返回去重后数量</note>
<prm>$count</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>count</func>(<str>'name'</str>);
<note>// max(`id`)</note>
<prm>$max</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>max</func>(<str>'id'</str>);
<note>// min(`id`)</note>
<prm>$min</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>min</func>(<str>'id'</str>);
<note>// avg(`id`)</note>
<prm>$avg</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>avg</func>(<str>'id'</str>);
<note>// sum(`id`)</note>
<prm>$sum</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>sum</func>(<str>'id'</str>);
</pre>
        <p>这里运算都为简单运算，需要用到复合运算或者多表运算时，建议使用<code>addtion</code>方法</p>


        <h2 id="dao-update">删改数据</h2>
        <p>在单表操作中可以用到删改数据方法，包括<code>update</code>（多联表也可），<code>delete</code>，<code>add</code>等</p>
        <p><code>update</code>方法为更新数据，返回成功（<code>true</code>）或者失败（<code>false</code>），条件内容参考后面<code>选择器</code>的使用</p>
<pre class="code"><note>// update `DATABASE`.`TABLE` set `name`='xxx', `type`=5</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>update</func>(<sys>array</sys>(<str>'name'</str>=><str>'xxx'</str>, <str>'type'</str>=>5));</pre>

        <p><code>delete</code>方法返回成功（<code>true</code>）或者失败（<code>false</code>），条件内容参考后面<code>选择器</code>的使用</p>
<pre class="code"><note>// delete from `DATABASE`.`TABLE`</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>delete</func>();</pre>

        <p><code>add</code>方法 insert成功时默认返回数据库新插入自增ID，第二个参数为<code>false</code>时 返回成功（<code>true</code>）或者失败（<code>false</code>）</p>
<pre class="code"><note>// insert into `DATABASE`.`TABLE` (`name`,`type`) values('test', 1)</note>
<prm>$sets</prm> = <sys>array</sys>(<str>'name'</str>=><str>'test'</str>, <str>'type'</str>=>1);
<note>// false 时返回true/false</note>
<prm>$id</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>add</func>(<prm>$sets</prm>, <sys>false</sys>);</pre>

        <p><code>addCount</code>方法返回成功（<code>true</code>）或者失败（<code>false</code>），相当于<code>update set count += n</code></p>
<pre class="code"><note>// update `DATABASE`.`TABLE` set `type`+=5</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>addCount</func>(<sys>array</sys>(<str>'type'</str>=>5);</pre>

        <p><code>createOrUpdate</code>方法 为添加数据，但当有重复键值时会自动update数据</p>
<pre class="code"><note>// 第一个参数为insert数组，第二个参数为失败时update参数，不传即为第一个参数</note>
<prm>$sets</prm> = <sys>array</sys>(<str>'name'</str>=><str>'test'</str>, <str>'type'</str>=>1);
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>createOrUpdate</func>(<prm>$sets</prm>);</pre>

        <p><code>addList</code>方法 为批量添加数据，返回成功（<code>true</code>）或者失败（<code>false</code>）</p>
<pre class="code"><note>// 参数为批量数据值（二维数组），键值必须统一</note>
<prm>$sets</prm> = <sys>array</sys>(
    <sys>array</sys>(<str>'name'</str>=><str>'test1'</str>, <str>'type'</str>=>1),
    <sys>array</sys>(<str>'name'</str>=><str>'test2'</str>, <str>'type'</str>=>2),
);
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>addList</func>(<prm>$sets</prm>);</pre>

        <h2 id="dao-join">多联表</h2>
        <p>框架支持多连表模型，DAO类都有<code>join</code>（全联接），<code>join</code>（左联接），<code>join</code>（右联接）方法</p>
        <p>参数为联接关系</p>
        <pre class="code"><note>// on `user`.`projectId` = `project`.`id` and `user`.`type` = `project`.`type`</note>
<prm>$DAO</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>, <str>'type'</str>=><str>'type'</str>));</pre>

        <p><code>$DAO</code>可以继续联接，联接第三个表时，联接关系为二维数组，第一个数组对应第一张表与新表关系，第二个数组对应第二张表与新表关系</p>
        <pre class="code"><note>// on `user`.`testId` = `test`.`id` and `project`.`type` = `test`.`status`</note>
<prm>$DAO</prm> = <prm>$DAO</prm>-><func>leftJoin</func>(<prm>$this</prm>-><prm>testDAO</prm>, <sys>array</sys>(
    <sys>array</sys>(<str>'testId'</str>=><str>'id'</str>),
    <sys>array</sys>(<str>'type'</str>=><str>'status'</str>)
));</pre>

        <p>可以继续联接，联接关系同样为二维数组，三个对象分别对应原表与新表关系，无关联则为空，最后的空数组可以<code>省略</code></p>
        <pre class="code"><note>// on `project`.`message` = `message`.`name`</note>
<prm>$DAO</prm> = <prm>$DAO</prm>-><func>rightJoin</func>(<prm>$this</prm>-><prm>messageDAO</prm>, <sys>array</sys>(
    <sys>array</sys>(),
    <sys>array</sys>(<str>'message'</str>=><str>'name'</str>),
<note>//  array()</note>
));</pre>
        <p>以此类推，理论上可以建立任意数量的关联表</p>

        <p>参数有两种写法，上面那种是位置对应表，另外可以根据<code>别名</code>做对应，<code>别名</code>即DAO之前的字符串</p>
        <pre class="code"><note>// on `project`.`message` = `message`.`name` and `user`.`mId` = `message`.`id`</note>
<prm>$DAO</prm> = <prm>$DAO</prm>-><func>rightJoin</func>(<prm>$this</prm>-><prm>messageDAO</prm>, <sys>array</sys>(
    <str>'project'</str> => <sys>array</sys>(<str>'message'</str>=><str>'name'</str>),
    <str>'user'</str> => <sys>array</sys>(<str>'mId'</str>=><str>'id'</str>),
));</pre>


        <p>多联表同样可以使用<code>query</code>，<code>find</code>，<code>count</code>等查询语句。参数则改为<code>二维数组</code>。</p>
        <p>和联表参数一样，参数有两种写法，一种是位置对应表，另一种即<code>别名</code>对应表，同样也可以混合使用。</p>
        <pre class="code"><note>// SELECT `user`.`id` AS 'uId', `user`.`cash`, `project`.`createTime` FROM ...</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>))
    -><func>query</func>(<sys>array</sys>(
      <sys>array</sys>(<str>'id'</str>=><str>'uId'</str>, <str>'cash'</str>),
      <str>'project'</str> => <sys>array</sys>(<str>'createTime'</str>),
    )
);</pre>

        <p>多联表的查询和修改（<code>update</code>，<code>addCount</code>），和单表操作基本一致，需要注意的是单表参数为<code>一维数组</code>，多表则为<code>二维数组</code>，写错会导致执行失败。</p>


        <h2 id="dao-filter">选择器</h2>

        <p>DAO类都可以调用<code>filter</code>（与选择器），<code>merge</code>（或选择器），效果相当于筛选表内数据</p>
        <p>同样选择器支持单表和多表操作，参数中单表为<code>一维数组</code>，多表则为<code>二维数组</code></p>
        <pre class="code"><note>// ... WHERE `user`.`id` = 1 AND `user`.`type` = 'admin'</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>1, <str>'type'</str>=><str>'admin'</str>));</pre>

        <p>而用<code>merge</code>或选择器筛选，条件则用<code>or</code>相连接</p>
        <pre class="code"><note>// ... WHERE `user`.`id` = 1 OR `user`.`type` = 'admin'</note>
<prm>$merge</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>merge</func>(<sys>array</sys>(<str>'id'</str>=>1, <str>'type'</str>=><str>'admin'</str>));</pre>

        <p>同样多表参数也可用<code>别名</code>对应表，用法跟上面一致，这里就不展开了</p>
        <pre class="code"><note>// ... WHERE `user`.`id` = 1 AND `project`.`type` = 'outer'</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>))
    -><func>filter</func>(<sys>array</sys>(
        <sys>array</sys>(<str>'id'</str>=><str>1</str>),
        <sys>array</sys>(<str>'type'</str>=><str>'outer'</str>),
    )
);</pre>

        <p><code>$filter</code>条件可以继续调用<code>filter</code>/<code>merge</code>方法，条件会在原来的基础上继续筛选</p>
        <pre class="code"><note>// ... WHERE (...) OR (`user`.`name` = 'test')</note>
<prm>$filter</prm> = <prm>$filter</prm>-><func>merge</func>(<sys>array</sys>(<str>'name'</str>=><str>'test'</str>);</pre>

        <p><code>$filter</code>条件也可以作为参数传入<code>filter</code>/<code>merge</code>方法。效果为条件的叠加。</p>
        <pre class="code"><note>// ... WHERE (`user`.`id` = 1 AND `user`.`type` = 'admin') OR (`user`.`id` = 2 AND `user`.`type` = 'user')</note>
<prm>$filter1</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>1, <str>'type'</str>=><str>'admin'</str>);
<prm>$filter2</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>2, <str>'type'</str>=><str>'user'</str>));
<prm>$merge</prm> = <prm>$filter1</prm>-><func>merge</func>(<prm>$filter2</prm>);</pre>

        <p>无论是<code>与选择器</code>还是<code>或选择器</code>，条件本身作为参数时，条件自身的<code>DAO</code>必须和被选择对象的<code>DAO</code>保持一致，否者会抛出<code>异常</code></p>

        <p>值得注意的是<code>filter</code>和<code>merge</code>的先后顺序对条件筛选是有影响的</p>
        <p>可以参考下面这个例子</p>
        <pre class="code"><note>// WHERE (`user`.`id`=1 AND `user`.`type`='admin') OR `user`.`id`=2</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>1, <str>'type'</str>=><str>'admin'</str>)-><func>merge</func>(<sys>array</sys>(<str>'id'</str>=>2));

<note>// WHERE `user`.`id`=2 AND (`user`.`id`=1 AND `user`.`type`='admin')</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>merge</func>(<sys>array</sys>(<str>'id'</str>=>2))-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>1, <str>'type'</str>=><str>'admin'</str>);</pre>

        <p>由上述例子可知，添加之间关联符是跟<code>后面</code>的选择器表达式<code>保持一致</code></p>

        <p><code>选择器</code>获取数据跟<code>DAO</code>方法一致，单表的<code>选择器</code>具有单表的所有查询，删改方法，而多表的<code>选择器</code>具有多表的所有查询，修改改方法</p>
        <pre class="code"><note>// UPDATE `DATABASE`.`TABLE` AS `user` SET `user`.`name` = 'test' WHERE `user`.`id` = 1</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>1)-><func>update</func>(<sys>array</sys>(<str>'name'</str>=><str>'test'</str>));

<note>// SELECT * FROM ... WHERE `project`.`type` = 'admin'</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>))
    -><func>filter</func>(<sys>array</sys>(<sys>array</sys>(),<sys>array</sys>(<str>'type'</str>=><str>'admin'</str>)))
    -><func>query</func>();</pre>


        <p>无论是<code>filter</code>还是<code>merge</code>，在执行SQL语句前都<code>不会被执行</code>，不会增加sql负担，可以放心使用。</p>

        <h2 id="dao-extracts">复杂选择</h2>
        <p>除了正常的匹配选择以外，<code>filter</code>，<code>merge</code>里还提供了其他复杂选择器。</p>
        <p>如果数组中值为<code>数组</code>的话，会自动变为<code>in</code>条件语句</p>
        <pre class="code"><note>// WHERE `user`.`type` IN (1,2,3,'test')</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=><sys>array</sys>(1,2,3,<str>'test'</str>)));</pre>

        <p>其他还包括 <code>></code>，<code><</code>，<code>>=</code>，<code><=</code>，<code>!=</code>，<code><></code>，<code>is</code>，<code>not is</code>
            ，同样，多表的情况下需要用<code>二维数组</code>去封装</p>
        <pre class="code"><note>// WHERE `user`.`id` >= 10 AND `user`.`time` >= 1461584562 AND `user`.`type` not is null</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(
    <str>'>='</str>=><sys>array</sys>(<str>'id'</str>=>10, <str>'time'</str>=>1461584562),
    <str>'not is'</str>=><sys>array</sys>(<str>'type'</str>=><sys>NULL</sys>),
));</pre>

        <p>另外，<code>like语句</code>也是支持的，可匹配正则符的开始结尾符，具体写法如下：</p>
        <pre class="code"><note>// WHERE `user`.`name` LIKE '%test%' OR `user`.`type` LIKE 'admin%' OR `user`.`type` LIKE '%admin'</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>merge</func>(<sys>array</sys>(
    <str>'__like__'</str>=><sys>array</sys>(<str>'name'</str>=><str>test</str>, <str>'type'</str>=><str>'^admin'</str>, <str>'type'</str>=><str>'admin$'</str>),
));</pre>

        <p><code>not in</code>语法暂时并未支持，可以暂时使用多个<code>!=</code>或者<code><></code>替代</p>

        <h2 id="dao-group">其他条件</h2>
        <p>在<code>DAO</code>或者<code>选择器</code>里都可以调用条件方法，方法可传递式调用，相同方法内的条件会自动合并</p>
        <p>其中包括<code>group</code>，<code>addition</code>，<code>order</code>，<code>limit</code>，<code>having</code></p>
        <pre class="code"><note>// SELECT avg(`user`.`cash`) AS 'a_c' FROM `TABLE` `user` WHERE ...
                GROUP BY `user`.`id`,`user`.`type` HAVING `a_c` >= 1000 ORDER BY `a_c` DESC, `id` ASC LIMIT 0,10;</note>
<prm>$this</prm>-><prm>userDAO</prm> <note>//->filter(...)</note>
    -><func>addition</func>(<sys>array</sys>(<str>'avg'</str>=><sys>array</sys>(<str>'cash'</str>=><str>'a_c'</str>))
    -><func>group</func>(<sys>array</sys>(<str>'id'</str>, <str>'type'</str>))
    -><func>having</func>(<sys>array</sys>(<str>'>='</str>=><sys>array</sys>(<str>'a_c'</str>, 1000)))
    -><func>order</func>(<sys>array</sys>(<str>'a_c'</str>=><str>'DESC'</str>, <str>'id'</str>=><str>'ASC'</str>))
    <note>// limit 第一个参数为取的条数，第二个参数为起始位置（默认为0）</note>
    -><func>limit</func>(10)
    -><func>query</func>();</pre>

        <p>每次添加条件后都是独立的，<code>不会影响</code>原DAO 或者 选择器，可以放心的使用</p>

        <pre class="code"><note>// 这个对象不会因添加条件而变化</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=><sys>array</sys>(1,2,3,<str>'test'</str>)));
<note>// 2</note>
<prm>$count</prm> = <prm>$filter</prm>-><func>limit</func>(2)-><func>count</func>()
<note>// 4</note>
<prm>$count</prm> = <prm>$filter</prm>-><func>count</func>()
<note>// 100 (user表总行数)</note>
<prm>$count</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>count</func>()</pre>


        <h2 id="dao-command">SQL模版</h2>
        <p>框架中提供了上述<code>选择器</code>，<code>条件语句</code>，<code>联表</code>等，基本覆盖了所有sql语法，但可能还有部分生僻的用法无法被实现，
        于是这里提供了一种SQL模版的使用方式，支持用户自定义SQL语句，但<code>并不推荐用户使用</code>，如果一定要使用的话，请务必自己做好<code>防SQL注入</code></p>

        <p>这里提供了两种方式，<code>select</code>（查询，返回数据），以及<code>command</code>（执行，返回bool）</p>
        <p>方法会自动替换<code>:where</code>和<code>:table</code>字段</p>
        <pre class="code"><note>// select * from `DATABASE`.`TABLE` WHERE ...</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>select</func>(<str>'select * from :table WHERE ...;'</str>);

<note>// update `DATABASE`.`TABLE` `user` set name = 'test' WHERE `user`.`id` = 10 AND type = 2</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>10))
    -><func>command</func>(<str>'update :table set name = 'test' WHERE :where AND type = 2;'</str>)</pre>

        <p>另外还可以添加一些自定义变量，这些变量会自动进行<code>sql转义</code>，防止<code>sql注入</code></p>
        <p>其中键值的替换符为<code>;</code>，例如<code>;key</code>，值的替换符为<code>:</code>，例如<code>:value</code></p>
        <pre class="code"><note>// select `name` from `DATABASE`.`TABLE` WHERE `name`=2</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>select</func>(<str>'select ;key from :table WHERE ;key=:value;'</str>, <sys>array</sys>(<str>'key'</str>=><str>'name'</str>, <str>'value'</str>=>2));</pre>

        <p>同时替换内容也可以是数组，系统会自动替换为以<code>,</code>连接的字符串</p>
        <pre class="code"><note>// select `id`,`name` from `DATABASE`.`TABLE` WHERE `name` in (1,2,3,'test')</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>select</func>(<str>'select ;fields from :table WHERE ;key in :value;'</str>,
    <sys>array</sys>(<str>'key'</str>=><str>'name'</str>, <str>'value'</str>=><sys>array</sys>(1,2,3,<str>'test'</str>), <str>'fields'</str>=><sys>array</sys>(<str>'id'</str>, <str>'name'</str>)));</pre>

        <p>以上替换方式都会进行<code>SQL转义</code>，建议用户使用模版替换，而不要自己将变量放入SQL语句中，防止<code>SQL注入</code></p>

        <h2 id="dao-cache">数据缓存</h2>
        <p>框架这边针对<code>pk键值索引</code>数据可以通过继承<code>baseDAO</code>进行缓存操作，默认为<code>关闭</code>，可在DAO中定义<code>$_pkCache = true</code>来开启</p>
        <p>然后需要在DAO中制定表键值，复合索引需要传<code>数组</code>，例如：<code>['id', 'type']</code></p>
        <p>因为系统缓存默认走<code>redis</code>，所以开启缓存的话，需要在<code>/app/config/dns_xxx.php</code>中配置环境相应的redis配置</p>
        <pre class="code"><note>// testDAO</note>
<sys>class</sys> testDAO <sys>extends</sys> baseDAO
{
    <sys>protected</sys> <prm>$dbConfig</prm> = [<str>'database'</str>, <str>'slaveDb'</str>];
    <sys>protected</sys> <prm>$table</prm> = <str>'Biny_Test'</str>;
    <note>// 表pk字段 复合pk为数组 ['id', 'type']</note>
    <sys>protected</sys> <prm>$_pk</prm> = <str>'id'</str>;
    <note>// 开启pk缓存</note>
    <sys>protected</sys> <prm>$_pkCache</prm> = <sys>true</sys>;
}</pre>

        <p><code>baseDAO</code>中提供了<code>getByPk</code>，<code>updateByPk</code>，<code>deleteByPk</code>，<code>addCountByPk</code>方法，
            当<code>$_pkCache</code>参数为<code>true</code>时，数据会走缓存，加快数据读取速度。</p>

        <p><code>getByPk</code> 读取键值数据，返回一维数组数据</p>
        <pre class="code"><note>//参数为pk值 返回 ['id'=>10, 'name'=>'test', 'time'=>1461845038]</note>
<prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>getByPk</func>(10);

<note>//复合pk需要传数组</note>
<prm>$data</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>getByPk</func>(<sys>array</sys>(10, <str>'test'</str>));</pre>

        <p><code>updateByPk</code> 更新单条数据</p>
        <pre class="code"><note>//参数为pk值,update数组，返回true/false</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>updateByPk</func>(10, <sys>array</sys>(<str>'name'</str>=><str>'test'</str>));</pre>

        <p><code>deleteByPk</code> 删除单条数据</p>
        <pre class="code"><note>//参数为pk值，返回true/false</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>deleteByPk</func>(10);</pre>

        <p><code>addCountByPk</code> 添加字段次数，效果等同<code>addCount()</code>方法：<code>set times = times + 3</code></p>
        <pre class="code"><note>//参数为pk值，添加字段次数，返回true/false</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>addCountByPk</func>(10, <sys>array</sys>(<str>'times'</str>=>3));</pre>

        <p><code>注意：</code>开启<code>$_pkCache</code>的DAO不允许再使用<code>update</code>和<code>delete</code>方法，这样会导致缓存与数据不同步的现象。</p>
        <p>如果该表频繁删改数据，建议关闭<code>$_pkCache</code>字段，或者在删改数据后调用<code>clearCache()</code>方法来清除缓存内容，从而与数据库内容保持同步。</p>


        <h2 id="dao-log">语句调试</h2>
        <p>SQL调试方法已经集成在框架事件中，只需要在需要调试语句的方法前调用<code>TXEvent::on(onSql)</code>就可以在<code>页面控制台</code>中输出sql语句了</p>
        <pre class="code"><note>// one方法绑定一次事件，输出一次后自动释放</note>
TXEvent::<func>one</func>(<const>onSql</const>);
<prm>$datas</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();

<note>// on方法绑定事件，直到off释放前都会有效</note>
TXEvent::<func>on</func>(<const>onSql</const>);
<prm>$datas</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();
<prm>$datas</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();
<prm>$datas</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();
TXEvent::<func>off</func>(<const>onSql</const>);</pre>

        <p>该SQL事件功能还可自行绑定方法，具体用法会在后面<code>事件</code>介绍中详细展开</p>
    </div>

    <div class="bs-docs-section">
        <h1 id="view" class="page-header">页面渲染</h1>
        <p>请在<code>php.ini</code>配置中打开<code>short_open_tag</code>，使用简写模版，提高开发效率</p>
        <p>页面view层目录在<code>/app/template/</code>下面，可以在<code>Action</code>层中通过<code>$this->display()</code>方法返回</p>
        <p>一般<code>Action</code>类都会继承<code>baseAction</code>类，在<code>baseAction</code>中可以将一些页面通用参数一起下发，减少开发，维护成本</p>

        <h2 id="view-param">渲染参数</h2>
        <p><code>display</code>方法有三个参数，第一个为指定<code>template</code>文件，第二个为页面参数数组，第三个为系统类数据(<code>没有可不传</code>)。</p>
        <pre class="code"><note>// 返回/app/template/main/test.tpl.php </note>
<sys>return</sys> <prm>$this</prm>-><func>display</func>(<str>'main/test'</str>, <sys>array</sys>(<str>'test'</str>=>1), <sys>array</sys>(<str>'path'</str>=><str>'/test.png'</str>));

<note>/* /app/template/main/test.tpl.php
返回:
&lt;div class="container">
    &lt;span> 1  &lt;/span>
    &lt;img src="/test.png"/>
&lt;/div> */</note>
<act>&lt;div</act> class="<func>container</func>"<act>&gt;</act>
    <act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'test'</str>]<sys>?&gt;</sys>  <act>&lt;/span&gt;</act>
    <act>&lt;img</act> src="<sys>&lt;?=</sys><prm>$path</prm><sys>?&gt;</sys>"<act>/&gt;</act>
<act>&lt;/div&gt;</act></pre>

        <p>第二个参数的数据都会放到<code>$PRM</code>这个页面对象中。第三个参数则会直接被渲染，适合<code>静态资源地址</code>或者<code>类数据</code></p>

        <h2 id="view-xss">反XSS注入</h2>
        <p>使用框架<code>display</code>方法，自动会进行参数<code>html实例化</code>，防止XSS注入。</p>
        <p><code>$PRM</code>获取参数时有两种写法，普通的数组内容获取，会自动进行<code>转义</code></p>
        <pre><note>// 显示 &lt;div&gt; 源码为 &amp;lt;div&amp;gt;</note>
<act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'test'</str>]<sys>?&gt;</sys>  <act>&lt;/span&gt;</act></pre>

        <p>另外可以用私用参数的方式获取，则不会被转义，适用于需要显示完整页面结构的需求（<code>普通页面不推荐使用，隐患很大</code>）</p>
        <pre><note>// 显示 &lt;div&gt; 源码为 &lt;div&gt; </note>
<act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>-><prm>test</prm><sys>?&gt;</sys>  <act>&lt;/span&gt;</act>
<note>// 效果同上</note>
<act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>-><func>get</func>(<str>'test'</str>)<sys>?&gt;</sys>  <act>&lt;/span&gt;</act></pre>

        <p>在多层数据结构中，也一样可以递归使用</p>
        <pre><note>// 显示 &lt;div&gt; 源码为 &amp;lt;div&amp;gt;</note>
<act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'array'</str>][<str>'key1'</str>]<sys>?&gt;</sys>  <act>&lt;/span&gt;</act>
<act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'array'</str>]-><func>get</func>(0)<sys>?&gt;</sys>  <act>&lt;/span&gt;</act></pre>

        <p>而多层结构数组参数会在使用时<code>自动转义</code>，不使用时则不会进行转义，避免资源浪费，影响渲染效率。</p>


        <p><code>注意：</code>第三个参数必定会进行参数<code>html实例化</code>，如果有参数不需要转义的，请放到第二个参数对象中使用。</p>

        <h2 id="view-func">参数方法</h2>
        <p>渲染参数除了渲染外，还提供了一些原有<code>array</code>的方法，例如：</p>
        <p><code>in_array</code> 判断字段是否在数组中</p>
        <pre class="code"><note>// 等同于 in_array('value', $array)</note>
<sys>&lt;? if </sys>(<prm>$PRM</prm>[<str>'array'</str>]-><func>in_array</func>(<str>'value'</str>) {
    <note>// do something</note>
}<sys>?&gt;</sys></pre>

        <p><code>array_key_exists</code> 判断key字段是否在数组中</p>
        <pre class="code"><note>// 等同于 array_key_exists('key1', $array)</note>
<sys>&lt;? if </sys>(<prm>$PRM</prm>[<str>'array'</str>]-><func>array_key_exists</func>(<str>'key1'</str>) {
    <note>// do something</note>
}<sys>?&gt;</sys></pre>

        <p>其他方法以此类推，使用方式是相同的，其他还有<code>json_encode</code></p>
        <pre><note>// 赋值给js参数 var jsParam = {'test':1, "demo": {"key": "test"}};</note>
<sys>var</sys> <prm>jsParam</prm> = <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'array'</str>]-><func>json_encode</func>()<sys>?&gt;</sys>;</pre>

        <p>判断数组参数是否为空，可以直接调用<code>$PRM['array']()</code>方法判断</p>
        <pre class="code"><note>// 等同于 if ($array)</note>
<sys>&lt;? if </sys>(<prm>$PRM</prm>[<str>'array'</str>]() ) {
    <note>// do something</note>
}<sys>?&gt;</sys></pre>

        <p>其他参数方法可以自行在<code>/lib/data/TXArray.php</code>中进行定义</p>
        <p>比如：定义一个<code>len</code>方法，返回数组长度</p>
        <pre class="code"><note>/lib/data/TXArray.php</note>
<sys>public function</sys> <act>len</act>()
{
    <sys>return count</sys>(<prm>$this</prm>-><prm>storage</prm>);
}</pre>
        <p>然后就可以在<code>tpl</code>中开始使用了</p>
        <pre><note>// 赋值给js参数 var jsParam = 2;</note>
<sys>var</sys> <prm>jsParam</prm> = <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'array'</str>]-><func>len</func>()<sys>?&gt;</sys>;</pre>

    </div>

    <div class="bs-docs-section">
        <h1 id="event" class="page-header">事件</h1>
        <p>事件</p>

        <h2 id="event-init">定义事件</h2>

        <h2 id="event-trigger">触发事件</h2>
        <p>触发事件</p>

        <div style="height: 200px"></div>

    </div>

    <div class="bs-docs-section">
        <h1 id="forms" class="page-header">表单验证</h1>
        <p>表单验证</p>

        <h2 id="forms-type">验证类型</h2>
        <p>验证类型</p>

        <div style="height: 200px"></div>
    </div>

    <div class="bs-docs-section">
        <h1 id="debug" class="page-header">逻辑调试</h1>
        <p>逻辑调试</p>

        <h2 id="debug-log">基本调试</h2>
        <p>基本调试</p>

        <div style="height: 200px"></div>
    </div>

</div>

<div class="col-md-3" role="complementary">
    <nav class="bs-docs-sidebar hidden-print hidden-xs hidden-sm">
        <ul class="nav bs-docs-sidenav">

            <li>
                <a href="#overview">概览</a>
                <ul class="nav">
                    <li><a href="#overview-introduce">介绍</a></li>
                    <li><a href="#overview-files">目录结构</a></li>
                    <li><a href="#overview-level">调用关系</a></li>
                    <li><a href="#overview-index">环境配置</a></li>
                </ul>
            </li>
            <li>
                <a href="#router">路由</a>
                <ul class="nav">
                    <li><a href="#router-rule">规则</a></li>
                    <li><a href="#router-ajax">异步请求</a></li>
                    <li><a href="#router-param">参数获取</a></li>
                    <li><a href="#router-check">参数验证</a></li>
                </ul>
            </li>
            <li>
                <a href="#config">配置</a>
                <ul class="nav">
                    <li><a href="#config-system">系统配置</a></li>
                    <li><a href="#config-app">程序配置</a></li>
                    <li><a href="#config-env">环境配置</a></li>
                    <li><a href="#config-alias">别名使用</a></li>
                </ul>
            </li>
            <li>
                <a href="#dao">数据库使用</a>
                <ul class="nav">
                    <li><a href="#dao-connect">连接配置</a></li>
                    <li><a href="#dao-simple">基础查询</a></li>
                    <li><a href="#dao-update">删改数据</a></li>
                    <li><a href="#dao-join">多联表</a></li>
                    <li><a href="#dao-filter">选择器</a></li>
                    <li><a href="#dao-extracts">复杂选择</a></li>
                    <li><a href="#dao-group">其他条件</a></li>
                    <li><a href="#dao-command">SQL模版</a></li>
                    <li><a href="#dao-cache">数据缓存</a></li>
                    <li><a href="#dao-log">语句调试</a></li>
                </ul>
            </li>
            <li>
                <a href="#view">页面渲染</a>
                <ul class="nav">
                    <li><a href="#view-param">渲染参数</a></li>
                    <li><a href="#view-xss">反XSS注入</a></li>
                    <li><a href="#view-func">参数方法</a></li>
                </ul>
            </li>
            <li>
                <a href="#event">事件</a>
                <ul class="nav">
                    <li><a href="#event-init">定义事件</a></li>
                    <li><a href="#event-trigger">触发事件</a></li>
                </ul>
            </li>
            <li>
                <a href="#forms">表单验证</a>
                <ul class="nav">
                    <li><a href="#forms-type">验证类型</a></li>
                </ul>
            </li>
            <li>
                <a href="#debug">逻辑调试</a>
                <ul class="nav">
                    <li><a href="#debug-log">基本调试</a></li>
                </ul>
            </li>

        </ul>
        <a class="back-to-top" href="#top">
            返回顶部
        </a>

    </nav>
</div>

</div>
</div>

<? include dirname(__DIR__) . "/base/footer.tpl.php" ?>
<script type="text/javascript" src="<?=$CDN_ROOT?>static/js/demo.js"></script>