2015-08-03 升级2.0版

1. router 使用域名模式(ajax为需要返回json) e.g: /biny/Index/?xxx  /biny/ajax/DataGet/?xxx
2. 模板内添加$this->isAjax() 判断是否异步请求
3. getParam 方法添加 字符名命名法 匹配参数类型
4. action 添加默认参数,自动赋值
5, service/dao 自动加载,无需再定义变量
6. 添加TXDAO 数据库层, 替代原有TXModel
7. 新建model orm数据层, 继承TXModel
8. BaseDAO  添加getByPK, updateByPK, deleteByPK方法(对键值操作, 有缓存)
9. DAO添加 join / leftJoin / rightJoin 创建关联表  可递归关联 (TXDoubleDAO)
10. DAO添加filter(筛选)/merge(合并)方法 (and/or) 可递归 (TXFilter)
11. DAO 添加 updateOrCreate / addOrCreate / addCount 方法
12. TXResponse 添加ignore过滤字段(不实体化转译 慎用)
13. TXConfig 设置别名以及自动获取转义

2015-08-17

1. DAO添加groupBy 请求
2. DAO添加分表->choose($id)
3. DAO \_\_ex\_\_(filter)弃用，直接用>,>=变量符替代

2015-09-06

1. TXAction、TXAjax分离 分别返回TXResponse、TXJSONResponse
2. 添加action子方法 /biny[/ajax]/Index/test => IndexAction::action_test()
3. TXResponse添加SEO自定义 title、keywords、description

2015-09-14

1. sql返回类封装（TXSqlData/TXObject） 反XSS注入封装
2. TXSqlData 添加lists/dict方法
3. 支持跨库连表（DAO dbConfig 动态设置）