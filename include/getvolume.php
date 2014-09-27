<?php

/* 
 * 获取商品地址内容
 */
function getvolumeurl($iid,$shop='c'){
	if($shop=='c'){
		$result = file_get_contents('http://item.taobao.com/item.htm?id='.$iid);
		$ptn = '/"apiItemInfo":"(.+?)"/is';
		preg_match_all($ptn,$result,$arr,PREG_SET_ORDER);
		//print_r($arr);
		if($arr[0][1])
			return $arr[0][1];
		else
			return -1;
	}	
}

/* 
 * 获取商品销量
 * C店有时候获取不到，打开的时候页面空白
 * 返回值，获取错误返回-1
 */
function getvolume($iid,$shop){
	if($shop){
		$shopshow = 'c';
		$url = getvolumeurl($iid,$shopshow);
	}else{
		$shopshow = 't';
		$url = 'http://a.m.tmall.com/i'.$iid.'.htm';
	}
	if($url){
		if($shopshow=='c'){
			$result = file_get_contents($url);
			$qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");
			$str = str_replace($qian,$hou,$result);
			$ptn = '/quanity:(.+?),/is';	
			preg_match_all($ptn,$str,$arr,PREG_SET_ORDER);
			$volume = trim($arr[0][1]);
		}else{
			$result = file_get_contents($url);
			$ptn = '/class="detail"(.+?)class=" odd "(.+?)<\/p>(.+?)class=" even "(.+?)class=" odd "(.+?)(\d+)(.+?)<\/p>(.+?)class="rate_desc"/is';	
			preg_match_all($ptn,$result,$arr,PREG_SET_ORDER);
			$volume = trim($arr[0][6]);
		}
		if($volume>=0)
			return trim($volume);	
		else
			return -1;
	}else{
		return -1;
	}
	
}
?>
