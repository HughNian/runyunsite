<?php
defined('APP_PATH') or die('No direct access allowed.');

/**
 * logowall model
 *
 * @author niansong
 *
 */
class LogoWallModel extends BaseModel
{
    //表名
    public $table = 'yun_logowall';
    
    /**
     * @todo 获取logo墙
     * 
     */
    public function getLogoWall()
    {
        return $this->select()
                    ->order_by('dtime', 'DESC')
                    ->find_all();
    }
}