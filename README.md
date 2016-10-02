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
            [/demo/" class="navbar-brand">Biny 演示页面](<?=$webRoot?)
        </div>
    </div>
</header>

<div class="container bs-docs-container">

<div class="row">
<div class="col-md-9" role="main">
    <div class="bs-docs-section">

# <?=_L('概览')?>

<?=_L('Biny是一个轻量级易用性强的web Server框架')?>

## 介绍

支持跨库连表，条件复合筛选，PK缓存查询等

同步异步请求分离，类的自动化加载管理

支持Form表单验证

支持事件触发机制

具有sql防注入，html防xss等特性

高性能，框架响应时间在1ms以内，tps轻松上3000

公共组件地址：[http://pub.code.oa.com/project/home?comeFrom=104&projectName=Biny](http://pub.code.oa.com/project/home?comeFrom=104&projectName=Biny)

GitHub 地址：[https://github.com/billge1205/biny](https://github.com/billge1205/biny)

## 目录结构

        <div class="col-lg-3">![](http://r.photo.store.qq.com/psb?/V130E8h51JH2da/.9gsh.Yw9u4O9rrwwiJTWNYEVPxTBA0eCwr0fNvGjcE!/o/dGIAAAAAAAAA&bo=yQAVAskAFQIDACU!)</div>
        <div class="col-lg-8" style="margin-left: 20px">

`/app/` 总工作目录

`/app/config/` 业务配置层

`/app/controller/` 路由入口Action层

`/app/dao/` 数据库表实例层

`/app/event/` 事件触发及定义层

`/app/form/` 表单定义及验证层

`/app/model/` 自定义模型层

`/app/service/` 业务逻辑层

`/app/template/` 页面渲染层

`/config/` 框架配置层

`/lib/` 系统Lib层

`/lib/vendor/` 自定义系统Lib层

`/logs/` 工作日志目录

`/plugins/` 插件目录

`/web/` 总执行入口

`/web/static/` 静态资源文件

`/web/index.php` 总执行文件

        </div>
        <div style="clear: both"></div>

## 调用关系

`Action`为总路由入口，`Action`可调用私有对象`Service`业务层 和 `DAO`数据库层

`Service`业务层 可调用私有对象`DAO`数据库层

程序全局可调用lib库下系统方法，例如：`TXLogger`（调试组件），`TXConfig`（配置类），`TXConst`（常量类）等

`TXApp::$base`为全局单例类，可全局调用

`TXApp::$base->person` 为当前用户，可在`/app/model/Person.php`中定义

`TXApp::$base->request` 为当前请求，可获取当前地址，客户端ip等

`TXApp::$base->session` 为系统session，可直接获取和复制，设置过期时间

`TXApp::$base->memcache` 为系统memcache，可直接获取和复制，设置过期时间

`TXApp::$base->redis` 为系统redis，可直接获取和复制，设置过期时间

简单示例

        <pre class="code"><span class="nc">/**
* 主页Action
* @property projectService $projectService
* @property projectDAO $projectDAO
*/  </span>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <note>// init方法会在action执行前被执行</note>
    <sys>public function</sys> <act>init</act>()
    {
        <note>// 未登录时调整登录页面</note>
        <sys>if</sys>(!TXApp::<prm>$base</prm>-><prm>person</prm>-><func>exist</func>()){
            <sys>return</sys> TXApp::<prm>$base</prm>-><prm>request</prm>-><func>redirect</func>(<str>'/auth/login/'</str>);
        }
    }

    <note>//默认路由index</note>
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>// 获取当前用户</note>
        <prm>$person</prm> = TXApp::<prm>$base</prm>-><prm>person</prm>-><func>get</func>();
        <prm>$members</prm> = TXApp::<prm>$base</prm>-><prm>memcache</prm>-><func>get</func>(<str>'cache_'</str><sys>.</sys><prm>$person</prm>-><prm>project_id</prm>);
        <sys>if</sys> (!<prm>$members</prm>){
            <note>// 获取用户所在项目成员</note>
            <prm>$project</prm> = <prm>$this</prm>-><prm>projectDAO</prm>-><func>find</func>(<sys>array</sys>(<str>'id'</str>=><prm>$person</prm>-><prm>project_id</prm>));
            <prm>$members</prm> = <prm>$this</prm>-><prm>projectService</prm>-><func>getMembers</func>(<prm>$project</prm>[<str>'id'</str>]);
            TXApp::<prm>$base</prm>-><prm>memcache</prm>-><func>set</func>(<str>'cache_'</str><sys>.</sys><prm>$person</prm>-><prm>project_id</prm>, <prm>$members</prm>);
        }
        <note>//返回 project/members.tpl.php</note>
        <sys>return</sys> <prm>$this</prm>-><func>display</func>(<str>'project/members'</str>, <sys>array</sys>(<str>'members'</str>=><prm>$members</prm>));
    }
}</pre>

P.S: 示例中的用法会在下面具体展开介绍

## 环境配置

PHP版本必须在`5.5`以上，包含`5.5`

如果需要用到数据库，则需要安装并启用`mysqli扩展`

`php.ini`配置中则需要把`short_open_tag`打开

`/config/autoload.php` 为自动加载配置类，必须具有`写权限`

`/logs/` 目录为日志记录文件夹，也必须具有`写权限`

本例子中主要介绍linux下nginx的配置

nginx根目录需要指向`/web/`目录下，示例如下

        <pre class="code"><sys>location</sys> / {
    <const>root</const>   /data/billge/biny/web/;
    <act>index</act>  index.php index.html index.htm;
    <act>try_files</act> $uri $uri/ /index.php?$args;
}                </pre>

`/web/index.php`是程序的主入口，其中有几个关键配置

        <pre class="code"><note>//默认时区配置</note>
<sys>date_default_timezone_set</sys>(<str>'Asia/Shanghai'</str>);
<note>// 开启debug调试模式（会输出异常）</note>
<sys>defined</sys>(<str>'SYS_DEBUG'</str>) <sys>or</sys> <sys>define</sys>(<str>'SYS_DEBUG'</str>, <sys>true</sys>);
<note>// 开启Logger页面调试</note>
<sys>defined</sys>(<str>'SYS_CONSOLE'</str>) <sys>or</sys> <sys>define</sys>(<str>'SYS_CONSOLE'</str>, <sys>true</sys>);
<note>// dev pre pub 当前环境</note>
<sys>defined</sys>(<str>'SYS_ENV'</str>) <sys>or</sys> <sys>define</sys>(<str>'SYS_ENV'</str>, <str>'dev'</str>);
<note>// 系统维护中。。。</note>
<sys>defined</sys>(<str>'isMaintenance'</str>) <sys>or</sys> <sys>define</sys>(<str>'isMaintenance'</str>, <sys>false</sys>);</pre>

其中`SYS_ENV`的环境值也有bool型，方便判断使用

        <pre class="code"><note>// 在\lib\config\TXDefine.php 中配置</note>
<note>// 测试环境</note>
<sys>defined</sys>(<str>'ENV_DEV'</str>) <sys>or define</sys>(<str>'ENV_DEV'</str>, <const>SYS_ENV</const> === 'dev');
<note>// 预发布环境</note>
<sys>defined</sys>(<str>'ENV_PRE'</str>) <sys>or define</sys>(<str>'ENV_PRE'</str>, <const>SYS_ENV</const> === 'pre');
<note>// 线上正式环境</note>
<sys>defined</sys>(<str>'ENV_PUB'</str>) <sys>or define</sys>(<str>'ENV_PUB'</str>, <const>SYS_ENV</const> === 'pub');</pre>
    </div>

    <div class="bs-docs-section">

# 路由

基本MVC架构路由模式，第一层对应`action`，第二层对应`method`（默认`index`）

## 默认路由

在`/app/controller`目录下，文件可以放在任意子目录或孙目录中。但必须确保文件名与类名一致，且不重复

示例：/app/controller/Main/testAction.php

        <pre class="code"><note>// http://biny.oa.com/test/</note>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <note>//默认路由index</note>
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>//返回 test/test.tpl.php</note>
        <sys>return</sys> <prm>$this</prm>-><func>display</func>(<str>'test/test'</str>);
    }
}</pre>

同时也能在同一文件内配置多个子路由

        <pre class="code"><note>//子路由查找action_{$router}</note>
<note>// http://biny.oa.com/test/demo1</note>
<sys>public function</sys> <act>action_demo1</act>()
{
    <note>//返回 test/demo1.tpl.php</note>
    <sys>return</sys> <prm>$this</prm>-><func>display</func>(<str>'test/demo1'</str>);
}

