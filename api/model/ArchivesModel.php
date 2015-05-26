<?php
defined('APP_PATH') or die('No direct access allowed.');

/**
 * 文章model
 *
 * @author niansong
 *
 */
class ArchivesModel extends BaseModel
{
    //表名
    public $table = 'yun_archives';
    
    protected $disabled = 0;
    
    protected $disabledName = 'arcrank';
    
    /**
     * @todo 获取轮播图片
     * 
     */
    public function getLubo()
    {
        $archives =  $this->select('id,title,shorttitle,litpic,description,target')
                          ->where('FIND_IN_SET("f",`flag`) AND type <> 1')
                          ->order_by('pubdate', 'DESC')
                          ->limit(0,4)
                          ->find_all();
        
        $AddonArticleModel = M('AddonArticle');
        foreach($archives as $key => $val){
            $aid = $val['id'];
            $archives[$key]['redirecturl'] = $AddonArticleModel->select('redirecturl')->where("aid = $aid")->get()->redirecturl;
        }
        
        return $archives;
    }
    
    /**
     * @todo 获取首页模板库
     * 
     * @param int - $limit:limit的数量
     * 
     */
    public function getIndexTpl()
    {
        $limit = 8;
        $arcTypeModel = M('Arctype');
        
        $tplTypes = $arcTypeModel->getTplType();
        array_unshift($tplTypes, array('id'=>-1, 'typename'=>'全部'));
        
        foreach($tplTypes as $key => $val){
            $typeid = $val['id'];
            if($typeid == -1){
                $tmpid  = C('default.tmpid');
                $inids  = array();
                foreach($arcTypeModel->select('id')
                                     ->where("reid = $tmpid")
                                     ->find_all() as $val){
                    $inids[] = $val['id'];
                }
                $_inids = implode(',', $inids);
                $data = $this->select("$this->table.*, yun_arctype.typename")
                             ->left('yun_arctype', 'typeid', 'id')
                             ->where("$this->table.typeid IN ($_inids) AND $this->table.type <> 1 AND FIND_IN_SET('a',`flag`)")
                             ->order_by("$this->table.senddate", 'ASC')
                             ->limit(0, $limit)
                             ->find_all();
                foreach($data as $k => $v){
                    $data[$k]['typeid'] = 'all';
                }
                $tplTypes[$key]['tpl'] = $data;
            } else {
                $data = $this->select("$this->table.*, yun_arctype.typename")
                             ->left('yun_arctype', 'typeid', 'id')
                             ->where("$this->table.typeid = $typeid AND $this->table.type <> 1")
                             ->order_by("$this->table.senddate", 'ASC')
                             ->limit(0, $limit)
                             ->find_all();
                $tplTypes[$key]['tpl'] = $data;
            }
        }
        return $tplTypes;
    }
    
    /**
     * @todo 获取首页润云动态内容
     * 
     */
    public function getIndexDynamic()
    {
        $typeid = C('default.dynamicid');
        $arctypeModel = M('Arctype');
        $typeids = '';
        foreach($arctypeModel->select('id')
                             ->where("reid = $typeid")
                             ->find_all() as $val)
        {
            $typeids .= $val['id'] . ',';    
        }
        $typeids = substr($typeids, 0, -1);
        
        return $this->select()
                    ->where("typeid IN ($typeids) AND type <> 1")
                    ->order_by('senddate', 'DESC')
                    ->limit(0,3)
                    ->find_all();
    }
    
    /**
     * @todo 获取模板库列表内容
     * 
     */
    public function getTplList($page)
    {
        $limit = 15;
        $offset = ($page-1)*$limit;
        $arcTypeModel = M('Arctype');
        
        $tplTypes = $arcTypeModel->getTplType();
        array_unshift($tplTypes, array('id'=>-1, 'typename'=>'全部'));
        
        foreach($tplTypes as $key => $val){
            $typeid = $val['id'];
            if($typeid == -1){ //表示全部
                $tmpid  = C('default.tmpid');
                $inids  = array();
                foreach($arcTypeModel->select('id')
                                     ->where("reid = $tmpid")
                                     ->find_all() as $val){
                    $inids[] = $val['id'];
                }
                $_inids = implode(',', $inids);
                $allcount = $this->count("typeid IN ($_inids) AND type <> 1 AND FIND_IN_SET('a', `flag`)");
                $tplTypes[$key]['allcount'] = (int)$allcount;
                $tplTypes[$key]['page'] = $page;
                
                $data = $this->select("$this->table.*, yun_arctype.typename")
                             ->left('yun_arctype', 'typeid', 'id')
                             ->where("$this->table.typeid IN ($_inids) AND $this->table.type <> 1 AND FIND_IN_SET('a',`flag`)")
                             ->order_by("$this->table.senddate", 'ASC')
                             ->limit($offset, $limit)
                             ->find_all();
                foreach($data as $k => $v){
                    $data[$k]['typeid'] = 'all';
                }
                $tplTypes[$key]['tpl'] = $data;
            } else {
                $allcount = $this->count("$this->table.typeid = $typeid AND $this->table.type <> 1");
                $tplTypes[$key]['allcount'] = (int)$allcount;
                $tplTypes[$key]['page'] = $page;
                
                $data = $this->select("$this->table.*, yun_arctype.typename")
                             ->left('yun_arctype', 'typeid', 'id')
                             ->where("$this->table.typeid = $typeid AND $this->table.type <> 1")
                             ->order_by("$this->table.senddate", 'ASC')
                             ->limit($offset, $limit)
                             ->find_all();
                $tplTypes[$key]['tpl'] = $data;
            }
        }
        return $tplTypes;
    }
    
