<?php
defined('APP_PATH') or die('No direct access allowed.');

/**
 * 模板接口
 *
 * @author   niansong
 * @version  1.0
 * @package  API
 *
 */
class TemplateController extends BaseController
{
    /**
     * @todo 模板列表数据
     * 
     */
    public function Tpl($G = array())
    {
        $page = isset($G['page']) ? $G['page'] : 1;
        
        $data = array();
        $archivesModel  = M('Archives');
        
        $data['tpl'] = $archivesModel->getTplList($page);
        
        //返回数据
        $this->ret_val['data'] = $data;
        $this->result();
    }
    
    /**
     * @todo 模板详情
     * 
     */
    public function TplDetail($G = array())
    {
        //检测参数
        $this->check('aid,typeid');
        
        $aid = $G['aid'];
        $typeid = $G['typeid'];
        
        $archivesModel  = M('Archives');
        $detail = $archivesModel->getTplDetail($aid, $typeid);
        
        //返回数据
        $this->ret_val['data'] = $detail;
        $this->result();
    }
}