<note>// http://biny.oa.com/test/demo2</note>
<sys>public function</sys> <act>action_demo2</act>()
{
    <note>//返回 test/demo2.tpl.php</note>
    <sys>return</sys> <prm>$this</prm>-><func>display</func>(<str>'test/demo2'</str>);
}</pre>

## 自定义路由

除了上述默认路由方式外还可以自定义路由规则，可在`/config/config.php`中配置

自定义路由规则会先被执行，匹配失败后走默认规则，参数冒号后面的字符串会自动转化为`正则匹配符`

<pre class="code"><note>/config/config.php</note>
<str>'routeRule'</str> => <sys>array</sys>(
    <note>// test/(\d+).html 的路由会自动转发到testAction中的 action_view方法</note>
    <str>'<prm>test</prm>/&lt;<prm>id</prm>:\d+&gt;.html'</str> => <str>'test/view'</str>,
    <note>// 匹配的参数可在转发路由中动态使用</note>
    <str>'<prm>test</prm>/&lt;<prm>method</prm>:[\w_]+&gt;/&lt;<prm>id</prm>:\d+&gt;.html'</str> => <str>'test/&lt;<prm>method</prm>&gt;'</str>,
),

<note>/app/controller/testAction.php</note>
<note>// test/272.html 正则匹配的内容会传入方法</note>
<sys>public function</sys> <act>action_view</act>(<prm>$id</prm>)
{
    <sys>echo</sys> <prm>$id</prm>; <note>// 272</note>
}

<note>// test/my_router/123.html</note>
<sys>public function</sys> <act>action_my_router</act>(<prm>$id</prm>)
{
    <sys>echo</sys> <prm>$id</prm>; <note>// 123</note>
}
</pre>

## 异步请求

异步请求包含POST，ajax等多种请求方式，系统会自动进行`异步验证（csrf）`及处理

程序中响应方法和同步请求保持一致，返回`$this->error()`会自动和同步请求作区分，返回`json数据`

        <pre class="code"><note>// http://biny.oa.com/test/demo3</note>
<sys>public function</sys> <act>action_demo3</act>()
{
    <prm>$ret</prm> = <sys>array</sys>(<str>'result'</str>=>1);
    <note>//返回 json {"flag": true, "ret": {"result": 1}}</note>
    <sys>return</sys> <prm>$this</prm>-><func>correct</func>(<prm>$ret</prm>);

    <note>//返回 json {"flag": false, "error": {"result": 1}}</note>
    <sys>return</sys> <prm>$this</prm>-><func>error</func>(<prm>$ret</prm>);
}</pre>

框架提供了一整套`csrf验证`机制，默认`开启`，可通过在Action中将`$csrfValidate = false`关闭。

        <pre class="code"><note>// http://biny.oa.com/test/</note>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <note>//关闭csrf验证</note>
    <sys>protected</sys> <prm>$csrfValidate</prm> = <sys>false</sys>;

    <note>//默认路由index</note>
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>//返回 test/test.tpl.php</note>
        <sys>return</sys> <prm>$this</prm>-><func>correct</func>();
    }
}</pre>

当csrf验证开启时，前端ajax请求需要预先加载引用`/static/js/main.js`文件，ajax提交时，系统会自动加上验证字段。

POST请求同样也会触发csrf验证，需要在form中添加如下数据字段：

        <pre class="code"><note>// 加在form中提交</note>
<act>&lt;input</act> type="<str>text</str>" name="<str>_csrf</str>" hidden value="<sys>&lt;?=</sys><prm>$this</prm>-><func>getCsrfToken</func>()<sys>?&gt;</sys>"<act>/></act></pre>

同样也可以在js中获取（前提是引用`/static/js/main.js`JS文件），加在POST参数中即可。

        <pre class="code"><sys>var</sys> <prm>_csrf</prm> = <func>getCookie</func>(<str>'csrf-token'</str>);</pre>

## 参数传递

方法可以直接接收 GET 参数，并可以赋默认值，空则返回null

        <pre class="code"><note>// http://biny.oa.com/test/demo4/?id=33</note>
<sys>public function</sys> <act>action_demo4</act>(<prm>$id</prm>=10, <prm>$type</prm>, <prm>$name</prm>=<str>'biny'</str>)
{
    <note>// 33</note>
    <sys>echo</sys>(<prm>$id</prm>);
    <note>// NULL</note>
    <sys>echo</sys>(<prm>$type</prm>);
    <note>// 'biny'</note>
    <sys>echo</sys>(<prm>$name</prm>);
}</pre>

同时也可以调用`getParam`，`getGet`，`getPost` 方法获取参数。

`getParam($key, $default)` 获取GET/POST参数{$key}, 默认值为{$default}

`getGet($key, $default)` 获取GET参数{$key}, 默认值为{$default}

`getPost($key, $default)` 获取POST参数{$key}, 默认值为{$default}

`getJson($key, $default)` 如果传递过来的参数为完整json流可使用该方法获取

        <pre class="code"><note>// http://biny.oa.com/test/demo5/?id=33</note>
<sys>public function</sys> <act>action_demo5</act>()
{
    <note>// NULL</note>
    <sys>echo</sys>(<prm>$this</prm>-><func>getParam</func>(<str>'name'</str>));
    <note>// 'install'</note>
    <sys>echo</sys>(<prm>$this</prm>-><func>getPost</func>(<str>'type'</str>, <str>'install'</str>));
    <note>// 33</note>
    <sys>echo</sys>(<prm>$this</prm>-><func>getGet</func>(<str>'id'</str>, 1));
}</pre>

## 权限验证

框架中提供了一套完整的权限验证逻辑，可对路由下所有`method`进行权限验证

用户需要在action中添加`privilege`方法，具体返回字段如下

        <pre class="code"><sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <sys>private</sys> <prm>$key</prm> = <str>'test'</str>;

    <sys>protected function</sys> <act>privilege</act>()
    {
        <sys>return array</sys>(
            <note>// 登录验证（在privilegeService中定义）</note>
            <str>'login_required'</str> => <sys>array</sys>(
                <str>'actions'</str> => <str>'*'</str>, <note>// 绑定action，*为所有method</note>
                <str>'params'</str> => [],   <note>// 传参（能获取到$this，不用另外传）可不传</note>
                <str>'callBack'</str> => [], <note>// 验证失败回调函数， 可不传</note>
            ),
            <str>'my_required'</str> => <sys>array</sys>(
                <str>'actions'</str> => [<str>'index'</str>], <note>// 对action_index进行验证</note>
                <str>'params'</str> => [<prm>$this</prm>-><prm>key</prm>],   <note>// 传参</note>
                <str>'callBack'</str> => [<prm>$this</prm>, <str>'test'</str>], <note>// 验证失败后调用$this->test()</note>
            ),
        );
    }
    <note>// 根据逻辑被调用前会分别进行login_required和my_required验证，都成功后进入该方法</note>
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>// do something</note>
    }
    <note>// my_required验证失败后调用, $action为验证失败的action（这里是$this）</note>
    <sys>public function</sys> <act>test</act>(<prm>$action</prm>)
    {
        <note>// do something</note>
    }
}</pre>

然后在`privilegeService`中定义验证方法

        <pre class="code"><note>第一个参数$action为testAction，$key为params传入参数</note>
<sys>public function</sys> <act>my_required</act>(<prm>$action</prm>, <prm>$key</prm>=<sys>NULL</sys>)
{
    <sys>if</sys>(<prm>$key</prm>){
        <note>// 通过校验</note>
        <sys>return</sys> <prm>$this</prm>-><func>correct</func>();
    } <sys>else</sys> {
        <note>// 校验失败，错误信息可通过$this->privilegeService->getError()获取</note>
        <sys>return</sys> <prm>$this</prm>-><func>error</func>(<str>'key not exist'</str>);
    }
}</pre>

`callBack`参数为校验失败时调用的方法，默认不填会抛出错误异常，程序不会再继续执行。

    </div>

    <div class="bs-docs-section">

# 配置

程序配置分两块，一块是系统配置，一块是程序配置

`/config/` 系统配置路径，用户一般不需要修改（除了默认路由，默认为indexAction，可替换）

`/app/config/` 程序逻辑配置路径

## 系统配置

`/config/config.php` 系统基本配置（包括默认路由，自定义路由配置等）

`/config/autoload.php` 系统自动加载类的配置，会根据用户代码自动生成，无需配置，但必须具有`写权限`

`/config/exception.php` 系统异常配置类

`/config/http.php` HTTP请求基本错误码

`/config/database.php` DAO映射配置

用户可通过`TXConfig::getConfig`方法获取

简单例子：

        <pre class="code"><note>/config/config.php</note>
