<?php
include_once 'func.php';
include_once 'tbtop/ItemGetRequest.php';
include_once 'tbtop/RequestCheckUtil.php';
include_once 'tbtop/TopClient.php';
include_once 'tbtop/TaobaokeItemsDetailGetRequest.php';
include_once 'tbtop/TaobaokeReportGetRequest.php';
include_once 'tbtop/ItemcatsGetRequest.php';
include_once 'tbtop/ShopGetRequest.php';
header("Content-Type:text/html;charset=gbk");

//$app=array('21463466'=>'91cd273f32da3a640d237595a1e827e0');
$sellsapp = array('23021902'=>'6a18682d4ed4bf5d4c7c3b55cbe21fe1');
$app = $sellsapp;
foreach($app as $k=>$v){
	global $Key,$Secret;
	$Key = $k;
	$Secret = $v;
}
function gettkreport($page=1){
	global $Key,$Secret;
	$c = new TopClient;
	$c->appkey = trim($Key);
	$c->secretKey = trim($Secret);
	$req = new TaobaokeReportGetRequest;
	$req->setFields("trade_id,pay_time,pay_price,num_iid,outer_code,real_pay_fee,commission_rate,commission,item_num,seller_nick,pay_time,app_key");
	$req->setDate("20130710");
	$req->setPageNo($page);
	$req->setPageSize(100);
	$resp = $c->execute($req);	
	$resp = object_to_array($resp->taobaoke_report->taobaoke_report_members);
	return $resp['taobaoke_report_member'];
	
}
function getShop($nick){
	global $Key,$Secret;
	$c = new TopClient;
	$c->appkey = trim($Key);
	$c->secretKey = trim($Secret);
	$req = new ShopGetRequest;
	$req->setFields("sid,cid,title,nick,desc,bulletin,pic_path,created,modified");
	$req->setNick($nick);
	$resp = $c->execute($req);
//	var_dump($resp);
	//$resp = object_to_array($resp->item);
	if($resp)
		return $resp;
}
function getItem($num_iid,$mode='taoke')
{
	global $Key,$Secret;
	$c = new TopClient;
	$c->appkey = trim($Key);
	$c->secretKey = trim($Secret);
	//$c->appkey = trim($Key);
	//$c->secretKey = trim($Secret);
	if($mode == 'normal'){
		$req = new ItemGetRequest;
		$req->setFields("title,num_iid,nick,pic_url,cid,list_time,detail_url,approve_status,delist_time,price,nick,freight_payer,post_fee,express_fee,ems_fee,auction_point,has_discount");
		$req->setNumIid($num_iid);
		$resp = $c->execute($req);
//		var_dump($resp);
		$resp = object_to_array($resp->item);
		//var_dump($resp);
	}
	elseif($mode == 'taoke'){
		$req = new TaobaokeItemsDetailGetRequest;
		$req->setFields("iid,title,detail_url,nick,cid,price,pic_url,seller_credit_score,click_url,shop_click_url");
		$req->setNumIids($num_iid);
		$resp = $c->execute($req);
		$resp = object_to_array($resp->taobaoke_item_details->taobaoke_item_detail);
		//var_dump($resp);
	}elseif($mode == 'approve_status'){
		$req = new ItemGetRequest;
		$req->setFields("approve_status");
		$req->setNumIid($num_iid);
		$resp = $c->execute($req);
		$resp = object_to_array($resp->item);
	}
	//var_dump($resp);
	if($resp)
		return $resp;
}
function getPcid($cid){
	global $Key,$Secret;
	$c = new TopClient;
	$c->appkey = trim($Key);
	$c->secretKey = trim($Secret);
	//$c->appkey = trim($Key);
	//$c->secretKey = trim($Secret);
	$req = new ItemcatsGetRequest;
	$req->setFields("cid,parent_cid,name");
	$req->setCids($cid);
	$resp = $c->execute($req);
	//var_dump($resp);
	
	$pcid = $resp->item_cats->item_cat->parent_cid;
	if($pcid==0){
		$resp = object_to_array($resp->item_cats->item_cat);
		//$resp['name'] = iconv('utf-8','gbk',$resp['name']);
		//var_dump($resp);
		return $resp;
	}else{
		// 递归返回切记这里也要返回,不然只能得到null,杯具了半天
		return getPcid($pcid);
	}
	
}

