<?php
/**
 * index page for api
 * 
 * PHP version 5.2
 * 
 * @category  API
 * @package   API
 * @author    niansong
 * 
 */
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('PRC');
setlocale(LC_ALL, 'en_US.utf-8');

//常量定义
define('ENVIRONMENT', 'development');
defined('APP_PATH') or define('APP_PATH', 'api' . DIRECTORY_SEPARATOR);
defined('ROOT_PATH') or define('ROOT_PATH', $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . APP_PATH);
defined('PUBLIC') or define('PUBLIC', 'public' . DIRECTORY_SEPARATOR);
defined('STATICS') or define('STATICS', 'statics' . DIRECTORY_SEPARATOR);
defined('APP_DEBUG') or define('APP_DEBUG', true); // 是否调试模式
defined('EXT') or define('EXT', '.php');
define('IS_CLI',PHP_SAPI=='cli'? 1 : 0);

if (defined('ENVIRONMENT'))
{
	switch (ENVIRONMENT)
	{
		case 'development':
			error_reporting(E_ALL);
		break;
	
		case 'testing':
		case 'production':
			error_reporting(0);
		break;
		
		default:
			exit('The application environment is not set correctly.');
	}
}

require_once APP_PATH . "boot". EXT;
?>