# 文件数据库
## 一、简介
	一些项目在正式上线后，遇到项目数据结构根据实际需求发生改变的情况，出现数据库难以修改等情况。因此，开发文件数据库，用以保存非必要
	数据，并实现简单的读取、修改、删除、添加等操作。

## 二、结构
	![](http://i.imgur.com/qsURbRb.png)

## 三、用法
	引入类文件数据库类
	require_once 'File.php'
	
	数据库
	$db = './test';

	实例化	
	$file_db = new File($db);
	
	查找
	$file_db->getAll('admin_user')

	增加
	$file_db->insert(array('id'=>'5','username'=>'xuby','password'=>'admin'),'admin_user');

	删除
	$file_db->delete(array('id'=>'5'),'admin_user');

	修改
	$file_db->update(array('id'=>'5') , array('password' => md5('admin')) , 'admin_user');

	具体参数见File.php类

	另，查找信息默认返回格式为对象，如若需要转为数组，则调用obj2array方法：
	$file_db->obj2array($object)