<sys>return array</sys>(
    <str>'session_name'</str> => <str>'biny_sessionid'</str>
}

<note>// 程序中获取方式 第二个参数为文件名（默认为config可不传）第三个参数为是否使用别名（默认为true）</note>
TXConfig::<func>getConfig</func>(<str>'session_name'</str>, <str>'config'</str>, <sys>true</sys>);</pre>

## 程序配置

程序配置目录在`/app/config/`中

默认有`dns.php`（连接配置） 和 `config.php`（默认配置路径）

使用方式也与系统配置基本一致

        <pre class="code"><note>/app/config/dns.php</note>
<sys>return array</sys>(
    <str>'memcache'</str> => <sys>array</sys>(
        <str>'host'</str> => <str>'10.1.163.35'</str>,
        <str>'port'</str> => 12121
    )
}

<note>// 程序中获取方式 第二个参数为文件名（默认为config可不传）第三个参数为是否使用别名（默认为true）</note>
TXConfig::<func>getAppConfig</func>(<str>'memcache'</str>, <str>'dns'</str>);</pre>

## 环境配置

系统对不同环境的配置是可以做区分的

系统配置在`/web/index.php`中

        <pre class="code"><note>// dev pre pub 当前环境</note>
<sys>defined</sys>(<str>'SYS_ENV'</str>) <sys>or</sys> <sys>define</sys>(<str>'SYS_ENV'</str>, <str>'dev'</str>);</pre>

当程序调用`TXConfig::getConfig`时，系统会自动查找对应的配置文件

        <pre class="code"><note>// 当前环境dev 会自动查找 /config/config_dev.php文件</note>
TXConfig::<func>getConfig</func>(<str>'test'</str>, <str>'config'</str>);

<note>// 当前环境pub 会自动查找 /config/dns_pub.php文件</note>
TXConfig::<func>getConfig</func>(<str>'test2'</str>, <str>'dns'</str>);</pre>

公用配置文件可以放在不添加环境名的文件中，如`/config/config.php`

在系统中同时存在`config.php`和`config_dev.php`时，带有环境配置的文件内容会覆盖通用配置

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

系统配置和程序配置中的使用方法相同

## 别名使用

配置中是支持别名的使用的，在别名两边加上`@`即可

系统默认有个别名 `web`会替换当前路径

        <pre class="code"><note>/config/config.php</note>
<sys>return array</sys>(
    <str>'path'</str> => <str>'@web@/my-path/'</str>
}

<note>// 返回 '/biny/my-path/' </note>
TXConfig::<func>getConfig</func>(<str>'path'</str>);</pre>

用户也可以自定义别名，例如

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

当然如果需要避免别名转义，也可以在`TXConfig::getConfig`第三个参数传`false`，就不会执行别名转义了。

    </div>

    <div class="bs-docs-section">

# 数据库使用

框架要求每个数据库表都需要建一个单独的类，放在`/dao`目录下。跟其他目录一样，支持多层文件结构，写在子目录或孙目录中，但类名`必须唯一`。

所有传入DAO 方法的参数都会自动进行`转义`，可以完全避免`SQL注入`的风险

例如：

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

## 连接配置

数据库库信息都配置在`/app/config/dns.php`中，也可根据环境配置在`dns_dev.php`/`dns_pre.php`/`dns_pub.php`里面

基本参数如下：

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
        <note>// 是否长链接（默认关闭）</note>
        <str>'keep-alive'</str> => true,
    )
)</pre>

这里同时也可以配置多个，只需要在DAO类中指定该表所选的库即可（默认为`'database'`）

## DAO映射

上诉DAO都需要写PHP文件，框架这边也提供了一个简易版的映射方式

用户可在`/config/database.php`中配置，示例如下

        <pre class="code"><note>// database.php</note>
<sys>return array</sys>(
    <str>'dbConfig'</str> => array(
        <note>// 相当于创建了一个testDAO.php</note>
        <str>'test'</str> => <str>'Biny_Test'</str>
    )
);</pre>

然后就可以在`Action、Service、Model`各层中使用`testDAO`了

<pre class="code"><note>// testAction.php
/**
* DAO 或者 Service 会自动映射 生成对应类的单例
* @property TXSingleDAO $testDAO
*/</note>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>// 此处的testDAO为映射生成的，没有baseDAO中对于缓存的操作
            [['id'=>1, 'name'=>'xx', 'type'=>2], ['id'=>2, 'name'=>'yy', 'type'=>3]]</note>
        <prm>$datas</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();
    }
}</pre>

需要`注意`的是，映射的DAO不具备设置数据库功能（主从库都是默认的`database`配置）

也不具备缓存操作（`getByPK、updateByPK、deleteByPK`等）的功能

如果需要使用上述功能，还是需要在`dao`目录下创建php文件自定义相关参数

## 基础查询

DAO提供了`query`，`find`等基本查询方式，使用也相当简单

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

同时还支持`count`，`max`，`sum`，`min`，`avg`等基本运算，count带参数即为`参数去重后数量`

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

这里运算都为简单运算，需要用到复合运算或者多表运算时，建议使用`addtion`方法

## 删改数据

在单表操作中可以用到删改数据方法，包括`update`（多联表也可），`delete`，`add`等

`update`方法为更新数据，返回成功（`true`）或者失败（`false`），条件内容参考后面`选择器`的使用

<pre class="code"><note>// update `DATABASE`.`TABLE` set `name`='xxx', `type`=5</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>update</func>(<sys>array</sys>(<str>'name'</str>=><str>'xxx'</str>, <str>'type'</str>=>5));</pre>

`delete`方法返回成功（`true`）或者失败（`false`），条件内容参考后面`选择器`的使用

<pre class="code"><note>// delete from `DATABASE`.`TABLE`</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>delete</func>();</pre>

`add`方法 insert成功时默认返回数据库新插入自增ID，第二个参数为`false`时 返回成功（`true`）或者失败（`false`）

<pre class="code"><note>// insert into `DATABASE`.`TABLE` (`name`,`type`) values('test', 1)</note>
<prm>$sets</prm> = <sys>array</sys>(<str>'name'</str>=><str>'test'</str>, <str>'type'</str>=>1);
<note>// false 时返回true/false</note>
<prm>$id</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>add</func>(<prm>$sets</prm>, <sys>false</sys>);</pre>

`addCount`方法返回成功（`true`）或者失败（`false`），相当于`update set count += n`

<pre class="code"><note>// update `DATABASE`.`TABLE` set `type`+=5</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>addCount</func>(<sys>array</sys>(<str>'type'</str>=>5);</pre>

`createOrUpdate`方法 为添加数据，但当有重复键值时会自动update数据

<pre class="code"><note>// 第一个参数为insert数组，第二个参数为失败时update参数，不传即为第一个参数</note>
<prm>$sets</prm> = <sys>array</sys>(<str>'name'</str>=><str>'test'</str>, <str>'type'</str>=>1);
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>createOrUpdate</func>(<prm>$sets</prm>);</pre>

`addList`方法 为批量添加数据，返回成功（`true`）或者失败（`false`）

<pre class="code"><note>// 参数为批量数据值（二维数组），键值必须统一</note>
<prm>$sets</prm> = <sys>array</sys>(
    <sys>array</sys>(<str>'name'</str>=><str>'test1'</str>, <str>'type'</str>=>1),
    <sys>array</sys>(<str>'name'</str>=><str>'test2'</str>, <str>'type'</str>=>2),
);
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>addList</func>(<prm>$sets</prm>);</pre>

## 多联表

框架支持多连表模型，DAO类都有`join`（全联接），`leftJoin`（左联接），`rightJoin`（右联接）方法

参数为联接关系

        <pre class="code"><note>// on `user`.`projectId` = `project`.`id` and `user`.`type` = `project`.`type`</note>
<prm>$DAO</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>, <str>'type'</str>=><str>'type'</str>));</pre>

`$DAO`可以继续联接，联接第三个表时，联接关系为二维数组，第一个数组对应第一张表与新表关系，第二个数组对应第二张表与新表关系

        <pre class="code"><note>// on `user`.`testId` = `test`.`id` and `project`.`type` = `test`.`status`</note>
<prm>$DAO</prm> = <prm>$DAO</prm>-><func>leftJoin</func>(<prm>$this</prm>-><prm>testDAO</prm>, <sys>array</sys>(
    <sys>array</sys>(<str>'testId'</str>=><str>'id'</str>),
    <sys>array</sys>(<str>'type'</str>=><str>'status'</str>)
));</pre>

可以继续联接，联接关系同样为二维数组，三个对象分别对应原表与新表关系，无关联则为空，最后的空数组可以`省略`

        <pre class="code"><note>// on `project`.`message` = `message`.`name`</note>
