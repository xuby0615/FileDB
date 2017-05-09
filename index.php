<?php
//  文件数据库类
require_once 'File.php';

//  数据库
$db = './test';

//  实例化类
$file_db = new File($db);

$file_db->delete(array('id'=>'5'),'admin_user');
// $file_db->insert(array('id'=>'5','username'=>'xuby','password'=>'admin'),'admin_user');
// $file_db->update(array('id'=>'5') , array('password' => md5('admin')) , 'admin_user');
// var_dump($file_db->getAll('admin_user'));
?>