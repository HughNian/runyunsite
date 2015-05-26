<?php
defined('APP_PATH') or die('No direct access allowed.');

/**
 * 动态接口
 * 
 * @author   niansong
 * @version  1.0
 * @package  API
 * 
 */
class DynamicController extends BaseController
{
    /**
     * @todo 动态列表数据
     * 
     */
    public function DynamicList($G = array())
    {
        $typeid = isset($G['typeid']) ? $G['typeid'] : NULL;
        
        $arctypeModel   = M('Arctype');
        $archivesModel  = M('Archives');
        
        $data = array();
        $reid = C('default.dynamicid');
        $types = $arctypeModel->select('id,typename')->where("reid = $reid")->find_all();
        $data['types'] = $types;
        
        if(is_null($typeid)){
            //默认第一个分类的动态列表数据
            $firsttype = current($types);
            $typeid = $firsttype['id'];
        }
        
        $type_exists = $archivesModel->count("typeid = $typeid");
        if(!$type_exists){
            $this->error(100, "分类不存在");
        }
        
        $dynamics = array();
        foreach($archivesModel->select()->where("typeid = $typeid")->order_by('pubdate','DESC')->find_all() as $val){
            $year = date('Y', $val['pubdate']);
            $daytime = date('m.d', $val['pubdate']);
            $val['year'] = $year;
            $val['daytime'] = $daytime;
            $dynamics[$year]['year'] = $year;
            $dynamics[$year]['data'][] = $val;
        }
        
        $data['dynamics'] = array_values($dynamics);
        
        //返回数据
        $this->ret_val['data'] = $data;
        $this->result();
    }
    
    /**
     * @todo 动态详情
     * 
     */
    public function DynamicDetail($G = array())
    {
        $this->check('aid,typeid');
        
        $aid    = $G['aid'];
        $typeid = $G['typeid'];
        
        $archivesModel  = M('Archives');
        
        $is_exists = $archivesModel->count("id = $aid AND typeid = $typeid");
        if(!$is_exists){
            $this->error(100, "文章不存在");
        }
        
        $detail = $archivesModel->getDynamicDetail($aid, $typeid);
        
        //返回数据
        $this->ret_val['data'] = $detail;
        $this->result();
    }
}