<prm>$DAO</prm> = <prm>$DAO</prm>-><func>rightJoin</func>(<prm>$this</prm>-><prm>messageDAO</prm>, <sys>array</sys>(
    <sys>array</sys>(),
    <sys>array</sys>(<str>'message'</str>=><str>'name'</str>),
<note>//  array()</note>
));</pre>

以此类推，理论上可以建立任意数量的关联表

参数有两种写法，上面那种是位置对应表，另外可以根据`别名`做对应，`别名`即DAO之前的字符串

        <pre class="code"><note>// on `project`.`message` = `message`.`name` and `user`.`mId` = `message`.`id`</note>
<prm>$DAO</prm> = <prm>$DAO</prm>-><func>rightJoin</func>(<prm>$this</prm>-><prm>messageDAO</prm>, <sys>array</sys>(
    <str>'project'</str> => <sys>array</sys>(<str>'message'</str>=><str>'name'</str>),
    <str>'user'</str> => <sys>array</sys>(<str>'mId'</str>=><str>'id'</str>),
));</pre>

多联表同样可以使用`query`，`find`，`count`等查询语句。参数则改为`二维数组`。

和联表参数一样，参数有两种写法，一种是位置对应表，另一种即`别名`对应表，同样也可以混合使用。

        <pre class="code"><note>// SELECT `user`.`id` AS 'uId', `user`.`cash`, `project`.`createTime` FROM ...</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>))
    -><func>query</func>(<sys>array</sys>(
      <sys>array</sys>(<str>'id'</str>=><str>'uId'</str>, <str>'cash'</str>),
      <str>'project'</str> => <sys>array</sys>(<str>'createTime'</str>),
    ));</pre>

联表条件中有时需要用到等于固定值的情况，可以通过`on`方法添加

        <pre class="code"><note>// ... on `user`.`projectId` = `project`.`id` and `user`.`type` = 10 and `project`.`cash` > 100</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>))
    -><func>on</func>(<sys>array</sys>(
        <sys>array</sys>(<str>'type'</str>=>10),
        <sys>array</sys>(<str>'cash'</str>=><sys>array</sys>(<str>'>'</str>, 100)),
    ))->query();</pre>

多联表的查询和修改（`update`，`addCount`），和单表操作基本一致，需要注意的是单表参数为`一维数组`，多表则为`二维数组`，写错会导致执行失败。

## 选择器

DAO类都可以调用`filter`（与选择器），`merge`（或选择器），效果相当于筛选表内数据

同样选择器支持单表和多表操作，参数中单表为`一维数组`，多表则为`二维数组`

        <pre class="code"><note>// ... WHERE `user`.`id` = 1 AND `user`.`type` = 'admin'</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>1, <str>'type'</str>=><str>'admin'</str>));</pre>

而用`merge`或选择器筛选，条件则用`or`相连接

        <pre class="code"><note>// ... WHERE `user`.`id` = 1 OR `user`.`type` = 'admin'</note>
<prm>$merge</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>merge</func>(<sys>array</sys>(<str>'id'</str>=>1, <str>'type'</str>=><str>'admin'</str>));</pre>

同样多表参数也可用`别名`对应表，用法跟上面一致，这里就不展开了

        <pre class="code"><note>// ... WHERE `user`.`id` = 1 AND `project`.`type` = 'outer'</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>))
    -><func>filter</func>(<sys>array</sys>(
        <sys>array</sys>(<str>'id'</str>=><str>1</str>),
        <sys>array</sys>(<str>'type'</str>=><str>'outer'</str>),
    ));</pre>

`$filter`条件可以继续调用`filter`/`merge`方法，条件会在原来的基础上继续筛选

        <pre class="code"><note>// ... WHERE (...) OR (`user`.`name` = 'test')</note>
<prm>$filter</prm> = <prm>$filter</prm>-><func>merge</func>(<sys>array</sys>(<str>'name'</str>=><str>'test'</str>);</pre>

`$filter`条件也可以作为参数传入`filter`/`merge`方法。效果为条件的叠加。

        <pre class="code"><note>// ... WHERE (`user`.`id` = 1 AND `user`.`type` = 'admin') OR (`user`.`id` = 2 AND `user`.`type` = 'user')</note>
<prm>$filter1</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>1, <str>'type'</str>=><str>'admin'</str>);
<prm>$filter2</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>2, <str>'type'</str>=><str>'user'</str>));
<prm>$merge</prm> = <prm>$filter1</prm>-><func>merge</func>(<prm>$filter2</prm>);</pre>

无论是`与选择器`还是`或选择器`，条件本身作为参数时，条件自身的`DAO`必须和被选择对象的`DAO`保持一致，否者会抛出`异常`

值得注意的是`filter`和`merge`的先后顺序对条件筛选是有影响的

可以参考下面这个例子

        <pre class="code"><note>// WHERE (`user`.`id`=1 AND `user`.`type`='admin') OR `user`.`id`=2</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>1, <str>'type'</str>=><str>'admin'</str>)-><func>merge</func>(<sys>array</sys>(<str>'id'</str>=>2));

<note>// WHERE `user`.`id`=2 AND (`user`.`id`=1 AND `user`.`type`='admin')</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>merge</func>(<sys>array</sys>(<str>'id'</str>=>2))-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>1, <str>'type'</str>=><str>'admin'</str>);</pre>

由上述例子可知，添加之间关联符是跟`后面`的选择器表达式`保持一致`

`选择器`获取数据跟`DAO`方法一致，单表的`选择器`具有单表的所有查询，删改方法，而多表的`选择器`具有多表的所有查询，修改改方法

        <pre class="code"><note>// UPDATE `DATABASE`.`TABLE` AS `user` SET `user`.`name` = 'test' WHERE `user`.`id` = 1</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>1)-><func>update</func>(<sys>array</sys>(<str>'name'</str>=><str>'test'</str>));

<note>// SELECT * FROM ... WHERE `project`.`type` = 'admin'</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>join</func>(<prm>$this</prm>-><prm>projectDAO</prm>, <sys>array</sys>(<str>'projectId'</str>=><str>'id'</str>))
    -><func>filter</func>(<sys>array</sys>(<sys>array</sys>(),<sys>array</sys>(<str>'type'</str>=><str>'admin'</str>)))
    -><func>query</func>();</pre>

无论是`filter`还是`merge`，在执行SQL语句前都`不会被执行`，不会增加sql负担，可以放心使用。

## 复杂选择

除了正常的匹配选择以外，`filter`，`merge`里还提供了其他复杂选择器。

如果数组中值为`数组`的话，会自动变为`in`条件语句

        <pre class="code"><note>// WHERE `user`.`type` IN (1,2,3,'test')</note>
<prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=><sys>array</sys>(1,2,3,<str>'test'</str>)));</pre>

其他还包括 `>`，`<`，`>=`，`<=`，`!=`，`<>`，`is`，`is not`
            ，同样，多表的情况下需要用`二维数组`去封装

        <pre class="code"><note>// WHERE `user`.`id` >= 10 AND `user`.`time` >= 1461584562 AND `user`.`type` is not null</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(
    <str>'>='</str>=><sys>array</sys>(<str>'id'</str>=>10, <str>'time'</str>=>1461584562),
    <str>'is not'</str>=><sys>array</sys>(<str>'type'</str>=><sys>NULL</sys>),
));</pre>

另外，`like语句`也是支持的，可匹配正则符的开始结尾符，具体写法如下：

        <pre class="code"><note>// WHERE `user`.`name` LIKE '%test%' OR `user`.`type` LIKE 'admin%' OR `user`.`type` LIKE '%admin'</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>merge</func>(<sys>array</sys>(
    <str>'__like__'</str>=><sys>array</sys>(<str>'name'</str>=><str>test</str>, <str>'type'</str>=><str>'^admin'</str>, <str>'type'</str>=><str>'admin$'</str>),
));</pre>

`not in`语法暂时并未支持，可以暂时使用多个`!=`或者`<>`替代

同时`filter/merge`也可以被迭代调用，以应对不确定筛选条件的复杂查询

        <pre class="code"><note>// 某一个返回筛选数据的Action</note>
