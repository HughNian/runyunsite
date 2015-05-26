<?php
defined('APP_PATH') or die('No direct access allowed.');

/**
 * 控制器基类
 * 
 * @author niansong
 * @version  1.0
 * @package  API
 * 
 */
class BaseController
{
	protected $ret_val = array();
	
	/**
	 * @todo:输出结果集
	 * 
	 */
	protected function result()
	{
		extract($this->ret_val, EXTR_OVERWRITE);
		
		$jsoncallback = FuncHelper::json();
		
		$y['code']  = 200;
		$y['msg']   = 'success';
		$y['data']  = $data;
		
		$tt = $jsoncallback."(".(json_encode($y)).")";
		
		exit($tt);
	}
	
	/**
	 * @todo:输出错误信息
	 * 
	 */
	protected function error($code, $msg)
	{	
		$jsoncallback = FuncHelper::json();
		
		$y['code']  = $code;
		$y['msg']   = $msg;
		$y['data']  = array();
		
		$tt = $jsoncallback."(".(json_encode($y)).")";
		exit($tt);
	}
	
	/**
	 * @todo:检测接口必填参数
	 * 
	 * @param {string} 参数名称 xxx,xxx,xxx 形式
	 * 
	 */
	protected function check($params)
	{
		if(!$params) return;
		
		global $G; //GET参数全局变量
		
		$param_arr = explode(',', $params);
		foreach($param_arr as $param){
			if(!isset($G["$param"]) || !$G["$param"]){
				$this->error(101,"缺少参数$param");
			}
		}
	}
}