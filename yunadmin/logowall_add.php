<?php
/**
 * logo墙添加
 *
 * @version        $Id: friendlink_add.php 1 10:59 2010年7月13日Z tianya $
 * @package        DedeCMS.Administrator
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require(dirname(__FILE__)."/config.php");
CheckPurview('plus_友情链接模块');
if(empty($dopost)) $dopost="";

if($dopost=="add")
{
    $dtime = time();
    if(is_uploaded_file($logoimg))
    {
        $names = split("\.", $logoimg_name);
        $shortname = ".".$names[count($names)-1];
        if(!preg_match("#(jpg|gif|png)$#", $shortname))
        {
            $shortname = '.gif';
        }
        $filename = MyDate("ymdHis", time()).mt_rand(1000,9999).$shortname;
        $imgurl = $cfg_medias_dir."/logowall";
        if(!is_dir($cfg_basedir.$imgurl))
        {
            MkdirAll($cfg_basedir.$imgurl, $cfg_dir_purview);
            CloseFtp();
        }
        $imgurl = $imgurl."/".$filename;
        move_uploaded_file($logoimg,$cfg_basedir.$imgurl) or die("复制文件到:".$cfg_basedir.$imgurl."失败");
        @unlink($logoimg);
    }
    else
    {
        $imgurl = $logo;
    }
    
    //强制检测用户友情链接分类是否数据结构不符
    if(empty($typeid) || preg_match("#[^0-9]#", $typeid))
    {
        $typeid = 0;
        $dsql->ExecuteNoneQuery("ALTER TABLE `#@__flinktype` CHANGE `ID` `id` MEDIUMINT( 8 ) UNSIGNED DEFAULT NULL AUTO_INCREMENT; ");
    }
    
    $query = "INSERT INTO `#@__logowall`(sortrank,url,name,logo,msg,email,typeid,dtime)
            VALUES('$sortrank','$url','$name','$imgurl','$msg','$email','$typeid','$dtime'); ";
    $rs = $dsql->ExecuteNoneQuery($query);
    $burl = empty($_COOKIE['ENV_GOBACK_URL']) ? "friendlink_main.php" : $_COOKIE['ENV_GOBACK_URL'];
    if($rs)
    {
        ShowMsg("成功增加一个logo!",$burl,0,500);
        exit();
    }
    else
    {
        ShowMsg("增加logo时出错");
        exit();
    }
}
include DedeInclude('templets/logowall_add.htm');