<prm>$DAO</prm> = <prm>$this</prm>-><prm>userDAO</prm>;
<sys>if </sys>(<prm>$status</prm>=<prm>$this</prm>-><func>getParam</func>(<str>'status'</str>)){
    <prm>$DAO</prm> = <prm>$DAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'status'</str>=><prm>$status</prm>));
}
<sys>if </sys>(<prm>$startTime</prm>=<prm>$this</prm>-><func>getParam</func>(<str>'start'</str>, 0)){
    <prm>$DAO</prm> = <prm>$DAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'>='</str>=><sys>array</sys>(<str>'start'</str>=><prm>$startTime</prm>)));
}
<sys>if </sys>(<prm>$endTime</prm>=<prm>$this</prm>-><func>getParam</func>(<str>'end'</str>, <func>time</func>())){
    <prm>$DAO</prm> = <prm>$DAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'<'</str>=><sys>array</sys>(<str>'end'</str>=><prm>$endTime</prm>)));
}
<note>// 获取复合条件数量</note>
<prm>$count</prm> = <prm>$DAO</prm>-><func>count</func>();
<note>// 获取复合条件前10条数据</note>
<prm>$data</prm> = <prm>$DAO</prm>-><func>limit</func>(10)-><func>query</func>();</pre>

## 其他条件

在`DAO`或者`选择器`里都可以调用条件方法，方法可传递式调用，相同方法内的条件会自动合并

其中包括`group`，`addition`，`order`，`limit`，`having`

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

每次添加条件后都是独立的，`不会影响`原DAO 或者 选择器，可以放心的使用

        <pre class="code"><note>// 这个对象不会因添加条件而变化</note>
<prm>$filter</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=><sys>array</sys>(1,2,3,<str>'test'</str>)));
<note>// 2</note>
<prm>$count</prm> = <prm>$filter</prm>-><func>limit</func>(2)-><func>count</func>()
<note>// 4</note>
<prm>$count</prm> = <prm>$filter</prm>-><func>count</func>()
<note>// 100 (user表总行数)</note>
<prm>$count</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>count</func>()</pre>

## SQL模版

框架中提供了上述`选择器`，`条件语句`，`联表`等，基本覆盖了所有sql语法，但可能还有部分生僻的用法无法被实现，
        于是这里提供了一种SQL模版的使用方式，支持用户自定义SQL语句，但`并不推荐用户使用`，如果一定要使用的话，请务必自己做好`防SQL注入`

这里提供了两种方式，`select`（查询，返回数据），以及`command`（执行，返回bool）

方法会自动替换`:where`和`:table`字段

        <pre class="code"><note>// select * from `DATABASE`.`TABLE` WHERE ...</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>select</func>(<str>'select * from :table WHERE ...;'</str>);

<note>// update `DATABASE`.`TABLE` `user` set name = 'test' WHERE `user`.`id` = 10 AND type = 2</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>filter</func>(<sys>array</sys>(<str>'id'</str>=>10))
    -><func>command</func>(<str>'update :table set name = 'test' WHERE :where AND type = 2;'</str>)</pre>

另外还可以添加一些自定义变量，这些变量会自动进行`sql转义`，防止`sql注入`

其中键值的替换符为`;`，例如`;key`，值的替换符为`:`，例如`:value`

        <pre class="code"><note>// select `name` from `DATABASE`.`TABLE` WHERE `name`=2</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>select</func>(<str>'select ;key from :table WHERE ;key=:value;'</str>, <sys>array</sys>(<str>'key'</str>=><str>'name'</str>, <str>'value'</str>=>2));</pre>

同时替换内容也可以是数组，系统会自动替换为以`,`连接的字符串

        <pre class="code"><note>// select `id`,`name` from `DATABASE`.`TABLE` WHERE `name` in (1,2,3,'test')</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>select</func>(<str>'select ;fields from :table WHERE ;key in :value;'</str>,
    <sys>array</sys>(<str>'key'</str>=><str>'name'</str>, <str>'value'</str>=><sys>array</sys>(1,2,3,<str>'test'</str>), <str>'fields'</str>=><sys>array</sys>(<str>'id'</str>, <str>'name'</str>)));</pre>

以上替换方式都会进行`SQL转义`，建议用户使用模版替换，而不要自己将变量放入SQL语句中，防止`SQL注入`

## 事务处理

框架为DAO提供了一套简单的事务处理机制，默认是关闭的，可以通过`TXDatebase::start()`方法开启

`注意：`请确保连接的数据表是`innodb`的存储引擎，否者事务并不会生效。

在`TXDatebase::start()`之后可以通过`TXDatebase::commit()`来进行完整事务的提交保存，但并不会影响`start`之前的操作

同理，可以通过`TXDatebase::rollback()`进行整个事务的回滚，回滚所有当前未提交的事务

当程序调用`TXDatebase::end()`方法后事务会全部终止，未提交的事务也会自动回滚，另外，程序析构时，也会自动回滚未提交的事务

        <pre class="code"><note>// 在事务开始前的操作都会默认提交，num:0</note>
<prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>update</func>([<str>'num'</str>=>0]);
<note>// 开始事务</note>
TXDatabase::<func>start</func>();
<note>// set num = num+2</note>
<prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>addCount</func>([<str>'num'</str>=>1]);
<prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>addCount</func>([<str>'num'</str>=>1]);
<note>// 回滚事务</note>
TXDatabase::<func>rollback</func>();
<note>// 当前num还是0</note>
<prm>$num</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>find</func>()[<str>'num'</str>];
<note>// set num = num+2</note>
<prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>addCount</func>([<str>'num'</str>=>1]);
<prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>addCount</func>([<str>'num'</str>=>1]);
<note>// 提交事务</note>
TXDatabase::<func>commit</func>();
<note>// num = 2</note>
<prm>$num</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>filter</func>([<str>'id'</str>=>1])-><func>find</func>()[<str>'num'</str>];
<note>// 关闭事务</note>
TXDatabase::<func>end</func>();</pre>

另外，事务的开启并不会影响`select`操作，只对增加，删除，修改操作有影响

## 数据缓存

框架这边针对`pk键值索引`数据可以通过继承`baseDAO`进行缓存操作，默认为`关闭`，可在DAO中定义`$_pkCache = true`来开启

然后需要在DAO中制定表键值，复合索引需要传`数组`，例如：`['id', 'type']`

因为系统缓存默认走`redis`，所以开启缓存的话，需要在`/app/config/dns_xxx.php`中配置环境相应的redis配置

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

`baseDAO`中提供了`getByPk`，`updateByPk`，`deleteByPk`，`addCountByPk`方法，
            当`$_pkCache`参数为`true`时，数据会走缓存，加快数据读取速度。

`getByPk` 读取键值数据，返回一维数组数据

        <pre class="code"><note>//参数为pk值 返回 ['id'=>10, 'name'=>'test', 'time'=>1461845038]</note>
<prm>$data</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>getByPk</func>(10);

<note>//复合pk需要传数组</note>
<prm>$data</prm> = <prm>$this</prm>-><prm>userDAO</prm>-><func>getByPk</func>(<sys>array</sys>(10, <str>'test'</str>));</pre>

`updateByPk` 更新单条数据

        <pre class="code"><note>//参数为pk值,update数组，返回true/false</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>updateByPk</func>(10, <sys>array</sys>(<str>'name'</str>=><str>'test'</str>));</pre>

`deleteByPk` 删除单条数据

        <pre class="code"><note>//参数为pk值，返回true/false</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>deleteByPk</func>(10);</pre>

`addCountByPk` 添加字段次数，效果等同`addCount()`方法：`set times = times + 3`

        <pre class="code"><note>//参数为pk值，添加字段次数，返回true/false</note>
<prm>$result</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>addCountByPk</func>(10, <sys>array</sys>(<str>'times'</str>=>3));</pre>

`注意：`开启`$_pkCache`的DAO不允许再使用`update`和`delete`方法，这样会导致缓存与数据不同步的现象。

如果该表频繁删改数据，建议关闭`$_pkCache`字段，或者在删改数据后调用`clearCache()`方法来清除缓存内容，从而与数据库内容保持同步。

## 语句调试

SQL调试方法已经集成在框架事件中，只需要在需要调试语句的方法前调用`TXEvent::on(onSql)`就可以在`页面控制台`中输出sql语句了

        <pre class="code"><note>// one方法绑定一次事件，输出一次后自动释放</note>
TXEvent::<func>one</func>(<const>onSql</const>);
<prm>$datas</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();

<note>// on方法绑定事件，直到off释放前都会有效</note>
TXEvent::<func>on</func>(<const>onSql</const>);
<prm>$datas</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();
<prm>$datas</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();
<prm>$datas</prm> = <prm>$this</prm>-><prm>testDAO</prm>-><func>query</func>();
TXEvent::<func>off</func>(<const>onSql</const>);</pre>

该SQL事件功能还可自行绑定方法，具体用法会在后面`事件`介绍中详细展开

    </div>

    <div class="bs-docs-section">

# 页面渲染

请在`php.ini`配置中打开`short_open_tag`，使用简写模版，提高开发效率

