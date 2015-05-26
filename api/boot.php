<?php
/**
 * 程序启动分发
 * 
 */
defined('APP_PATH') or die('No direct script access.');

//引用配置文件和系统函数库
require_once "config/config.php";
require_once "common/function.php";

//链接数据库
mysql_connect(C("db.connection.hostname"),C("db.connection.username"),C("db.connection.password")) or die('数据库连接失败');
mysql_select_db(C("db.connection.database"));
mysql_query('set names utf8');

//自动加载
function loader($class)
{
	$dir = "";
	if(strpos($class, "Controller")) $dir = 'controller' . DIRECTORY_SEPARATOR;
	if(strpos($class, "Model")) $dir = 'model' . DIRECTORY_SEPARATOR;
	if(strpos($class, "Helper")) $dir = 'helper' . DIRECTORY_SEPARATOR;

	$file = $class . EXT;
	if (file_exists(ROOT_PATH . $dir . $file)) {
		require_once $dir . $file;
	} else {
		throw new Exception("$file . class file not found");
		exit();
	}
}

//GET参数
$G = array();
parse_str(substr(strrchr($_SERVER['REQUEST_URI'], '?'), 1), $G);

//开始
function start()
{
    spl_autoload_register('loader');
    // 设定错误和异常处理
    register_shutdown_function('ExceptionHelper::fatalError');
    set_error_handler('ExceptionHelper::appError');
    set_exception_handler('ExceptionHelper::appException');
    
    $CON_ACT = isset($_GET) ? $_GET : false;
    
    if(!$CON_ACT)
        exit('missing params!');
    
    $CON = key($CON_ACT);
    $ACT = current($CON_ACT);
    
    global $G;
    //控制器
    $Controller = ucfirst($CON) . 'Controller';
    
    //执行程序
    $run = new $Controller();
    $run->$ACT($G);
}

//just do it
call_user_func("start");
