<?php

/* 
 * 店铺信誉检测
 */

function checkrate($iid){
	$url = 'http://item.taobao.com/item.htm?id='.$iid;
	$result = file_get_contents($url);
	$ptn = '/class="rank-ico(.+?)href="(.+?)"(.+?)>/is';	
	preg_match_all($ptn,$result,$arr,PREG_SET_ORDER);
	$ratepageurl = trim($arr[0][2]);
	
	$result = null;
	$arr = null;
	$result = file_get_contents($ratepageurl);
	$ptn = '/卖家信用(.+?)(\d+)(.+?)</is';	
	preg_match_all($ptn,$result,$arr,PREG_SET_ORDER);
	
	$rate = trim($arr[1][2]);
	if($rate>=251)
		return 2;
	else
		return -1;
}
?>
