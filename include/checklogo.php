<?php
function trimall($str)//删除空格
{
	$qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");
	return str_replace($qian,$hou,$str);	
}
/*C点左侧代码获取*/
function getleft($iid){
	$result = file_get_contents('http://item.taobao.com/item.htm?id='.$iid);
	$ptn = '/Hub.config.set(.+?)async_sys(.+?)support/is';
	preg_match_all($ptn,$result,$arr,PREG_SET_ORDER);
	$str = trimall($arr[0][2]);
	$ptn = '/api(.+?)api:\'(.+?)\'/is';
	preg_match_all($ptn,$str,$arr1,PREG_SET_ORDER);
	return $arr1[0][2];
}
function getpararr($text){
	$temp = explode(";",trim($text));
	foreach($temp as $k=>$v){
		$p = explode("=",$v);
		$arr[trim($p[0])] = trim($p[1]);
	}
	//print_r($arr);
	return $arr;
}
/*
	getpar($iid,$where='left',$shop='c');
	参数 
	iid=>商品IID
	where=>左侧或者详情页
	shop=>C店或者天猫店
*/
function getpar($iid,$where='left',$shop='c'){
	if($shop=='t')
		$result = file_get_contents('http://detail.tmall.com/item.htm?id='.$iid);
	else
		$result = file_get_contents('http://item.taobao.com/item.htm?id='.$iid);
	
	if($where=='left'){
		$ptn = '/name="microscope-data"(.+?)content="(.+?)"/i';
		preg_match_all($ptn,$result,$arr,PREG_SET_ORDER);
		//print_r($arr);
		$pars = getpararr($arr[0][2]);
		//print_r($pars);
		return $pars;
	}elseif($where=='dec'){
		if($shop=='t'){
			$ptn = '/"descUrl":"(.+?)"/i';
		}else{
			$ptn = '/"apiItemDesc":"(.+?)"/i';			
		}
		preg_match_all($ptn,$result,$arr,PREG_SET_ORDER);
		return $arr[0][1];
	}
}
/*
	检查左侧LOGO
	checkleftlogo($iid,$shop='c');
	参数 
	iid=>商品IID
	shop=>C店或者天猫店
	返回值
	2=>true
	-1=>false
*/
function checkleftlogo($iid,$shop='c'){
	if($shop=='c'){
		/* $pars = getpar($iid);
		$post = array(
			'p'=>1,
			'g'=>'dc',
			'mods'=>'css,head,side,main,foot,sv',
			'_cb'=>'Hub.mods.asyn.dc',
			'v'=>2,
			'sv'=>1,
			'siteId'=>$pars['siteId'],
			'virtual'=>'false',
			'flagShip'=>'false',
			'int'=>'true',
			'ins'=>'true',
			'dn'=>'',
			'sci'=>2,
			'h'=>'p_lazyHd_sid'.$pars['shopId'].'_pid'.$pars['pageId'],
			'c'=>'css_sid'.$pars['shopId'],
			'l'=>'p_lazyLeft_sid'.$pars['shopId'].'_pid'.$pars['pageId'].'_v2',
			'r'=>'p_lazyRight_sid'.$pars['shopId'].'_pid'.$pars['pageId'],
			'f'=>'p_lazyFt_sid'.$pars['shopId'].'_pid'.$pars['pageId'],
			//'t'=>'1397440209714',
			'cb'=>'TB.Async.parse'
		);
		//print_r($post);
		foreach($post as $k=>$v){
			$posts .= $k.'='.$v.'&'; 
		}
		$url = 'http://sd4.tbcdn.cn/asyn.htm?'.$posts; */
		$url = getleft($iid);
		//echo $url;
		$result = file_get_contents($url);
		//echo $result;
		return checklogo($result);	
	}elseif($shop=='t'){
		$pars = getpar($iid);
		$post = array(
			'callback'=>'jsonpDC',
			'pid'=>$pars['pageId'],
			'sellerId'=>$pars['userid'],
			//'t'=>'1397475599000'
		);
		foreach($post as $k=>$v){
			$posts .= $k.'='.$v.'&'; 
		}
		$url = 'http://tdecorate.tbcdn.cn/dc/fetchDc.htm?'.$posts;
		//echo $url;
		$result = file_get_contents($url);
		//echo $result;
		return checklogo($result);	
	}
}
/*
	检查详情页LOGO
	checkxqylogo($iid,$shop='c');
	参数 
	iid=>商品IID
	shop=>C店或者天猫店
	返回值
	2=>true
	-1=>false
*/
function checkxqylogo($iid,$shop='c'){
	if($shop=='c'){
		$url = getpar($iid,'dec'); 
	}elseif($shop=='t'){
		$url = getpar($iid,'dec','t');
	}
	//echo $result;
	$result = file_get_contents($url);
	return checklogo($result);	
}
function checklogo($result){
	if($result){
		//echo $result;
		if(strstr($result,'yinxiang.uz.taobao.com'))
			return 2;
		else{
			if(strstr(html_entity_decode($result),'yinxiang.uz.taobao.com'))
				return 2;
			else
				return -1;
		}
	}else{
		return -2;
	}
}
?>