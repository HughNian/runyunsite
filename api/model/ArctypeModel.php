<?php
defined('APP_PATH') or die('No direct access allowed.');

/**
 * 文章栏目model
 *
 * @author niansong
 *
 */
class ArctypeModel extends BaseModel
{
    //表名
    public $table = 'yun_arctype';
    
    /**
     * @todo 获取一级分类
     * 
     */
    public function getFirstType()
    {
        return $this->select('id,typename')
                    ->where('topid = 0 AND ishidden = 0')
                    ->order_by('sortrank', 'ASC')
                    ->find_all();
    }
    
    /**
     * @todo 获取模板库分类
     * 
     */
    public function getTplType()
    {
        $reid = C('default.tmpid');
        return $this->select('id,typename')
                    ->where("reid = $reid")
                    ->order_by('sortrank', 'ASC')
                    ->find_all();
    }
    
    /**
     * @todo 获取合作加盟内容
     *
     */
    public function getIndexPortfolio()
    {
        $typeid = C('default.portfolioid');
        return $this->select()->where("id = $typeid")->find();
    }
    
    /**
     * @todo 获取关于我们内容
     *
     */
    public function getIndexAbout()
    {
        $typeid = C('default.aboutid');
        return $this->select()->where("id = $typeid")->find();
    }
}