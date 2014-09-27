<?php
$app=array('21677073'=>'77c4b369298415cad9888dde165c2df0');
foreach($app as $k=>$v){
	global $Key,$Secret;
	$Key = $k;
	$Secret = $v;
}

function createSign ($paramArr){
	global $appSecret;
	$sign = $appSecret;
	ksort($paramArr);
	foreach ($paramArr as $key => $val){
		if ($key != '' && $val != ''){
			$sign .= $key.$val;
		}
	}
	$sign.=$appSecret;
	$sign = strtoupper(md5($sign));
	return $sign;
}

function createStrParam ($paramArr){
	$strParam = '';
	foreach ($paramArr as $key => $val){
		if ($key != '' && $val != ''){
			$strParam .= $key.'='.urlencode($val).'&';
		}
	}
     return $strParam;
}

function getParamArr($iid,$api,$fields){
	global $Key,$Secret;
	return $paramArr = array(
				'app_key' => $Key,
				'method' => $api,
				'format' => 'json',
				'v' => '2.0',
				'sign_method'=>'md5',
				'timestamp' => date('Y-m-d H:i:s'),
				'fields' => $fields,
				'num_iid' => $iid
			);
}
function getItem($num_iid,$mode='taoke'){
	global $Key,$Secret;
	
	if($mode == 'normal'){
		$api = 'taobao.item.get';
		$fields = 'title,num_iid,nick,pic_url,cid,list_time,detail_url,approve_status,delist_time,price,freight_payer,post_fee,express_fee,ems_fee,approve_status,has_discount,auction_point';
	}
	elseif($mode == 'taoke'){
		;
	}elseif($mode == 'approve_status'){
		;
	}
	$paramArr = getParamArr($num_iid,$api,$fields);
	//����ǩ��
	$sign = createSign($paramArr);
	//��֯����
	$strParam = createStrParam($paramArr);
	$strParam .= 'sign='.$sign;
	//���ʷ���
	$url = 'http://gw.api.taobao.com/router/rest?'.$strParam; //ɳ�价�����õ�ַ
	//$result = file_get_contents($url);
	$ch = curl_init();
	// 2. ����ѡ�����URL
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	//@curl_setopt($curl, CURLOPT_HTTPHEADER, 'content-type: application/x-www-form-urlencoded;charset=UTF-8');
	// 3. ִ�в���ȡHTML�ĵ�����
	$result = curl_exec($ch);
	// 4. �ͷ�curl���
	curl_close($ch);
	var_dump($result); 
	if($result)
		return $result;
}



?>