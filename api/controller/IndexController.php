<?php
defined('APP_PATH') or die('No direct access allowed.');

/**
 * 首页接口
 * 
 * @author   niansong
 * @version  1.0
 * @package  API
 * 
 */
class IndexController extends BaseController
{
    /**
     * @todo 右侧主页面内容
     * 
     *
     */
    public function Right($G = array())
    {
        $data = array();
        $arcTypeModel   = M('Arctype');
        $archivesModel  = M('Archives');
        $logoWallModel  = M('LogoWall');
        
        //轮播图片数据
        $data['lunbo'] = $archivesModel->getLubo();
        //首页模板库数据
        $data['tpl']   = $archivesModel->getIndexTpl();
        //润云动态数据
        $data['dynamic'] = $archivesModel->getIndexDynamic();
        //合作加盟数据
        $data['portfolio'] = $arcTypeModel->getIndexPortfolio();
        //获取logo墙数据
        $data['logowall'] = $logoWallModel->getLogoWall();
        //关于润云数据
        $data['about'] = $arcTypeModel->getIndexAbout();
        //菜单名称
        $data['menu'] = $arcTypeModel->getFirstType();
        
        //返回数据
        $this->ret_val['data'] = $data;
        $this->result();
    }
    
    /**
     * @todo 左侧页面内容
     * 
     */
    public function Left($G = array())
    {
        $data = array();
        $arcTypeModel = M('Arctype');
        $sysConfigModel = M('SysConfig');
        
        //菜单数据
        $data['menu'] = $arcTypeModel->getFirstType();
        array_unshift($data['menu'], array("id"=>-1, "typename"=>"首页"));
        array_push($data['menu'], array("id"=>-1, "typename"=>"联系我们"));
        //站点设置数据
        $data['siteinfo'] = $sysConfigModel->getinfo();
        
        //返回数据
        $this->ret_val['data'] = $data;
        $this->result();
    }
    
    /**
     * @todo 获取友情链接
     * 
     */
    public function Links($G = array())
    {
        $flinkModel = M('Flink');
        
        //友情链接数据
        $data = $flinkModel->getLinks(2);
        
        //返回数据
        $this->ret_val['data'] = $data;
        $this->result();
    }
    
    /**
     * @todo 提交需求信息
     * 
     */
    public function Demand($G = array())
    {
        //参数检测
        //$this->check('name,telephone,email');
        
        $feedbackModel = M('FeedBack');
        
        $name      = isset($G['name']) ? $G['name'] : '';
        $telephone = isset($G['telephone']) ? $G['telephone'] : '';
        $email     = isset($G['email']) ? $G['email'] : '';
        $company   = isset($G['company']) ? $G['company'] : '';
        $msg       = isset($G['msg']) ? $G['msg'] : '';
        
        if(!$name){
            $this->error(100, '请输入姓名');
        }

        if(!$telephone){
            $this->error(101, '请输入手机号');
        }
        
        if(!$email){
            $this->error(102, '请输入邮箱');
        }
        
        if(!UtilityHelper::checkPhoneNum($telephone)){
            $this->error(103, '手机号码格式不正确');
        }
        
        if(!UtilityHelper::checkEmail($email)){
            $this->error(104, '邮箱格式不正确');
        }
        
        //安全过滤
        $name    = UtilityHelper::h($name);
        $company = UtilityHelper::h($company);
        $msg     = UtilityHelper::h($msg);
        
        //插入数据
        $data = array(
            'username'  => $name,
            'telephone' => $telephone,
            'email'     => $email,
            'company'   => $company,
            'ip'        => UtilityHelper::getip(),
            'msg'       => $msg
        );
        $feedbackModel->data($data)->insert();
        
        //返回数据
        $this->ret_val['data'] = array();
        $this->result();
    }
}