页面view层目录在`/app/template/`下面，可以在`Action`层中通过`$this->display()`方法返回

一般`Action`类都会继承`baseAction`类，在`baseAction`中可以将一些页面通用参数一起下发，减少开发，维护成本

## 渲染参数

`display`方法有三个参数，第一个为指定`template`文件，第二个为页面参数数组，第三个为系统类数据(`没有可不传`)。

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

第二个参数的数据都会放到`$PRM`这个页面对象中。第三个参数则会直接被渲染，适合`静态资源地址`或者`类数据`

## 自定义TKD

页面TKD一般都默认在`common.tpl.php`定义好，如果页面单独需要修改对应的`title，keywords，description`的话，
            也可以在`TXResponse`生成后对其赋值

        <pre class="code"><prm>$view</prm> = <prm>$this</prm>-><func>display</func>(<str>'main/test'</str>, <prm>$params</prm>);
<prm>$view</prm>-><prm>title</prm> = <str>'Biny'</str>;
<prm>$view</prm>-><prm>keywords</prm> = <str>'biny,php,框架'</str>;
<prm>$view</prm>-><prm>description</prm> = <str>'一款轻量级好用的框架'</str>;
<sys>return</sys> <prm>$view</prm>;</pre>

## 反XSS注入

使用框架`display`方法，自动会进行参数`html实例化`，防止XSS注入。

`$PRM`获取参数时有两种写法，普通的数组内容获取，会自动进行`转义`

        <pre><note>// 显示 &lt;div&gt; 源码为 &amp;lt;div&amp;gt;</note>
<act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'test'</str>]<sys>?&gt;</sys>  <act>&lt;/span&gt;</act></pre>

另外可以用私用参数的方式获取，则不会被转义，适用于需要显示完整页面结构的需求（`普通页面不推荐使用，隐患很大`）

        <pre><note>// 显示 &lt;div&gt; 源码为 &lt;div&gt; </note>
<act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>-><prm>test</prm><sys>?&gt;</sys>  <act>&lt;/span&gt;</act>
<note>// 效果同上</note>
<act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>-><func>get</func>(<str>'test'</str>)<sys>?&gt;</sys>  <act>&lt;/span&gt;</act></pre>

在多层数据结构中，也一样可以递归使用

        <pre><note>// 显示 &lt;div&gt; 源码为 &amp;lt;div&amp;gt;</note>
<act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'array'</str>][<str>'key1'</str>]<sys>?&gt;</sys>  <act>&lt;/span&gt;</act>
<act>&lt;span&gt;</act> <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'array'</str>]-><func>get</func>(0)<sys>?&gt;</sys>  <act>&lt;/span&gt;</act></pre>

而多层结构数组参数会在使用时`自动转义`，不使用时则不会进行转义，避免资源浪费，影响渲染效率。

`注意：`第三个参数必定会进行参数`html实例化`，如果有参数不需要转义的，请放到第二个参数对象中使用。

## 参数方法

渲染参数除了渲染外，还提供了一些原有`array`的方法，例如：

`in_array` 判断字段是否在数组中

        <pre class="code"><note>// 等同于 in_array('value', $array)</note>
<sys>&lt;? if </sys>(<prm>$PRM</prm>[<str>'array'</str>]-><func>in_array</func>(<str>'value'</str>) {
    <note>// do something</note>
}<sys>?&gt;</sys></pre>

`array_key_exists` 判断key字段是否在数组中

        <pre class="code"><note>// 等同于 array_key_exists('key1', $array)</note>
<sys>&lt;? if </sys>(<prm>$PRM</prm>[<str>'array'</str>]-><func>array_key_exists</func>(<str>'key1'</str>) {
    <note>// do something</note>
}<sys>?&gt;</sys></pre>

其他方法以此类推，使用方式是相同的，其他还有`json_encode`

        <pre><note>// 赋值给js参数 var jsParam = {'test':1, "demo": {"key": "test"}};</note>
<sys>var</sys> <prm>jsParam</prm> = <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'array'</str>]-><func>json_encode</func>()<sys>?&gt;</sys>;</pre>

判断数组参数是否为空，可以直接调用`$PRM['array']()`方法判断

        <pre class="code"><note>// 等同于 if ($array)</note>
<sys>&lt;? if </sys>(<prm>$PRM</prm>[<str>'array'</str>]() ) {
    <note>// do something</note>
}<sys>?&gt;</sys></pre>

其他参数方法可以自行在`/lib/data/TXArray.php`中进行定义

比如：定义一个`len`方法，返回数组长度

        <pre class="code"><note>/lib/data/TXArray.php</note>
<sys>public function</sys> <act>len</act>()
{
    <sys>return count</sys>(<prm>$this</prm>-><prm>storage</prm>);
}</pre>

然后就可以在`tpl`中开始使用了

        <pre><note>// 赋值给js参数 var jsParam = 2;</note>
<sys>var</sys> <prm>jsParam</prm> = <sys>&lt;?=</sys><prm>$PRM</prm>[<str>'array'</str>]-><func>len</func>()<sys>?&gt;</sys>;</pre>

    </div>

    <div class="bs-docs-section">

# 事件

框架中提供了事件机制，可以方便全局调用。其中系统默认已提供的有`beforeAction`，`afterAction`，`onException`，`onError`，`onSql`这几个

`beforeAction`为Action执行前执行的事件（比`init()`方法还要早被触发）

`afterAction`为Action执行后执行的事件（会在渲染页面之前触发）

`onException`系统抛出异常时被触发，会传递错误code，在`/config/exception.php`中定义code

`onError`程序调用`$this->error($data)`方法时被触发，传递`$data`参数

`onSql`执行语句时被触发，上述例子中的`TXEvent::on(onSql)`就是使用了该事件

## 定义事件

系统提供了两种定义事件的方式，一种是定义长期事件`$fd = TXEvent::on($event, [$class, $method])`，直到被off之前都会生效。

参数分别为`事件名`，`方法[类，方法名]` 方法可以不传，默认为`TXLogger::event()`方法，会在console中打印

`$fd`返回的是该事件的操作符。在调用off方法时，可以通过传递该操作符解绑该事件。

        <pre class="code"><note>/**
* 主页Action
* @property testService $testService
*/  </note>
<sys>class</sys> testAction <sys>extends</sys> baseAction
{
    <note>//构造函数</note>
    <sys>public function</sys> <act>__construct</act>()
    {
        <note>// 构造函数记得要调用一下父级的构造函数</note>
        <sys>parent</sys>::<func>__construct</func>();
        <note>// 要触发beforeAction事件，必须在init被调用前定义</note>
        TXEvent::<func>on</func>(<const>beforeAction</const>, <sys>array</sys>(<prm>$this</prm>, <str>'test_event'</str>));
    }

    <note>//默认路由index</note>
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>// 绑定testService里的my_event1方法 和 my_event2方法 到 myEvent事件中，两个方法都会被执行，按绑定先后顺序执行</note>
        <prm>$fd1</prm> = TXEvent::<func>on</func>(<str>'myEvent'</str>, <sys>array</sys>(<prm>$this</prm>-><prm>testService</prm>, <str>'my_event1'</str>));
        <prm>$fd2</prm> = TXEvent::<func>on</func>(<str>'myEvent'</str>, <sys>array</sys>(<prm>$this</prm>-><prm>testService</prm>, <str>'my_event2'</str>));

        <note>// do something ..... </note>

        <note>// 解绑myEvent事件的 my_event1方法</note>
        TXEvent::<func>off</func>(<str>'myEvent'</str>, <prm>$fd1</prm>);

        <note>// 解绑myEvent事件，所有绑定在该事件上的方法都不会再被执行</note>
        TXEvent::<func>off</func>(<str>'myEvent'</str>);

        <sys>return</sys> <prm>$this</prm>-><func>error</func>(<str>'测试一下'</str>);
    }

    <note>// 自定义的事件类</note>
    <sys>public function</sys> <act>test_event</act>(<prm>$event</prm>)
    {
        <note>// addLog为写日志的方法</note>
        TXLogger::<func>addLog</func>(<str>'触发beforeAction事件'</str>);
    }
}</pre>

另一种绑定则为一次绑定事件`TXEvent::one()`，调用参数相同，返回`$fd`操作符，当该事件被触发一次后会自动解绑

        <pre><prm>$fd</prm> = TXEvent::<func>one</func>(<str>'myEvent'</str>, <sys>array</sys>(<prm>$this</prm>, <str>'my_event'</str>));</pre>

当然如果想要绑定多次但非长期绑定时，系统也提供了`bind`方法，参数用法类似。

        <pre><note>// 第一个参数绑定方法，第二个为事件名，第三个为绑定次数，触发次数满后自动释放</note>
