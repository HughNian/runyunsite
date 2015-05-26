<?php
/**
 * logo墙编辑
 *
 * @version        $Id: friendlink_edit.php 1 10:59 2010年7月13日Z tianya $
 * @package        DedeCMS.Administrator
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");

$ENV_GOBACK_URL = empty($_COOKIE['ENV_GOBACK_URL']) ? 'friendlink_main.php' : $_COOKIE['ENV_GOBACK_URL'];
if(empty($dopost)) $dopost = "";

if(isset($allid))
{
    $aids = explode(',',$allid);
    if(count($aids)==1)
    {
        $id = $aids[0];
        $dopost = "delete";
    }
}
if($dopost=="delete")
{
    $id = preg_replace("#[^0-9]#", "", $id);
    $dsql->ExecuteNoneQuery("DELETE FROM `#@__logowall` WHERE id='$id'");
    ShowMsg("成功删除一个logo！",$ENV_GOBACK_URL);
    exit();
}
else if($dopost=="delall")
{
    $aids = explode(',',$aids);
    if(isset($aids) && is_array($aids))
    {
        foreach($aids as $aid)
        {
            $aid = preg_replace("#[^0-9]#", "", $aid);
            $dsql->ExecuteNoneQuery("DELETE FROM `#@__logowall` WHERE id='$aid'");
        }
        ShowMsg("成功删除指定logo！",$ENV_GOBACK_URL);
        exit();
    }
    else
    {
        ShowMsg("你没选定任何logo！",$ENV_GOBACK_URL);
        exit();
    }
}
else if($dopost=="saveedit")
{
	require_once DEDEINC.'/request.class.php';
	$request = new Request();
	$request->Init();
    $id = preg_replace("#[^0-9]#", "", $request->Item('id', 0));
	$logo = $request->Item('logo', '');
	$logoimg = $request->Upfile('logoimg', '');
	if(!empty($logoimg))
	{
		$request->MoveUploadFile('logoimg', DEDEROOT.'/uploads/logowall/'.$request->GetFileInfo('logoimg', 'name'));
		$logo = $cfg_cmspath.'/uploads/logowall/'.$request->GetFileInfo('logoimg', 'name');
	}
	$sortrank = $request->Item('sortrank', 1);
	$url = $request->Item('url', '');
	$name = $request->Item('name', '');
	$msg = $request->Item('msg', '');
	$email = $request->Item('email', '');
	$typeid = $request->Item('typeid', 0);
	$ischeck = $request->Item('ischeck', 0);
	
    $query = "UPDATE `#@__logowall` SET sortrank='$sortrank',url='$url',name='$name',logo='$logo',msg='$msg',
                  email='$email',typeid='$typeid',ischeck='$ischeck' WHERE id='$id' ";
    
    $dsql->ExecuteNoneQuery($query);
    ShowMsg("成功更改一个logo！",$ENV_GOBACK_URL);
    exit();
}
$myLink = $dsql->GetOne("SELECT #@__logowall.* FROM #@__logowall WHERE id=$id");
include DedeInclude('templets/logowall_edit.htm');