function getPcidNew($cid){
	$resp = file_get_contents('http://tiangou.uz.taobao.com//top/getpcid.php?id='.$cid);
	$rule  = '/class="J_TScriptedModule taeapp(.+?)>(.+?)<\/div>/is';
	preg_match_all($rule,$resp,$result,PREG_SET_ORDER);
//	echo trim($result[0][2]);
	$resp = json_decode(iconv('gbk','utf-8',trim($result[0][2])),1);
	$resp = array_multi2single($resp);
	if($resp){
		return $resp;
	}
}
/* if($_GET['mode']=='ajaxprocat'){
	include 'dbconfig.php';
	$sql = 'select * from '.$DBconfig['DBPREFIX'].'pro';
	$result = $legouPDO->query($sql);
	$result->setFetchMode(PDO::FETCH_ASSOC);
	$Pros = $result->fetchAll();
	//var_dump($Pros);
	foreach($Pros as $k=>$v){
		$pcid = getPcid($v['cid']);
		$pcid = $pcid['cid'];
		
		// 查询fstk_catmap对应类目
		$sql='select * from fstk_catmap where cid='.$pcid;
		$result= $legouPDO->query($sql);
		$result->setFetchMode(PDO::FETCH_ASSOC);
		$result = $result->fetchAll();

		$catMap = $result[0];
		//var_dump($catMap);
		if($catMap){ //如果商品类目有映射
			$v['cat'] = $catMap['type'];
		}else{
			$v['cat'] = 42;
		}
		
		$item = $v;
		$value = "";
		// 更新入数据库
		foreach($item as $k =>$v){
			$value.=$k."='".$v."',";
		}
		$value=substr($value,0,strlen($value)-1);
		$sql='update fstk_pro set '.$value.' where iid='.$item['iid'];
		
		$legouPDO->query($sql);
		//var_dump($v);
		//$sql='select * from fstk_catmap where cid='.$pcid;
		// end - 查询fstk_catmap对应类目
	}
	echo 'update complate!';
} */
function creatCids(){
	global $Key,$Secret;
	$c = new TopClient;
	$c->appkey = trim($Key);
	$c->secretKey = trim($Secret);
	$req = new ItemcatsGetRequest;
	$req->setFields("cid,parent_cid,name");
	$req->setParentCid(0);
	$resp = $c->execute($req);
	$resp = object_to_array($resp->item_cats);	
	//var_dump($resp);
	return $resp;
}
//foreach($)
function object_to_array($obj)
{
		$_arr = is_object($obj) ? get_object_vars($obj) : $obj;
		foreach ($_arr as $key => $val)
		{
			$val = (is_array($val) || is_object($val)) ? object_to_array($val) : $val;
			$arr[$key] = $val;
		}
		return $arr;
}
function getShopDetail($nick){
	
}
function getItemDetail($num_iid,$mode=1){
 	if($mode==2){
		$result = getItem($num_iid,'approve_status');
	}else{
//		$result = getItem($num_iid,'normal');
		$result = getItemNew($num_iid,'normal');
                if($result<0){
                    return -1;
                }else{
			$volume = 200;
			$item = array(
				"iid"=>$num_iid,
				"title"=>htmlspecialchars($result['title']),
				"nick"=>htmlspecialchars($result['nick']),
				"pic"=>'http://img01.taobaocdn.com/bao/uploaded/'.$result['pic_url'],
				"oprice"=>$result['price'],			
				"st"=>date("Y-m-d"),//商品上架时间
				"et"=>date("Y-m-d",86400*7+time()),//商品下架时间
				"cid"=>0,
				"link"=>'http://item.taobao.com/item.htm?id='.$num_iid,
				"rank"=>500,
				"postdt"=>date("Y-m-d"),
				"ischeck"=>1,
				"volume"=>$volume,
			);
			$item['title'] = preg_replace('/【.+?】/i','',$item['title']);
			//var_dump($item);
			// 运费
			$item['carriage']=1;
			// 淘宝或天猫商品(天猫有抽佣)
			if($result['tmall'])
				$item['shopshow']=0; //天猫
			else 
				$item['shopshow']=1;
			// 是否vip打折商品
			$item['shopv']=1;
                        
                        $cid = getcid($num_iid,$result['tmall']);//获取商品CID
                        
                        if($cid)
                            $item['cid'] = $cid;
                        
                        $item['cat'] = 42;//默认其他分类
                        
                        $item['commission_rate'] = getCommissionRate($item['iid']);
                        
//			var_dump($item);
			return $item;
		}
		
	}
}

function getItemNew($num_iid,$mode='taoke'){
	if($mode == 'normal'){
		$resp = file_get_contents('http://youpinba.uz.taobao.com/top/2.php?id='.$num_iid);
                $rule  = '/class="J_TScriptedModule taeapp(.+?)>(.+?)<\/div>/is';
                preg_match_all($rule,$resp,$result,PREG_SET_ORDER);
//                echo $resp;
//        	echo trim($result[0][2]);
                if(trim($result[0][2])!='null'){
                    $resp = json_decode(iconv('gbk','utf-8',trim($result[0][2])),1);
                    $resp = array_multi2single($resp);
                }else{
                    $resp = null;
                }
	}elseif($mode == 'taoke'){
		$req = new TaobaokeItemsDetailGetRequest;
		$req->setFields("iid,title,detail_url,nick,cid,price,pic_url,seller_credit_score,click_url,shop_click_url");
		$req->setNumIids($num_iid);
		$resp = $c->execute($req);
		$resp = object_to_array($resp->taobaoke_item_details->taobaoke_item_detail);
		//var_dump($resp);
	}elseif($mode == 'approve_status'){
		$req = new ItemGetRequest;
		$req->setFields("approve_status");
		$req->setNumIid($num_iid);
		$resp = $c->execute($req);
		$resp = object_to_array($resp->item);
	}
	if($resp['approve_status']){
		unset($resp['code']);
		unset($resp['msg']);
		unset($resp['sub_code']);
		unset($resp['sub_msg']);
	}
        
	if($resp){
            return $resp;
	}else{
            return -1;
	}
		
}
function array_multi2single($array){
	static $result_array = array();
	foreach($array as $k=>$value){
		if(is_array($value)){
			array_multi2single($value);
		}else{
			$result_array[$k] = $value;
		}
	}
	return $result_array;
}
function getcid($iid,$tmall){
    if($tmall){
        $resp = file_get_contents('http://detail.tmall.com/item.htm?id='.$iid);
        $rule  = '/TShop.Setup(.+?)"categoryId":"(\d+)"/is';
        preg_match_all($rule,$resp,$result,PREG_SET_ORDER);
        $cid = (int)$result[0][2];
    }else{
        $resp = file_get_contents('http://item.taobao.com/item.htm?id='.$iid);
        $rule  = '/g_config.idata={(.+?)rcid(.+?),(.+?)\'(\d+)\',/is';
        preg_match_all($rule,$resp,$result,PREG_SET_ORDER);
//        var_dump($result);
//	echo trim($result[0][2]);
        $cid = (int)$result[0][4];
    }
    if($cid)
        return $cid;
    else
        return 0;
}
?>