<prm>$fd</prm> = TXEvent::<func>bind</func>(<sys>array</sys>(<prm>$this</prm>, <str>'my_event'</str>), <str>'myEvent'</str>, <prm>$times</prm>);</pre>

## 触发事件

用户可以自定义事件，同时也可以选择性的触发，可以直接使用`TXEvent::trigger($event, $params)`方法

参数有两个，第一个为触发的事件名，第二个为触发传递的参数，会传递到触发方法中执行

        <pre class="code"><note>// 触发myEvent事件</note>
TXEvent::<func>trigger</func>(<str>'myEvent'</str>, <sys>array</sys>(<func>get_class</func>(<prm>$this</prm>), <str>'test'</str>))

<note>// 定义事件时绑定的方法</note>
<sys>public function</sys> my_event(<prm>$event</prm>, <prm>$params</prm>)
{
    <note>// array('testService', 'test')</note>
    <sys>var_dump</sys>(<prm>$params</prm>);
}</pre>

    </div>

    <div class="bs-docs-section">

# 表单验证

框架提供了一套完整的表单验证解决方案，适用于绝大多数场景。

表单验证支持所有类型的验证以及自定义方法

简单示例：

        <pre class="code"><note>/**
 * @property testService $testService
 * 自定义一个表单验证类型类 继承TXForm
 */</note>
<sys>class</sys> testForm <sys>extends</sys> TXForm
{
    <note>// 定义表单参数，类型及默认值（可不写，默认null）</note>
    <sys>protected</sys> <prm>$_rules</prm> = [
        <note>// id必须为整型, 默认10</note>
        <str>'id'</str>=>[<sys>self</sys>::<prm>typeInt</prm>, 10],
        <note>// name必须非空（包括null, 空字符串）</note>
        <str>'name'</str>=>[<sys>self</sys>::<prm>typeNonEmpty</prm>],
        <note>// 自定义验证方法(valid_testCmp)</note>
        <str>'status'</str>=>[<str>'testCmp'</str>]
    ];

    <note>// 自定义验证方法</note>
    <sys>public function</sys> <act>valid_testCmp</act>()
    {
        <note>// 和Action一样可以调用Service和DAO作为私有方法</note>
        <sys>if</sys> (<prm>$this</prm>-><prm>testService</prm>-><func>checkStatus</func>(<prm>$this</prm>-><prm>status</prm>)){
            <note>// 验证通过</note>
            <sys>return</sys> <prm>$this</prm>-><func>correct</func>();
        } <sys>else</sys> {
            <note>// 验证失败，参数可以通过getError方法获取</note>
            <sys>return</sys> <prm>$this</prm>-><func>error</func>(<str>'非法类型'</str>);
        }
    }
}</pre>

定义完验证类，然后就可以在Action中使用了，可以通过`getForm`方法加载表单

        <pre class="code"><note>// 加载testForm</note>
<prm>$form</prm> = <prm>$this</prm>-><func>getForm</func>(<str>'test'</str>);
<note>// 验证表单字段，true/false</note>
<sys>if</sys> (!<prm>$form</prm>-><func>check</func>()){
    <note>// 获取错误信息</note>
    <prm>$error</prm> = <prm>$form</prm>-><func>getError</func>();
    <sys>return</sys> <prm>$this</prm>-><func>error</func>(<str>'参数错误'</str>);
}
<note>// 获取对应字段</note>
<prm>$status</prm> = <prm>$form</prm>-><prm>status</prm>;
<note>// 获取全部字段 返回数组类型 ['id'=>1, 'name'=>'billge', 'status'=>2]</note>
<prm>$datas</prm> = <prm>$form</prm>-><func>values</func>();
        </pre>

`注意：`在`$_rules`中未定义的字段，无法在`$form`中被获取到，就算不需要验证，也最好定义一下

在很多情况下，表单参数并不是都完全相同的，系统支持`Form复用`，即可以在通用的Form类中自定义一些内容

比如，还是上述例子的testForm，有个类似的表单，但是多了一个字段type，而且对于status的验证方式也需要变化

可以在testForm中添加一个方法

        <pre class="code"><note>// 在testForm中添加</note>
<sys>public function</sys> <act>addType</act>()
{
    <note>// 添加type字段， 默认'default', 规则为非空</note>
    <prm>$this</prm>-><prm>_rules</prm>[<str>'type'</str>] = [<sys>self</sys>::<prm>typeNonEmpty</prm>,<str>'default'</str>];
    <note>// 修改status的判断条件，改为valid_typeCmp()方法验证，记得要写这个方法哦</note>
    <prm>$this</prm>-><prm>_rules</prm>[<str>'status'</str>][0] = <str>'typeCmp'</str>;
}</pre>

然后在Action中加载表单也需要添加`'addType'`作为参数，其他使用方法一致

        <pre class="code"><prm>$form</prm> = <prm>$this</prm>-><func>getForm</func>(<str>'test'</str>, <str>'addType'</str>);</pre>

一个表单验证类里可以写多个附加方法，相互直接并不会有任何影响

## 验证类型

系统提供了7种默认验证方式，验证失败时都会记录错误信息，用户可以通过`getError`方法获取

`self::typeInt` 数字类型，包括整型浮点型，负数

`self::typeBool` 判断是否为true/false

`self::typeArray` 判断是否为数组类型

`self::typeObject` 判断是否为对象数据

`self::typeDate` 判断是否为一个合法的日期

`self::typeDatetime` 判断是否为一个合法的日期时间

`self::typeNonEmpty` 判断是否非空（包括null, 空字符串）

`self::typeRequired` 有该参数即可，可以为空字符串

验证类型几乎涵盖了所有情况，如果有不能满足的类型，用户可以自定义验证方法，上述例子中已有，不再过多阐述

    </div>

    <div class="bs-docs-section">

# 调试

框架中有两种调试方式，一种是在页面控制台中输出的调试，方便用户对应网页调试。

另一种则是和其他框架一样，在日志中调试

## 控制台调试

Biny的一大特色既是这控制台调试方式，用户可以调试自己想要的数据，同时也不会对当前的页面结构产生影响。

调试的开关在`/web/index.php`里

        <pre class="code"><note>// console调试开关，关闭后控制台不会输出内容</note>
<sys>defined</sys>(<str>'SYS_CONSOLE'</str>) <sys>or define</sys>(<str>'SYS_CONSOLE'</str>, <sys>true</sys>);</pre>

控制台调试的方式，同步异步都可以调试，但异步的调试是需要引用`/static/js/main.js`文件，这样异步ajax的请求也会把调试信息输出在控制台里了。

调试方式很简单，全局可以调用`TXLogger::info($message, $key)`，另外还有warn，error，log等

第一个参数为想要调试的内容，同时也支持数组，Object类的输出。第二个参数为调试key，不传默认为`phpLogs`

`TXLogger::info()`消息 输出

`TXLogger::warn()`警告 输出

`TXLogger::error()`异常 输出

`TXLogger::log()`日志 输出

下面是一个简单例子，和控制台的输出结果。结果会因为浏览器不一样而样式不同，效果上是一样的。

        <pre class="code"><note>// 以下代码全局都可以使用</note>
TXLogger::<func>log</func>(<sys>array</sys>(<str>'cc'</str>=><str>'dd'</str>));
TXLogger::<func>error</func>(<str>'this is a error'</str>);
TXLogger::<func>info</func>(<sys>array</sys>(1,2,3,4,5));
TXLogger::<func>warn</func>(<str>"ss"</str>, <str>"warnKey"</str>);</pre>

