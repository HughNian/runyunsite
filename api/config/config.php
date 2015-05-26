<?php
defined('APP_PATH') or die('No direct access allowed.');

//配置
return $config = array
(
	//数据库配置
	'db' => array(
		'type' => 'mysql',
		'connection' => array(
			'hostname' => '127.0.0.1',
			'database' => 'yuncms',
			'username' => 'root',
			'password' => '123456',
			'port'     => '3306',
			'persistent' => FALSE,
		),
	),
	//默认的一些配置
	'default' => array(
	    'limit'        => 10, //分页数
	    'tmpid'        => 2,  //模板库类型id
	    'dynamicid'    => 11, //润云动态类型父类id
	    'portfolioid'  => 10, //合作加盟类型id
	    'aboutid'      => 9,  //关于润云类型id
	),
);