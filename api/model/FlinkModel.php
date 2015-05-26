<?php
defined('APP_PATH') or die('No direct access allowed.');

/**
 * 友情链接model
 *
 * @author niansong
 *
 */
class FlinkModel extends BaseModel
{
    //表名
    public $table = 'yun_flink';

    /**
     * @todo 获取友情链接
     * 
     * @param $type 链接类型 1,站内链接.2,首页链接
     * 
     */
    public function getLinks($type)
    {
        return $this->select()
                    ->where("ischeck = $type")
                    ->find_all();
    }   
}