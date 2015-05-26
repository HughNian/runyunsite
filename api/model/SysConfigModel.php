<?php
defined('APP_PATH') or die('No direct access allowed.');

/**
 * 站点配置model
 *
 * @author niansong
 *
 */
class SysConfigModel extends BaseModel
{
    //表名
    public $table = 'yun_sysconfig';
    
    /**
     * @todo 获取配置信息
     * 
     */
    public function getInfo()
    {
        return $this->select()->find_all();
    }
}