![](http://km.oa.com/files/photos/captures/201505/1432003538_35_w219_h87.png)

另外`TXLogger`调试类中还支持time，memory的输出，可以使用其对代码性能做优化。

        <pre class="code"><note>// 开始结尾处加上时间 和 memory 就可以获取中间程序消耗的性能了</note>
TXLogger::<func>time</func>(<str>'start-time'</str>);
TXLogger::<func>memory</func>(<str>'start-memory'</str>);
TXLogger::<func>log</func>(<str>'do something'</str>);
TXLogger::<func>time</func>(<str>'end-time'</str>);
TXLogger::<func>memory</func>(<str>'end-memory'</str>);</pre>

![](http://shp.qpic.cn/gqop/20000/LabImage_2ee327c680046dc1d14d7dce5c7bcb45.png/0)

这块调试的内容在KM中也有[相关的文章](http://km.oa.com/group/1746/articles/show/226484)。文章中作为demo的框架代码已经比较老了，仅作参考。

## 日志调试

平台的日志目录在`/logs/`，请确保该目录有`写权限`

异常记录会生成在`error_{日期}.log`文件中，如：`error_2016-05-05.log`

调试记录会生成在`log_{日期}.log`文件中，如：`log_2016-05-05.log`

程序中可以通过调用`TXLogger::addLog($log, INFO)`方法添加日志，`TXLogger::addError($log, ERROR)`方法添加异常

`$log`参数支持传数组，会自动排列打印

`$LEVEL`可使用常量（`INFO`、`DEBUG`、`NOTICE`、`WARNING`、`ERROR`）不填即默认级别

系统程序错误也都会在error日志中显示，如页面出现500时可在错误日志中查看定位

    </div>

    <div class="bs-docs-section">

# 脚本执行

Biny框架除了提供HTTP的请求处理以外，同时还提供了一套完整的脚本执行逻辑

执行入口为根目录下的`shell.php`文件，用户可以通过命令行执行`php shell.php {router} {param}`方式调用

其中`router`为脚本路由，`param`为执行参数，可缺省或多个参数

        <pre class="code"><note>// shell.php</note>
<note>//默认时区配置</note>
<sys>date_default_timezone_set</sys>(<str>'Asia/Shanghai'</str>);
<note>// 开启脚本执行（shell.php固定为true）</note>
<sys>defined</sys>(<str>'RUN_SHELL'</str>) <sys>or</sys> <sys>define</sys>(<str>'RUN_SHELL'</str>, <sys>true</sys>);
<note>// dev pre pub 当前环境</note>
<sys>defined</sys>(<str>'SYS_ENV'</str>) <sys>or</sys> <sys>define</sys>(<str>'SYS_ENV'</str>, <str>'dev'</str>);
</pre>

## 脚本路由

路由跟http请求模式基本保持一致，分为`{module}/{method}`的形式，其中`{method}`可以缺省，默认为`index`

例如：`index/test`就会执行`indexShell`中的`action_test`方法，而`demo`则会执行`demoShell`中的`action_index`方法

如果router缺省的话，默认会读取`/config/config.php`中的router内容作为默认路由

        <pre class="code"><note>// /config/config.php</note>
<sys>return array</sys>(
    <str>'router'</str> => <sys>array</sys>(
        <note>// http 默认路由</note>
        <str>'base_action'</str> => <str>'demo'</str>,
        <note>// shell 默认路由</note>
        <str>'base_shell'</str> => <str>'index'</str>
    )

<note>// /app/shell/indexShell.php</note>
<sys>class</sys> testShell <sys>extends</sys> TXShell
{
    <note>// 和http一样都会先执行init方法</note>
    <sys>public function</sys> <act>init</act>()
    {
        <note>//return 0 或者 不return 则程序继续执行。如果返回其他内容则输出内容后程序终止。</note>
        <sys>return</sys> 0;
    }

    <note>//默认路由index</note>
    <sys>public function</sys> <act>action_index</act>()
    {
        <note>//返回异常，会记录日志并输出在终端</note>
        <sys>return</sys> <prm>$this</prm>-><func>error</func>(<str>'执行错误'</str>);
    }
}
)</pre>

## 脚本参数

脚本执行可传复数的参数，同http请求可在方法中直接捕获，顺序跟参数顺序保持一致，可缺省

另外，可以用`getParam`方法获取对应位置的参数，用法与http模式保持一致

例如：终端执行`php shell.php test/demo 1 2 aaa`，结果如下：

        <pre class="code"><note>// php shell.php test/demo 1 2 aaa</note>
<sys>class</sys> testShell <sys>extends</sys> TXShell
{
    <note>test/demo => testShell/action_demo</note>
    <sys>public function</sys> <act>action_demo</act>(<prm>$prm1</prm>, <prm>$prm2</prm>, <prm>$prm3</prm>, <prm>$prm4</prm>=<str>'default'</str>)
    {
        <note>//1, 2, aaa, default</note>
        <sys>echo</sys> <str>"<prm>$prm1</prm>, <prm>$prm2</prm>, <prm>$prm3</prm>, <prm>$prm4</prm>"</str>;
        <note>//1</note>
        <sys>echo</sys> <prm>$this</prm>-><func>getParam</func>(0);
        <note>//2</note>
        <sys>echo</sys> <prm>$this</prm>-><func>getParam</func>(1);
        <note>//aaa</note>
        <sys>echo</sys> <prm>$this</prm>-><func>getParam</func>(2);
        <note>//default</note>
        <sys>echo</sys> <prm>$this</prm>-><func>getParam</func>(3, <str>'default'</str>);
    }
}</pre>

## 脚本日志

脚本执行不再具有HTTP模式的其他功能，例如`表单验证`，`页面渲染`，`浏览器控制台调试`。所以在`TXLogger`调试类中，`info/error/debug/warning`这几个方法不在有效了

需要调试的可以继续调用`TXLogger::addLog`和`TXLogger::addError`方法来进行写日志的操作

日志目录则保存在`/logs/shell/`目录下，请确保该目录有`写权限`。格式与http模式保持一致。

`注意:`当程序返回`$this->error($msg)`的时候，系统会默认调用`TXLogger::addError($msg)`，请勿重复调用。

    </div>

    <div class="bs-docs-section">

# 其他

系统有很多单例都可以直接通过`TXApp::$base`直接获取

`TXApp::$base->person` 为当前用户，可在`/app/model/Person.php`中定义

`TXApp::$base->request` 为当前请求，可获取当前地址，客户端ip等

`TXApp::$base->session` 为系统session，可直接获取和复制，设置过期时间

`TXApp::$base->memcache` 为系统memcache，可直接获取和复制，设置过期时间

`TXApp::$base->redis` 为系统redis，可直接获取和复制，设置过期时间

## Request

在进入`Controller`层后，`Request`就可以被调用了，以下是几个常用操作

        <pre class="code"><note>// 已请求 /test/demo/ 为例</note>

<note>// 获取Action名 返回test</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getModule</func>();

<note>// 获取Method名 返回action_demo</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getMethod</func>();

<note>// 获取纯Method名 返回demo</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getMethod</func>(<sys>true</sys>);

<note>// 是否异步请求 返回false</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>isAjax</func>();

<note>// 返回当前路径  /test/demo/</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getBaseUrl</func>();

<note>// 返回完整路径  http://biny.oa.com/test/demo/</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getBaseUrl</func>(<sys>true</sys>);

<note>// 获取来源网址 （上一个页面地址）</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getReferrer</func>();

<note>// 获取浏览器UA</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getUserAgent</func>();

<note>// 获取用户IP</note>
TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getUserIP</func>();</pre>

## Session

session的设置和获取都比较简单，在未调用session时，对象不会被创建，避免性能损耗。

        <pre class="code"><note>// 只需要赋值就可以实现session的设置了</note>
TXApp::<prm>$base</prm>-><prm>session</prm>-><prm>testkey</prm> = <str>'test'</str>;
<note>// 获取则是直接去元素，不存在则返回null</note>
<prm>$testKey</prm> = TXApp::<prm>$base</prm>-><prm>session</prm>-><prm>testkey</prm>;</pre>

同时也可以通过方法`close()`来关闭session，避免session死锁的问题

        <pre class="code"><note>// close之后再获取数据时会重新开启session</note>
TXApp::<prm>$base</prm>-><prm>session</prm>-><func>close</func>();</pre>

而`clear()`方法则会清空当前session中的内容

        <pre class="code"><note>// clear之后再获取则为null</note>
TXApp::<prm>$base</prm>-><prm>session</prm>-><func>clear</func>();</pre>

同时session也是支持`isset`判断的

        <pre class="code"><note>// isset 相当于先get 后isset 返回 true/false</note>
<prm>$bool</prm> = <sys>isset</sys>(TXApp::<prm>$base</prm>-><prm>session</prm>-><prm>testKey</prm>);</pre>

## Cookie

cookie的获取和设置都是在`TXApp::$base->request`中完成的，分别提供了`getCookie`和`setCookie`方法

`getCookie`参数为需要的cookie键值，如果不传，则返回全部cookie，以数组结构返回

        <pre class="code"><prm>$param</prm> = TXApp::<prm>$base</prm>-><prm>request</prm>-><func>getCookie</func>(<str>'param'</str>);</pre>

`setCookie`参数有4个，分别为键值，值，过期时间(单位秒)，cookie所属路径，过期时间不传默认1天，路径默认`'/'`

        <pre class="code">TXApp::<prm>$base</prm>-><prm>request</prm>-><func>setCookie</func>(<str>'param'</str>, <str>'test'</str>, 86400, <str>'/'</str>);</pre>

        <div style="height: 200px"></div>
    </div>

</div>

</div>
</div>