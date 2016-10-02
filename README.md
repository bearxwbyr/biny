## 介绍

支持跨库连表，条件复合筛选，PK缓存查询等

同步异步请求分离，类的自动化加载管理

支持Form表单验证

支持事件触发机制

具有sql防注入，html防xss等特性

高性能，框架响应时间在1ms以内，tps轻松上3000

GitHub 地址：[https://github.com/billge1205/biny](https://github.com/billge1205/biny)

## 目录结构
![](http://r.photo.store.qq.com/psb?/V130E8h51JH2da/.9gsh.Yw9u4O9rrwwiJTWNYEVPxTBA0eCwr0fNvGjcE!/o/dGIAAAAAAAAA&bo=yQAVAskAFQIDACU!)

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

```
/**
* 主页Action
* @property projectService $projectService
* @property projectDAO $projectDAO
*/  
class testAction extends baseAction
{
    // init方法会在action执行前被执行
    public function init()
    {
        // 未登录时调整登录页面
        if(!TXApp::$base->person->exist()){
            return TXApp::$base->request->redirect('/auth/login/');
        }
    }

    //默认路由index
    public function action_index()
    {
        // 获取当前用户
        $person = TXApp::$base->person->get();
        $members = TXApp::$base->memcache->get('cache_'.$person->project_id);
        if (!$members){
            // 获取用户所在项目成员
            $project = $this->projectDAO->find(array('id'=>$person->project_id));
            $members = $this->projectService->getMembers($project['id']);
            TXApp::$base->memcache->set('cache_'.$person->project_id, $members);
        }
        //返回 project/members.tpl.php
        return $this->display('project/members', array('members'=>$members));
    }
}
```

P.S: 示例中的用法会在下面具体展开介绍

## 环境配置

PHP版本必须在`5.5`以上，包含`5.5`

如果需要用到数据库，则需要安装并启用`mysqli扩展`

`php.ini`配置中则需要把`short_open_tag`打开

`/config/autoload.php` 为自动加载配置类，必须具有`写权限`

`/logs/` 目录为日志记录文件夹，也必须具有`写权限`

本例子中主要介绍linux下nginx的配置

nginx根目录需要指向`/web/`目录下，示例如下
```
location / {
    root   /data/billge/biny/web/;
    index  index.php index.html index.htm;
    try_files $uri $uri/ /index.php?$args;
}         
```

`/web/index.php`是程序的主入口，其中有几个关键配置
```
//默认时区配置
date_default_timezone_set('Asia/Shanghai');
// 开启debug调试模式（会输出异常）
defined('SYS_DEBUG') or define('SYS_DEBUG', true);
// 开启Logger页面调试
defined('SYS_CONSOLE') or define('SYS_CONSOLE', true);
// dev pre pub 当前环境
defined('SYS_ENV') or define('SYS_ENV', 'dev');
// 系统维护中。。。
defined('isMaintenance') or define('isMaintenance', false);
```

其中`SYS_ENV`的环境值也有bool型，方便判断使用
```
// 在\lib\config\TXDefine.php 中配置
// 测试环境
defined('ENV_DEV') or define('ENV_DEV', SYS_ENV === 'dev');
// 预发布环境
defined('ENV_PRE') or define('ENV_PRE', SYS_ENV === 'pre');
// 线上正式环境
defined('ENV_PUB') or define('ENV_PUB', SYS_ENV === 'pub');
```

# 路由

基本MVC架构路由模式，第一层对应`action`，第二层对应`method`（默认`index`）

## 默认路由

在`/app/controller`目录下，文件可以放在任意子目录或孙目录中。但必须确保文件名与类名一致，且不重复

示例：/app/controller/Main/testAction.php
```
// http://biny.oa.com/test/
class testAction extends baseAction
{
    //默认路由index
    public function action_index()
    {
        //返回 test/test.tpl.php
        return $this->display('test/test');
    }
}
```

同时也能在同一文件内配置多个子路由
```
//子路由查找action_{$router}
// http://biny.oa.com/test/demo1
public function action_demo1()
{
    //返回 test/demo1.tpl.php
    return $this->display('test/demo1');
}

// http://biny.oa.com/test/demo2
public function action_demo2()
{
    //返回 test/demo2.tpl.php
    return $this->display('test/demo2');
}
```

## 自定义路由

除了上述默认路由方式外还可以自定义路由规则，可在`/config/config.php`中配置

自定义路由规则会先被执行，匹配失败后走默认规则，参数冒号后面的字符串会自动转化为`正则匹配符`

```
/config/config.php
'routeRule' => array(
    // test/(\d+).html 的路由会自动转发到testAction中的 action_view方法
    'test/<id:\d+>.html' => 'test/view',
    // 匹配的参数可在转发路由中动态使用
    'test/<method:[\w_]+>/<id:\d+>.html' => 'test/<method>',
),

/app/controller/testAction.php
// test/272.html 正则匹配的内容会传入方法
public function action_view($id)
{
    echo $id; // 272
}

// test/my_router/123.html
public function action_my_router($id)
{
    echo $id; // 123
}
```

## 异步请求

异步请求包含POST，ajax等多种请求方式，系统会自动进行`异步验证（csrf）`及处理

程序中响应方法和同步请求保持一致，返回`$this->error()`会自动和同步请求作区分，返回`json数据`

```
// http://biny.oa.com/test/demo3
public function action_demo3()
{
    $ret = array('result'=>1);
    //返回 json {"flag": true, "ret": {"result": 1}}
    return $this->correct($ret);

    //返回 json {"flag": false, "error": {"result": 1}}
    return $this->error($ret);
}
```

框架提供了一整套`csrf验证`机制，默认`开启`，可通过在Action中将`$csrfValidate = false`关闭。

```
// http://biny.oa.com/test/
class testAction extends baseAction
{
    //关闭csrf验证
    protected $csrfValidate = false;

    //默认路由index
    public function action_index()
    {
        //返回 test/test.tpl.php
        return $this->correct();
    }
}
```

当csrf验证开启时，前端ajax请求需要预先加载引用`/static/js/main.js`文件，ajax提交时，系统会自动加上验证字段。

POST请求同样也会触发csrf验证，需要在form中添加如下数据字段：

```
// 加在form中提交
<input type="text" name="_csrf" hidden value="<?=$this->getCsrfToken()?>"/>
```

同样也可以在js中获取（前提是引用`/static/js/main.js`JS文件），加在POST参数中即可。

```
var _csrf = getCookie('csrf-token');
```

## 参数传递

方法可以直接接收 GET 参数，并可以赋默认值，空则返回null

```
// http://biny.oa.com/test/demo4/?id=33
public function action_demo4($id=10, $type, $name='biny')
{
    // 33
    echo($id);
    // NULL
    echo($type);
    // 'biny'
    echo($name);
}
```

同时也可以调用`getParam`，`getGet`，`getPost` 方法获取参数。

`getParam($key, $default)` 获取GET/POST参数{$key}, 默认值为{$default}

`getGet($key, $default)` 获取GET参数{$key}, 默认值为{$default}

`getPost($key, $default)` 获取POST参数{$key}, 默认值为{$default}

`getJson($key, $default)` 如果传递过来的参数为完整json流可使用该方法获取

```
// http://biny.oa.com/test/demo5/?id=33
public function action_demo5()
{
    // NULL
    echo($this->getParam('name'));
    // 'install'
    echo($this->getPost('type', 'install'));
    // 33
    echo($this->getGet('id', 1));
}
```

## 权限验证

框架中提供了一套完整的权限验证逻辑，可对路由下所有`method`进行权限验证

用户需要在action中添加`privilege`方法，具体返回字段如下

```
class testAction extends baseAction
{
    private $key = 'test';

    protected function privilege()
    {
        return array(
            // 登录验证（在privilegeService中定义）
            'login_required' => array(
                'actions' => '*', // 绑定action，*为所有method
                'params' => [],   // 传参（能获取到$this，不用另外传）可不传
                'callBack' => [], // 验证失败回调函数， 可不传
            ),
            'my_required' => array(
                'actions' => ['index'], // 对action_index进行验证
                'params' => [$this->key],   // 传参
                'callBack' => [$this, 'test'], // 验证失败后调用$this->test()
            ),
        );
    }
    // 根据逻辑被调用前会分别进行login_required和my_required验证，都成功后进入该方法
    public function action_index()
    {
        // do something
    }
    // my_required验证失败后调用, $action为验证失败的action（这里是$this）
    public function test($action)
    {
        // do something
    }
}
```

然后在`privilegeService`中定义验证方法

```
第一个参数$action为testAction，$key为params传入参数
public function my_required($action, $key=NULL)
{
    if($key){
        // 通过校验
        return $this->correct();
    } else {
        // 校验失败，错误信息可通过$this->privilegeService->getError()获取
        return $this->error('key not exist');
    }
}
```

`callBack`参数为校验失败时调用的方法，默认不填会抛出错误异常，程序不会再继续执行。

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

```
/config/config.php
return array(
    'session_name' => 'biny_sessionid'
}

// 程序中获取方式 第二个参数为文件名（默认为config可不传）第三个参数为是否使用别名（默认为true）
TXConfig::getConfig('session_name', 'config', true);
```

<note>// 程序中获取方式 第二个参数为文件名（默认为config可不传）第三个参数为是否使用别名（默认为true）</note>
TXConfig::<func>getConfig</func>(<str>'session_name'</str>, <str>'config'</str>, <sys>true</sys>);</pre>

## 程序配置

程序配置目录在`/app/config/`中

默认有`dns.php`（连接配置） 和 `config.php`（默认配置路径）

使用方式也与系统配置基本一致

```
/app/config/dns.php
return array(
    'memcache' => array(
        'host' => '10.1.163.35',
        'port' => 12121
    )
}

// 程序中获取方式 第二个参数为文件名（默认为config可不传）第三个参数为是否使用别名（默认为true）
TXConfig::getAppConfig('memcache', 'dns');
```

<note>// 程序中获取方式 第二个参数为文件名（默认为config可不传）第三个参数为是否使用别名（默认为true）</note>
TXConfig::<func>getAppConfig</func>(<str>'memcache'</str>, <str>'dns'</str>);</pre>

## 环境配置

系统对不同环境的配置是可以做区分的

系统配置在`/web/index.php`中

```
// dev pre pub 当前环境
defined('SYS_ENV') or define('SYS_ENV', 'dev');
```

当程序调用`TXConfig::getConfig`时，系统会自动查找对应的配置文件

```
// 当前环境dev 会自动查找 /config/config_dev.php文件
TXConfig::getConfig('test', 'config');

// 当前环境pub 会自动查找 /config/dns_pub.php文件
TXConfig::getConfig('test2', 'dns');
```

公用配置文件可以放在不添加环境名的文件中，如`/config/config.php`

在系统中同时存在`config.php`和`config_dev.php`时，带有环境配置的文件内容会覆盖通用配置

```
/app/config/dns.php
return array(
    'test' => 'dns',
    'demo' => 'dns',
}

/app/config/dns_dev.php
return array(
    'test' => 'dns_dev
}

// 返回 'dns_dev' 
TXConfig::getAppConfig('test', 'dns');

// 返回 'dns' 
TXConfig::getAppConfig('demo', 'dns');
```

系统配置和程序配置中的使用方法相同

## 别名使用

配置中是支持别名的使用的，在别名两边加上`@`即可

系统默认有个别名 `web`会替换当前路径

```
/config/config.php
return array(
    'path' => '@web@/my-path/'
}

// 返回 '/biny/my-path/' 
TXConfig::getConfig('path');
```

用户也可以自定义别名，例如

```
// getConfig 之前执行
TXConfig::setAlias('time', time());

// config.php
return array(
    'path' => '@web@/my-path/?time=@time@'
}

// 返回 '/biny/my-path/?time=1461141347'
TXConfig::getConfig('path');

// 返回 '@web@/my-path/?time=@time@'
TXConfig::getConfig('path', 'config', false);
```

当然如果需要避免别名转义，也可以在`TXConfig::getConfig`第三个参数传`false`，就不会执行别名转义了。

# 数据库使用

框架要求每个数据库表都需要建一个单独的类，放在`/dao`目录下。跟其他目录一样，支持多层文件结构，写在子目录或孙目录中，但类名`必须唯一`。

所有传入DAO 方法的参数都会自动进行`转义`，可以完全避免`SQL注入`的风险

例如：

```
// testDAO.php 与类名保持一致
class testDAO extends baseDAO
{
    // 链接库 数组表示主库从库分离：['database', 'slaveDb'] 对应dns里配置 默认为'database'
    protected $dbConfig = 'database';
    // 表名
    protected $table = 'Biny_Test';
    // 键值 多键值用数组表示：['id', 'type']
    protected $_pk = 'id';
    // 是否使用数据库键值缓存，默认false
    protected $_pkCache = true;

    // 分表逻辑，默认为表名直接加上分表id
    public function choose($id)
    {
        $sub = $id % 100;
        $this->setDbTable(sprintf('%s_%02d', $this->table, $sub));
        return $this;
    }
}
```

## 连接配置

数据库库信息都配置在`/app/config/dns.php`中，也可根据环境配置在`dns_dev.php`/`dns_pre.php`/`dns_pub.php`里面

基本参数如下：

```
/app/config/dns_dev.php
return array(
    'database' => array(
        // 库ip
        'host' => '127.0.0.1',
        // 库名
        'database' => 'Biny',
        // 用户名
        'user' => 'root',
        // 密码
        'password' => 'pwd',
        // 编码格式
        'encode' => 'utf8',
        // 端口号
        'port' => 3306,
        // 是否长链接（默认关闭）
        'keep-alive' => true,
    )
)
```

这里同时也可以配置多个，只需要在DAO类中指定该表所选的库即可（默认为`'database'`）

## DAO映射

上诉DAO都需要写PHP文件，框架这边也提供了一个简易版的映射方式

用户可在`/config/database.php`中配置，示例如下

```
// database.php
return array(
    'dbConfig' => array(
        // 相当于创建了一个testDAO.php
        'test' => 'Biny_Test'
    )
);
```

然后就可以在`Action、Service、Model`各层中使用`testDAO`了

```
// testAction.php
/**
* DAO 或者 Service 会自动映射 生成对应类的单例
* @property TXSingleDAO $testDAO
*/
class testAction extends baseAction
{
    public function action_index()
    {
        // 此处的testDAO为映射生成的，没有baseDAO中对于缓存的操作
            [['id'=>1, 'name'=>'xx', 'type'=>2], ['id'=>2, 'name'=>'yy', 'type'=>3]]
        $datas = $this->testDAO->query();
    }
}
```

需要`注意`的是，映射的DAO不具备设置数据库功能（主从库都是默认的`database`配置）

也不具备缓存操作（`getByPK、updateByPK、deleteByPK`等）的功能

如果需要使用上述功能，还是需要在`dao`目录下创建php文件自定义相关参数

## 基础查询

DAO提供了`query`，`find`等基本查询方式，使用也相当简单

```
// testAction.php
/**
 * DAO 或者 Service 会自动映射 生成对应类的单例
 * @property testDAO $testDAO
 */
class testAction extends baseAction
{
    public function action_index()
    {
        // 返回 testDAO所对应表的全部内容 格式为二维数组
            [['id'=>1, 'name'=>'xx', 'type'=>2], ['id'=>2, 'name'=>'yy', 'type'=>3]]
        $datas = $this->testDAO->query();
        // 第一个参数为返回的字段 [['id'=>1, 'name'=>'xx'], ['id'=>2, 'name'=>'yy']]
        $datas = $this->testDAO->query(array('id', 'name'));
        // 第二个参数返回键值，会自动去重 [1 => ['id'=>1, 'name'=>'xx'], 2 => ['id'=>2, 'name'=>'yy']]
        $datas = $this->testDAO->query(array('id', 'name'), 'id');

        // 返回 表第一条数据 格式为一维 ['id'=>1, 'name'=>'xx', 'type'=>2]
        $data = $this->testDAO->find();
        // 参数为返回的字段名 可以为字符串或者数组 ['name'=>'xx']
        $data = $this->testDAO->find('name');
    }
}
```

同时还支持`count`，`max`，`sum`，`min`，`avg`等基本运算，count带参数即为`参数去重后数量`

```
// count(*) 返回数量
$count = $this->testDAO->count();
// count(distinct `name`) 返回去重后数量
$count = $this->testDAO->count('name');
// max(`id`)
$max = $this->testDAO->max('id');
// min(`id`)
$min = $this->testDAO->min('id');
// avg(`id`)
$avg = $this->testDAO->avg('id');
// sum(`id`)
$sum = $this->testDAO->sum('id');
```

这里运算都为简单运算，需要用到复合运算或者多表运算时，建议使用`addtion`方法

## 删改数据

在单表操作中可以用到删改数据方法，包括`update`（多联表也可），`delete`，`add`等

`update`方法为更新数据，返回成功（`true`）或者失败（`false`），条件内容参考后面`选择器`的使用

```
// update `DATABASE`.`TABLE` set `name`='xxx', `type`=5
$result = $this->testDAO->update(array('name'=>'xxx', 'type'=>5));
```

`delete`方法返回成功（`true`）或者失败（`false`），条件内容参考后面`选择器`的使用

```
// delete from `DATABASE`.`TABLE`
$result = $this->testDAO->delete();
```

`add`方法 insert成功时默认返回数据库新插入自增ID，第二个参数为`false`时 返回成功（`true`）或者失败（`false`）

```
// insert into `DATABASE`.`TABLE` (`name`,`type`) values('test', 1)
$sets = array('name'=>'test', 'type'=>1);
// false 时返回true/false
$id = $this->testDAO->add($sets, false);
```

`addCount`方法返回成功（`true`）或者失败（`false`），相当于`update set count += n`

```
// update `DATABASE`.`TABLE` set `type`+=5
$result = $this->testDAO->addCount(array('type'=>5);
```

`createOrUpdate`方法 为添加数据，但当有重复键值时会自动update数据

```
// 第一个参数为insert数组，第二个参数为失败时update参数，不传即为第一个参数
$sets = array('name'=>'test', 'type'=>1);
$result = $this->testDAO->createOrUpdate($sets);
```

`addList`方法 为批量添加数据，返回成功（`true`）或者失败（`false`）

```
// 参数为批量数据值（二维数组），键值必须统一
$sets = array(
    array('name'=>'test1', 'type'=>1),
    array('name'=>'test2', 'type'=>2),
);
$result = $this->testDAO->addList($sets);
```

## 多联表

框架支持多连表模型，DAO类都有`join`（全联接），`leftJoin`（左联接），`rightJoin`（右联接）方法

参数为联接关系

```
// on `user`.`projectId` = `project`.`id` and `user`.`type` = `project`.`type`
$DAO = $this->userDAO->join($this->projectDAO, array('projectId'=>'id', 'type'=>'type'));
```

`$DAO`可以继续联接，联接第三个表时，联接关系为二维数组，第一个数组对应第一张表与新表关系，第二个数组对应第二张表与新表关系

```
// on `user`.`testId` = `test`.`id` and `project`.`type` = `test`.`status`
$DAO = $DAO->leftJoin($this->testDAO, array(
    array('testId'=>'id'),
    array('type'=>'status')
));
```

可以继续联接，联接关系同样为二维数组，三个对象分别对应原表与新表关系，无关联则为空，最后的空数组可以`省略`

```
// on `project`.`message` = `message`.`name`
$DAO = $DAO->rightJoin($this->messageDAO, array(
    array(),
    array('message'=>'name'),
//  array()
));
```

以此类推，理论上可以建立任意数量的关联表

参数有两种写法，上面那种是位置对应表，另外可以根据`别名`做对应，`别名`即DAO之前的字符串

```
// on `project`.`message` = `message`.`name` and `user`.`mId` = `message`.`id`
$DAO = $DAO->rightJoin($this->messageDAO, array(
    'project' => array('message'=>'name'),
    'user' => array('mId'=>'id'),
));
```

多联表同样可以使用`query`，`find`，`count`等查询语句。参数则改为`二维数组`。

和联表参数一样，参数有两种写法，一种是位置对应表，另一种即`别名`对应表，同样也可以混合使用。

```
// SELECT `user`.`id` AS 'uId', `user`.`cash`, `project`.`createTime` FROM ...
$this->userDAO->join($this->projectDAO, array('projectId'=>'id'))
    ->query(array(
      array('id'=>'uId', 'cash'),
      'project' => array('createTime'),
    ));
```

联表条件中有时需要用到等于固定值的情况，可以通过`on`方法添加

```
// ... on `user`.`projectId` = `project`.`id` and `user`.`type` = 10 and `project`.`cash` > 100
$this->userDAO->join($this->projectDAO, array('projectId'=>'id'))
    ->on(array(
        array('type'=>10),
        array('cash'=>array('>', 100)),
    ))->query();
```

多联表的查询和修改（`update`，`addCount`），和单表操作基本一致，需要注意的是单表参数为`一维数组`，多表则为`二维数组`，写错会导致执行失败。

## 选择器

DAO类都可以调用`filter`（与选择器），`merge`（或选择器），效果相当于筛选表内数据

同样选择器支持单表和多表操作，参数中单表为`一维数组`，多表则为`二维数组`

```
// ... WHERE `user`.`id` = 1 AND `user`.`type` = 'admin'
$filter = $this->userDAO->filter(array('id'=>1, 'type'=>'admin'));
```

而用`merge`或选择器筛选，条件则用`or`相连接

```
// ... WHERE `user`.`id` = 1 OR `user`.`type` = 'admin'
$merge = $this->userDAO->merge(array('id'=>1, 'type'=>'admin'));
```

同样多表参数也可用`别名`对应表，用法跟上面一致，这里就不展开了

```
// ... WHERE `user`.`id` = 1 AND `project`.`type` = 'outer'
$filter = $this->userDAO->join($this->projectDAO, array('projectId'=>'id'))
    ->filter(array(
        array('id'=>1),
        array('type'=>'outer'),
    ));
```

`$filter`条件可以继续调用`filter`/`merge`方法，条件会在原来的基础上继续筛选

```
// ... WHERE (...) OR (`user`.`name` = 'test')
$filter = $filter->merge(array('name'=>'test');
```

`$filter`条件也可以作为参数传入`filter`/`merge`方法。效果为条件的叠加。

```
// ... WHERE (`user`.`id` = 1 AND `user`.`type` = 'admin') OR (`user`.`id` = 2 AND `user`.`type` = 'user')
$filter1 = $this->userDAO->filter(array('id'=>1, 'type'=>'admin');
$filter2 = $this->userDAO->filter(array('id'=>2, 'type'=>'user'));
$merge = $filter1->merge($filter2);
```

无论是`与选择器`还是`或选择器`，条件本身作为参数时，条件自身的`DAO`必须和被选择对象的`DAO`保持一致，否者会抛出`异常`

值得注意的是`filter`和`merge`的先后顺序对条件筛选是有影响的

可以参考下面这个例子

```
// WHERE (`user`.`id`=1 AND `user`.`type`='admin') OR `user`.`id`=2
$this->userDAO->filter(array('id'=>1, 'type'=>'admin')->merge(array('id'=>2));

// WHERE `user`.`id`=2 AND (`user`.`id`=1 AND `user`.`type`='admin')
$this->userDAO->merge(array('id'=>2))->filter(array('id'=>1, 'type'=>'admin');
```

由上述例子可知，添加之间关联符是跟`后面`的选择器表达式`保持一致`

`选择器`获取数据跟`DAO`方法一致，单表的`选择器`具有单表的所有查询，删改方法，而多表的`选择器`具有多表的所有查询，修改改方法

```
// UPDATE `DATABASE`.`TABLE` AS `user` SET `user`.`name` = 'test' WHERE `user`.`id` = 1
$result = $this->userDAO->filter(array('id'=>1)->update(array('name'=>'test'));

// SELECT * FROM ... WHERE `project`.`type` = 'admin'
$result = $this->userDAO->join($this->projectDAO, array('projectId'=>'id'))
    ->filter(array(array(),array('type'=>'admin')))
    ->query();
```

无论是`filter`还是`merge`，在执行SQL语句前都`不会被执行`，不会增加sql负担，可以放心使用。

## 复杂选择

除了正常的匹配选择以外，`filter`，`merge`里还提供了其他复杂选择器。

如果数组中值为`数组`的话，会自动变为`in`条件语句

```
// WHERE `user`.`type` IN (1,2,3,'test')
$this->userDAO->filter(array('id'=>array(1,2,3,'test')));
```

其他还包括 `>`，`<`，`>=`，`<=`，`!=`，`<>`，`is`，`is not`
            ，同样，多表的情况下需要用`二维数组`去封装

```
// WHERE `user`.`id` >= 10 AND `user`.`time` >= 1461584562 AND `user`.`type` is not null
$filter = $this->userDAO->filter(array(
    '>='=>array('id'=>10, 'time'=>1461584562),
    'is not'=>array('type'=>NULL),
));
```

另外，`like语句`也是支持的，可匹配正则符的开始结尾符，具体写法如下：

```
// WHERE `user`.`name` LIKE '%test%' OR `user`.`type` LIKE 'admin%' OR `user`.`type` LIKE '%admin'
$filter = $this->userDAO->merge(array(
    '__like__'=>array('name'=>test, 'type'=>'^admin', 'type'=>'admin$'),
));
```

`not in`语法暂时并未支持，可以暂时使用多个`!=`或者`<>`替代

同时`filter/merge`也可以被迭代调用，以应对不确定筛选条件的复杂查询

```
// 某一个返回筛选数据的Action
$DAO = $this->userDAO;
if ($status=$this->getParam('status')){
    $DAO = $DAO->filter(array('status'=>$status));
}
if ($startTime=$this->getParam('start', 0)){
    $DAO = $DAO->filter(array('>='=>array('start'=>$startTime)));
}
if ($endTime=$this->getParam('end', time())){
    $DAO = $DAO->filter(array('<'=>array('end'=>$endTime)));
}
// 获取复合条件数量
$count = $DAO->count();
// 获取复合条件前10条数据
$data = $DAO->limit(10)->query();
```

## 其他条件

在`DAO`或者`选择器`里都可以调用条件方法，方法可传递式调用，相同方法内的条件会自动合并

其中包括`group`，`addition`，`order`，`limit`，`having`

```
// SELECT avg(`user`.`cash`) AS 'a_c' FROM `TABLE` `user` WHERE ...
                GROUP BY `user`.`id`,`user`.`type` HAVING `a_c` >= 1000 ORDER BY `a_c` DESC, `id` ASC LIMIT 0,10;
$this->userDAO //->filter(...)
    ->addition(array('avg'=>array('cash'=>'a_c'))
    ->group(array('id', 'type'))
    ->having(array('>='=>array('a_c', 1000)))
    ->order(array('a_c'=>'DESC', 'id'=>'ASC'))
    // limit 第一个参数为取的条数，第二个参数为起始位置（默认为0）
    ->limit(10)
    ->query();
```

每次添加条件后都是独立的，`不会影响`原DAO 或者 选择器，可以放心的使用

```
// 这个对象不会因添加条件而变化
$filter = $this->userDAO->filter(array('id'=>array(1,2,3,'test')));
// 2
$count = $filter->limit(2)->count()
// 4
$count = $filter->count()
// 100 (user表总行数)
$count = $this->userDAO->count()
```

## SQL模版

框架中提供了上述`选择器`，`条件语句`，`联表`等，基本覆盖了所有sql语法，但可能还有部分生僻的用法无法被实现，
        于是这里提供了一种SQL模版的使用方式，支持用户自定义SQL语句，但`并不推荐用户使用`，如果一定要使用的话，请务必自己做好`防SQL注入`

这里提供了两种方式，`select`（查询，返回数据），以及`command`（执行，返回bool）

方法会自动替换`:where`和`:table`字段

```
// select * from `DATABASE`.`TABLE` WHERE ...
$result = $this->userDAO->select('select * from :table WHERE ...;');

// update `DATABASE`.`TABLE` `user` set name = 'test' WHERE `user`.`id` = 10 AND type = 2
$result = $this->userDAO->filter(array('id'=>10))
    ->command('update :table set name = 'test' WHERE :where AND type = 2;')
```

另外还可以添加一些自定义变量，这些变量会自动进行`sql转义`，防止`sql注入`

其中键值的替换符为`;`，例如`;key`，值的替换符为`:`，例如`:value`

```
// select `name` from `DATABASE`.`TABLE` WHERE `name`=2
$result = $this->userDAO->select('select ;key from :table WHERE ;key=:value;', array('key'=>'name', 'value'=>2));
```

同时替换内容也可以是数组，系统会自动替换为以`,`连接的字符串

```
// select `id`,`name` from `DATABASE`.`TABLE` WHERE `name` in (1,2,3,'test')
$result = $this->userDAO->select('select ;fields from :table WHERE ;key in :value;',
    array('key'=>'name', 'value'=>array(1,2,3,'test'), 'fields'=>array('id', 'name')));
```

以上替换方式都会进行`SQL转义`，建议用户使用模版替换，而不要自己将变量放入SQL语句中，防止`SQL注入`

## 事务处理

框架为DAO提供了一套简单的事务处理机制，默认是关闭的，可以通过`TXDatebase::start()`方法开启

`注意：`请确保连接的数据表是`innodb`的存储引擎，否者事务并不会生效。

在`TXDatebase::start()`之后可以通过`TXDatebase::commit()`来进行完整事务的提交保存，但并不会影响`start`之前的操作

同理，可以通过`TXDatebase::rollback()`进行整个事务的回滚，回滚所有当前未提交的事务

当程序调用`TXDatebase::end()`方法后事务会全部终止，未提交的事务也会自动回滚，另外，程序析构时，也会自动回滚未提交的事务

```
// 在事务开始前的操作都会默认提交，num:0
$this->testDAO->filter(['id'=>1])->update(['num'=>0]);
// 开始事务
TXDatabase::start();
// set num = num+2
$this->testDAO->filter(['id'=>1])->addCount(['num'=>1]);
$this->testDAO->filter(['id'=>1])->addCount(['num'=>1]);
// 回滚事务
TXDatabase::rollback();
// 当前num还是0
$num = $this->testDAO->filter(['id'=>1])->find()['num'];
// set num = num+2
$this->testDAO->filter(['id'=>1])->addCount(['num'=>1]);
$this->testDAO->filter(['id'=>1])->addCount(['num'=>1]);
// 提交事务
TXDatabase::commit();
// num = 2
$num = $this->testDAO->filter(['id'=>1])->find()['num'];
// 关闭事务
TXDatabase::end();
```

另外，事务的开启并不会影响`select`操作，只对增加，删除，修改操作有影响

## 数据缓存

框架这边针对`pk键值索引`数据可以通过继承`baseDAO`进行缓存操作，默认为`关闭`，可在DAO中定义`$_pkCache = true`来开启

然后需要在DAO中制定表键值，复合索引需要传`数组`，例如：`['id', 'type']`

因为系统缓存默认走`redis`，所以开启缓存的话，需要在`/app/config/dns_xxx.php`中配置环境相应的redis配置

```
// testDAO
class testDAO extends baseDAO
{
    protected $dbConfig = ['database', 'slaveDb'];
    protected $table = 'Biny_Test';
    // 表pk字段 复合pk为数组 ['id', 'type']
    protected $_pk = 'id';
    // 开启pk缓存
    protected $_pkCache = true;
}
```

`baseDAO`中提供了`getByPk`，`updateByPk`，`deleteByPk`，`addCountByPk`方法，
            当`$_pkCache`参数为`true`时，数据会走缓存，加快数据读取速度。

`getByPk` 读取键值数据，返回一维数组数据

```
//参数为pk值 返回 ['id'=>10, 'name'=>'test', 'time'=>1461845038]
$data = $this->testDAO->getByPk(10);

//复合pk需要传数组
$data = $this->userDAO->getByPk(array(10, 'test'));
```

`updateByPk` 更新单条数据

```
//参数为pk值,update数组，返回true/false
$result = $this->testDAO->updateByPk(10, array('name'=>'test'));
```

`deleteByPk` 删除单条数据

```
//参数为pk值，返回true/false
$result = $this->testDAO->deleteByPk(10);
```

`addCountByPk` 添加字段次数，效果等同`addCount()`方法：`set times = times + 3`

```
//参数为pk值，添加字段次数，返回true/false
$result = $this->testDAO->addCountByPk(10, array('times'=>3));
```

`注意：`开启`$_pkCache`的DAO不允许再使用`update`和`delete`方法，这样会导致缓存与数据不同步的现象。

如果该表频繁删改数据，建议关闭`$_pkCache`字段，或者在删改数据后调用`clearCache()`方法来清除缓存内容，从而与数据库内容保持同步。

## 语句调试

SQL调试方法已经集成在框架事件中，只需要在需要调试语句的方法前调用`TXEvent::on(onSql)`就可以在`页面控制台`中输出sql语句了

```
// one方法绑定一次事件，输出一次后自动释放
TXEvent::one(onSql);
$datas = $this->testDAO->query();

// on方法绑定事件，直到off释放前都会有效
TXEvent::on(onSql);
$datas = $this->testDAO->query();
$datas = $this->testDAO->query();
$datas = $this->testDAO->query();
TXEvent::off(onSql);
```

该SQL事件功能还可自行绑定方法，具体用法会在后面`事件`介绍中详细展开

# 页面渲染

请在`php.ini`配置中打开`short_open_tag`，使用简写模版，提高开发效率

页面view层目录在`/app/template/`下面，可以在`Action`层中通过`$this->display()`方法返回

一般`Action`类都会继承`baseAction`类，在`baseAction`中可以将一些页面通用参数一起下发，减少开发，维护成本

## 渲染参数

`display`方法有三个参数，第一个为指定`template`文件，第二个为页面参数数组，第三个为系统类数据(`没有可不传`)。

```
// 返回/app/template/main/test.tpl.php 
return $this->display('main/test', array('test'=>1), array('path'=>'/test.png'));

/* /app/template/main/test.tpl.php
返回:
<div class="container">
    <span> 1  </span>
    <img src="/test.png"/>
</div> */
<div class="container">
    <span> <?=$PRM['test']?>  </span>
    <img src="<?=$path?>"/>
</div>
```

第二个参数的数据都会放到`$PRM`这个页面对象中。第三个参数则会直接被渲染，适合`静态资源地址`或者`类数据`

## 自定义TKD

页面TKD一般都默认在`common.tpl.php`定义好，如果页面单独需要修改对应的`title，keywords，description`的话，
            也可以在`TXResponse`生成后对其赋值

```
$view = $this->display('main/test', $params);
$view->title = 'Biny';
$view->keywords = 'biny,php,框架';
$view->description = '一款轻量级好用的框架';
return $view;
```

## 反XSS注入

使用框架`display`方法，自动会进行参数`html实例化`，防止XSS注入。

`$PRM`获取参数时有两种写法，普通的数组内容获取，会自动进行`转义`

```
// 显示 <div> 源码为 &lt;div&gt;
<span> <?=$PRM['test']?>  </span>
```

另外可以用私用参数的方式获取，则不会被转义，适用于需要显示完整页面结构的需求（`普通页面不推荐使用，隐患很大`）

```
// 显示 <div> 源码为 <div> 
<span> <?=$PRM->test?>  </span>
// 效果同上
<span> <?=$PRM->get('test')?>  </span>
```

在多层数据结构中，也一样可以递归使用

```
// 显示 <div> 源码为 &lt;div&gt;
<span> <?=$PRM['array']['key1']?>  </span>
<span> <?=$PRM['array']->get(0)?>  </span>
```

而多层结构数组参数会在使用时`自动转义`，不使用时则不会进行转义，避免资源浪费，影响渲染效率。

`注意：`第三个参数必定会进行参数`html实例化`，如果有参数不需要转义的，请放到第二个参数对象中使用。

## 参数方法

渲染参数除了渲染外，还提供了一些原有`array`的方法，例如：

`in_array` 判断字段是否在数组中

```
// 等同于 in_array('value', $array)
<? if ($PRM['array']->in_array('value') {
    // do something
}?>
```

`array_key_exists` 判断key字段是否在数组中

```
// 等同于 array_key_exists('key1', $array)
<? if ($PRM['array']->array_key_exists('key1') {
    // do something
}?>
```

其他方法以此类推，使用方式是相同的，其他还有`json_encode`

```
// 赋值给js参数 var jsParam = {'test':1, "demo": {"key": "test"}};
var jsParam = <?=$PRM['array']->json_encode()?>;
```

判断数组参数是否为空，可以直接调用`$PRM['array']()`方法判断

```
// 等同于 if ($array)
<? if ($PRM['array']() ) {
    // do something
}?>
```

其他参数方法可以自行在`/lib/data/TXArray.php`中进行定义

比如：定义一个`len`方法，返回数组长度

```
/lib/data/TXArray.php
public function len()
{
    return count($this->storage);
}
```

然后就可以在`tpl`中开始使用了

```
// 赋值给js参数 var jsParam = 2;
var jsParam = <?=$PRM['array']->len()?>;
```

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

```
/**
* 主页Action
* @property testService $testService
*/  
class testAction extends baseAction
{
    //构造函数
    public function __construct()
    {
        // 构造函数记得要调用一下父级的构造函数
        parent::__construct();
        // 要触发beforeAction事件，必须在init被调用前定义
        TXEvent::on(beforeAction, array($this, 'test_event'));
    }

    //默认路由index
    public function action_index()
    {
        // 绑定testService里的my_event1方法 和 my_event2方法 到 myEvent事件中，两个方法都会被执行，按绑定先后顺序执行
        $fd1 = TXEvent::on('myEvent', array($this->testService, 'my_event1'));
        $fd2 = TXEvent::on('myEvent', array($this->testService, 'my_event2'));

        // do something ..... 

        // 解绑myEvent事件的 my_event1方法
        TXEvent::off('myEvent', $fd1);

        // 解绑myEvent事件，所有绑定在该事件上的方法都不会再被执行
        TXEvent::off('myEvent');

        return $this->error('测试一下');
    }

    // 自定义的事件类
    public function test_event($event)
    {
        // addLog为写日志的方法
        TXLogger::addLog('触发beforeAction事件');
    }
}
```

另一种绑定则为一次绑定事件`TXEvent::one()`，调用参数相同，返回`$fd`操作符，当该事件被触发一次后会自动解绑

```
$fd = TXEvent::one('myEvent', array($this, 'my_event'));
```

当然如果想要绑定多次但非长期绑定时，系统也提供了`bind`方法，参数用法类似。

```
// 第一个参数绑定方法，第二个为事件名，第三个为绑定次数，触发次数满后自动释放
$fd = TXEvent::bind(array($this, 'my_event'), 'myEvent', $times);
```

## 触发事件

用户可以自定义事件，同时也可以选择性的触发，可以直接使用`TXEvent::trigger($event, $params)`方法

参数有两个，第一个为触发的事件名，第二个为触发传递的参数，会传递到触发方法中执行

```
// 触发myEvent事件
TXEvent::trigger('myEvent', array(get_class($this), 'test'))

// 定义事件时绑定的方法
public function my_event($event, $params)
{
    // array('testService', 'test')
    var_dump($params);
}
```

# 表单验证

框架提供了一套完整的表单验证解决方案，适用于绝大多数场景。

表单验证支持所有类型的验证以及自定义方法

简单示例：

```
/**
 * @property testService $testService
 * 自定义一个表单验证类型类 继承TXForm
 */
class testForm extends TXForm
{
    // 定义表单参数，类型及默认值（可不写，默认null）
    protected $_rules = [
        // id必须为整型, 默认10
        'id'=>[self::typeInt, 10],
        // name必须非空（包括null, 空字符串）
        'name'=>[self::typeNonEmpty],
        // 自定义验证方法(valid_testCmp)
        'status'=>['testCmp']
    ];

    // 自定义验证方法
    public function valid_testCmp()
    {
        // 和Action一样可以调用Service和DAO作为私有方法
        if ($this->testService->checkStatus($this->status)){
            // 验证通过
            return $this->correct();
        } else {
            // 验证失败，参数可以通过getError方法获取
            return $this->error('非法类型');
        }
    }
}
```

定义完验证类，然后就可以在Action中使用了，可以通过`getForm`方法加载表单

```
// 加载testForm
$form = $this->getForm('test');
// 验证表单字段，true/false
if (!$form->check()){
    // 获取错误信息
    $error = $form->getError();
    return $this->error('参数错误');
}
// 获取对应字段
$status = $form->status;
// 获取全部字段 返回数组类型 ['id'=>1, 'name'=>'billge', 'status'=>2]
$datas = $form->values();
```

`注意：`在`$_rules`中未定义的字段，无法在`$form`中被获取到，就算不需要验证，也最好定义一下

在很多情况下，表单参数并不是都完全相同的，系统支持`Form复用`，即可以在通用的Form类中自定义一些内容

比如，还是上述例子的testForm，有个类似的表单，但是多了一个字段type，而且对于status的验证方式也需要变化

可以在testForm中添加一个方法

```
// 在testForm中添加
public function addType()
{
    // 添加type字段， 默认'default', 规则为非空
    $this->_rules['type'] = [self::typeNonEmpty,'default'];
    // 修改status的判断条件，改为valid_typeCmp()方法验证，记得要写这个方法哦
    $this->_rules['status'][0] = 'typeCmp';
}
```

然后在Action中加载表单也需要添加`'addType'`作为参数，其他使用方法一致

```
$form = $this->getForm('test', 'addType');
```

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

# 调试

框架中有两种调试方式，一种是在页面控制台中输出的调试，方便用户对应网页调试。

另一种则是和其他框架一样，在日志中调试

## 控制台调试

Biny的一大特色既是这控制台调试方式，用户可以调试自己想要的数据，同时也不会对当前的页面结构产生影响。

调试的开关在`/web/index.php`里

```
// console调试开关，关闭后控制台不会输出内容
defined('SYS_CONSOLE') or define('SYS_CONSOLE', true);
```

控制台调试的方式，同步异步都可以调试，但异步的调试是需要引用`/static/js/main.js`文件，这样异步ajax的请求也会把调试信息输出在控制台里了。

调试方式很简单，全局可以调用`TXLogger::info($message, $key)`，另外还有warn，error，log等

第一个参数为想要调试的内容，同时也支持数组，Object类的输出。第二个参数为调试key，不传默认为`phpLogs`

`TXLogger::info()`消息 输出

`TXLogger::warn()`警告 输出

`TXLogger::error()`异常 输出

`TXLogger::log()`日志 输出

下面是一个简单例子，和控制台的输出结果。结果会因为浏览器不一样而样式不同，效果上是一样的。

```
// 以下代码全局都可以使用
TXLogger::log(array('cc'=>'dd'));
TXLogger::error('this is a error');
TXLogger::info(array(1,2,3,4,5));
TXLogger::warn("ss", "warnKey");
```

![](http://km.oa.com/files/photos/captures/201505/1432003538_35_w219_h87.png)

另外`TXLogger`调试类中还支持time，memory的输出，可以使用其对代码性能做优化。

```
// 开始结尾处加上时间 和 memory 就可以获取中间程序消耗的性能了
TXLogger::time('start-time');
TXLogger::memory('start-memory');
TXLogger::log('do something');
TXLogger::time('end-time');
TXLogger::memory('end-memory');
```

![](http://shp.qpic.cn/gqop/20000/LabImage_2ee327c680046dc1d14d7dce5c7bcb45.png/0)

## 日志调试

平台的日志目录在`/logs/`，请确保该目录有`写权限`

异常记录会生成在`error_{日期}.log`文件中，如：`error_2016-05-05.log`

调试记录会生成在`log_{日期}.log`文件中，如：`log_2016-05-05.log`

程序中可以通过调用`TXLogger::addLog($log, INFO)`方法添加日志，`TXLogger::addError($log, ERROR)`方法添加异常

`$log`参数支持传数组，会自动排列打印

`$LEVEL`可使用常量（`INFO`、`DEBUG`、`NOTICE`、`WARNING`、`ERROR`）不填即默认级别

系统程序错误也都会在error日志中显示，如页面出现500时可在错误日志中查看定位

# 脚本执行

Biny框架除了提供HTTP的请求处理以外，同时还提供了一套完整的脚本执行逻辑

执行入口为根目录下的`shell.php`文件，用户可以通过命令行执行`php shell.php {router} {param}`方式调用

其中`router`为脚本路由，`param`为执行参数，可缺省或多个参数

```
// shell.php
//默认时区配置
date_default_timezone_set('Asia/Shanghai');
// 开启脚本执行（shell.php固定为true）
defined('RUN_SHELL') or define('RUN_SHELL', true);
// dev pre pub 当前环境
defined('SYS_ENV') or define('SYS_ENV', 'dev');
```

## 脚本路由

路由跟http请求模式基本保持一致，分为`{module}/{method}`的形式，其中`{method}`可以缺省，默认为`index`

例如：`index/test`就会执行`indexShell`中的`action_test`方法，而`demo`则会执行`demoShell`中的`action_index`方法

如果router缺省的话，默认会读取`/config/config.php`中的router内容作为默认路由

```
// /config/config.php
return array(
    'router' => array(
        // http 默认路由
        'base_action' => 'demo',
        // shell 默认路由
        'base_shell' => 'index'
    )
)
// /app/shell/indexShell.php
class testShell extends TXShell
{
    // 和http一样都会先执行init方法
    public function init()
    {
        //return 0 或者 不return 则程序继续执行。如果返回其他内容则输出内容后程序终止。
        return 0;
    }

    //默认路由index
    public function action_index()
    {
        //返回异常，会记录日志并输出在终端
        return $this->error('执行错误');
    }
}
```

## 脚本参数

脚本执行可传复数的参数，同http请求可在方法中直接捕获，顺序跟参数顺序保持一致，可缺省

另外，可以用`getParam`方法获取对应位置的参数，用法与http模式保持一致

例如：终端执行`php shell.php test/demo 1 2 aaa`，结果如下：

```
// php shell.php test/demo 1 2 aaa
class testShell extends TXShell
{
    test/demo => testShell/action_demo
    public function action_demo($prm1, $prm2, $prm3, $prm4='default')
    {
        //1, 2, aaa, default
        echo "$prm1, $prm2, $prm3, $prm4";
        //1
        echo $this->getParam(0);
        //2
        echo $this->getParam(1);
        //aaa
        echo $this->getParam(2);
        //default
        echo $this->getParam(3, 'default');
    }
}
```

## 脚本日志

脚本执行不再具有HTTP模式的其他功能，例如`表单验证`，`页面渲染`，`浏览器控制台调试`。所以在`TXLogger`调试类中，`info/error/debug/warning`这几个方法不在有效了

需要调试的可以继续调用`TXLogger::addLog`和`TXLogger::addError`方法来进行写日志的操作

日志目录则保存在`/logs/shell/`目录下，请确保该目录有`写权限`。格式与http模式保持一致。

`注意:`当程序返回`$this->error($msg)`的时候，系统会默认调用`TXLogger::addError($msg)`，请勿重复调用。

# 其他

系统有很多单例都可以直接通过`TXApp::$base`直接获取

`TXApp::$base->person` 为当前用户，可在`/app/model/Person.php`中定义

`TXApp::$base->request` 为当前请求，可获取当前地址，客户端ip等

`TXApp::$base->session` 为系统session，可直接获取和复制，设置过期时间

`TXApp::$base->memcache` 为系统memcache，可直接获取和复制，设置过期时间

`TXApp::$base->redis` 为系统redis，可直接获取和复制，设置过期时间

## Request

在进入`Controller`层后，`Request`就可以被调用了，以下是几个常用操作

```
// 已请求 /test/demo/ 为例

// 获取Action名 返回test
TXApp::$base->request->getModule();

// 获取Method名 返回action_demo
TXApp::$base->request->getMethod();

// 获取纯Method名 返回demo
TXApp::$base->request->getMethod(true);

// 是否异步请求 返回false
TXApp::$base->request->isAjax();

// 返回当前路径  /test/demo/
TXApp::$base->request->getBaseUrl();

// 返回完整路径  http://biny.oa.com/test/demo/
TXApp::$base->request->getBaseUrl(true);

// 获取来源网址 （上一个页面地址）
TXApp::$base->request->getReferrer();

// 获取浏览器UA
TXApp::$base->request->getUserAgent();

// 获取用户IP
TXApp::$base->request->getUserIP();
```

## Session

session的设置和获取都比较简单，在未调用session时，对象不会被创建，避免性能损耗。

```
// 只需要赋值就可以实现session的设置了
TXApp::$base->session->testkey = 'test';
// 获取则是直接去元素，不存在则返回null
$testKey = TXApp::$base->session->testkey;
```

同时也可以通过方法`close()`来关闭session，避免session死锁的问题

```
// close之后再获取数据时会重新开启session
TXApp::$base->session->close();
```

而`clear()`方法则会清空当前session中的内容

```
// clear之后再获取则为null
TXApp::$base->session->clear();
```

同时session也是支持`isset`判断的

```
// isset 相当于先get 后isset 返回 true/false
$bool = isset(TXApp::$base->session->testKey);
```

## Cookie

cookie的获取和设置都是在`TXApp::$base->request`中完成的，分别提供了`getCookie`和`setCookie`方法

`getCookie`参数为需要的cookie键值，如果不传，则返回全部cookie，以数组结构返回

```
$param = TXApp::$base->request->getCookie('param');
```

`setCookie`参数有4个，分别为键值，值，过期时间(单位秒)，cookie所属路径，过期时间不传默认1天，路径默认`'/'`

```
TXApp::$base->request->setCookie('param', 'test', 86400, '/');
```