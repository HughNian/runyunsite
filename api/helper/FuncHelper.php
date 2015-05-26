<?php
defined('APP_PATH') or die('No direct access allowed.');

/**
 * 公共方法帮助类
 * 
 * @author niansong
 * 
 */
class FuncHelper
{
	public static function json()
	{
		global $G;
		
		if(isset($G['jsoncallback']))
		{
			$jsoncallback = $G['jsoncallback'];
		}
		else
		{
			$jsoncallback = '';
		}
		return $jsoncallback;
	}
	
}