    /**
     * @todo 获取模板详细信息
     * 
     */
    public function getTplDetail($aid, $typeid)
    {
        $addonImages  = M('AddonImages');
        $arcTypeModel = M('Arctype');
        
        $detail  = $this->select()->where("id = $aid")->find();
        $real_typeid = $detail['typeid'];
        
        //过滤typeid
        if($typeid != 'all' && !is_numeric($typeid)) $typeid = 'all';
        
        //取当前文章的上一个和下一个的文章id
        if($typeid == 'all'){
            $tmpid  = C('default.tmpid');
            $inids  = array();
            foreach($arcTypeModel->select('id')
                                 ->where("reid = $tmpid")
                                 ->find_all() as $val){
                $inids[] = $val['id'];
            }
            $_inids = implode(',', $inids);
            $allarchives = $this->select('id')->where("typeid IN ($_inids) AND type <> 1 AND FIND_IN_SET('a', `flag`)")->order_by('senddate', 'ASC')->find_all();
        } else {
            $allarchives = $this->select('id')->where("typeid = $typeid AND type <> 1")->order_by('senddate', 'ASC')->find_all();
        }
        
        $count = count($allarchives);
        $detail['count']  = $count;
        
        //是否是本分类的第一篇文章
        $isfirst = false;
        $firstid = current($allarchives);
        if($aid == $firstid['id']) $isfirst = true;
        $detail['isfirst'] = $isfirst;
        
        //是否是本分类的最后一篇文章
        $islast = false;
        $lastid = end($allarchives);
        if($aid == $lastid['id']) $islast = true;
        $detail['islast'] = $islast;
        
        //上一个下一个id
        foreach($allarchives as $key => $val){
            if($val['id'] == $aid){
                if(!$isfirst){
                    $prevkey = $key-1;
                    if($prevkey < 0) $prevkey = 0;
                    $detail['previd'] = $allarchives[$prevkey]['id'];
                }
                if(!$islast) $detail['nextid'] = $allarchives[$key+1]['id'];
            }
        }
        
        $detail['typename'] = $arcTypeModel->select('typename')->where("id = $real_typeid")->get()->typename;
        
        $ret = $addonImages->select()->where("aid = $aid AND typeid = $real_typeid")->find();
        preg_match_all('/{dede:img.*?}(.*?){\/dede:img}/im', $ret['imgurls'], $match);
        $imgurls = array();
        if($match[1]) {
            foreach($match[1] as $key => $val){
                $match[1][$key] = trim($val);
            }
            $imgurls = $match[1];
        }
        $detail['imgurls'] = $imgurls;
        
        return $detail;
    }
    
    /**
     * @todo 获取润云动态文章详细信息
     * 
     */
    public function getDynamicDetail($aid, $typeid)
    {
        $detail = $dys = array();
        $archivesModel = M('Archives');
        $arcTypeModel = M('Arctype');
        
        $data = $this->select("$this->table.*, yun_addonarticle.*")
                     ->left('yun_addonarticle', 'id', 'aid')
                     ->where("$this->table.typeid = $typeid AND $this->table.id = $aid AND $this->table.type <> 1")
                     ->find();
        
        $data['daytime'] = date('Y-m-d', $data['senddate']);
        $data['time'] = date('H:i:s', $data['senddate']);
        $data['typename'] = $arcTypeModel->select('typename')->where("id = $typeid")->get()->typename;
        
        $data['body'] = preg_replace('/<img[^>]+src=[\'|\"](.*?)[\'|\"].*?[\/]?[^>]>/im', '<img src="$1" />', $data['body']);
        $detail['detail'] = $data;
        
        /**
         * 此篇文章的上一篇和下一篇
         * 
         */
        foreach($archivesModel->select('id,pubdate,typeid')->where("typeid = $typeid")->order_by('pubdate','DESC')->find_all() as $val){
            $year = date('Y', $val['pubdate']);
            $dys[$year][] = $val;
        }

        $aids = array();
        foreach($dys as $key => $val){
            foreach($val as $v){
                $aids[] = $v['id'];
            }
        }
        $firstid = current($aids);
        $lastid = end($aids);
        
        //是否是第一篇
        $isfirst = false;
        if($firstid == $aid) $isfirst = true;
        $detail['isfirst'] = $isfirst;
        
        //是否是最后一篇
        $islast = false;
        if($lastid == $aid) $islast = true;
        $detail['islast'] = $islast;
        
        //获取上一篇和下一篇aid
        foreach($aids as $key => $id){
            if($id == $aid){
                if(!$isfirst){
                    $prevkey = $key-1;
                    if($prevkey < 0) $prevkey = 0;
                    $detail['previd'] = $aids[$prevkey];
                }
                if(!$islast) $detail['nextid'] = $aids[$key+1];
            }
        }
        
        return $detail;
    }
}