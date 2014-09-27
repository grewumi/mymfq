<?php
global $cookie_info_real;

/* 获取验证码图片地址 */
function getCons($url){
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false); 
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($ch);
	//print_r($result);
	curl_close($ch);
	return $result;
}
function getCheckcode($referer){
	$result = getCons($referer);
	$ptn = '/id="J_StandardCode_m"(.+?)data-src="(.+?)"/is';	
	preg_match_all($ptn,$result,$arr,PREG_SET_ORDER);
	//echo $arr[0][2];
	//$arr[0][2]	= 'http://img02.taobaocdn.com/bao/uploaded/i2/T1FMxUFChnXXXXXXXX_!!0-item_pic.jpg';
	$url = 'http://'.$_SERVER['HTTP_HOST'].'/?c=virtualapi&a=getCheckcode&imgurl='.urlencode($arr[0][2]);
	echo '<img src="'.$arr[0][2].'" />';
	$result = file_get_contents($url);
	return trim($result);
}
/* END - 获取验证码图片地址 */


function openhttp_login($url, $post='', $cookie='', $referfer='', $host='', $show_header = 1)
{
    $header = array();

	if(!empty($host)){
		$header[] = "Host: ".$host;
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	//echo $cookie;
	if($cookie){
		curl_setopt($ch, CURLOPT_COOKIE,$cookie);
	}else{
		if(!empty($post)) curl_setopt($ch, CURLOPT_POST, 1);
		if(!empty($post)) curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	}
	//echo $post;
	if(count($header)) curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	if($show_header ==1){
		curl_setopt ($ch, CURLOPT_HEADER, 1);
	}
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0");
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	if(!empty($referfer)) curl_setopt($ch, CURLOPT_REFERER, $referfer);
	$return = curl_exec($ch);
	
	curl_close($ch);
	//header("Content-type: text/html; charset=utf-8"); 
	//print_r($return);
	return $return;
}

function get_items($string, $start=array(), $end=array(), $fiter_tag=1)	{
	$item_result = array();
	
	preg_match_all('#'.$start[0].'(.*?)'.$end[0].'#s', $string, $item);
	$item_result = $item[1];
	
	if(count($start)==2) {
		$item_result_arr = array();
		if(count($item_result)>1){
			foreach ($item_result as $key => $value){
				preg_match_all('#'.$start[1].'(.*?)'.$end[1].'#s', $value, $items);
				$item_result_arr[] = $items[1][0];
			}
		} else {
			preg_match_all('#'.$start[1].'(.*?)'.$end[1].'#s', $item_result[0], $items);
			$item_result_arr[] = $items[1];
		}
		
		 $item_result = $item_result_arr;
	}
	
	if(count($start)==3) {
		$item_result_arr = array();
		//if(strstr($string, $start[2]) && strstr($string, $end[2])){
		if(count($item_result)){
			foreach ($item_result as $key => $value){
				preg_match_all('#'.$start[2].'(.*?)'.$end[2].'#s', $value, $itemss);
				$item_result_arr = $itemss[1][0];
			}
			
			$item_result = $item_result_arr;
		}
		//}
	}
	
	return $item_result;
}

function getCookie($type = 'db')
{
    if($type != 'db')
    {
		return read_log('cookie.txt');
    }
}

function read_log($filename = ''){
    return trim(file_get_contents('../tmp/cookie/'.$filename));
}

function isLogin($content)
{
	$userinfo = get_items($content, array('<span id="J_loginInfo">欢迎您'), array('<'));
	if(empty($userinfo) || count($userinfo)<=0)
	{
		return false;
	}

	return true;
}



function get_taobao_header_cookie($html_content = '')
{
	$cookies = array();

	$cookie_item = get_items($html_content, array('Set-Cookie: '), array(";"));
	
	foreach($cookie_item as $key => $val)
	{
		$d = explode('=', trim($val));
    	$cookies[$d[0]] = trim($d[1]);
	}
	
	//echo $cookies;
	return $cookies;
}

function array2cookie($cookie_ary)
{
    $cookie = '';

    foreach($cookie_ary as $k=>$v)
    {
        $cookie .= $k.'='.$v.';';
    }

	return rtrim($cookie, ';');
}


function saveCookie($cookie, $type = 'db')
{
    if($type != 'db')
    {
        write_cookie($cookie);
        return ;
    }
}

function write_cookie($string, $filename = 'cookie.txt')
{
//	if (is_writable($filename)) {
		if(!$handle = fopen('../tmp/cookie/'.$filename,"w")) {
         	die("open $filename error.");
		}
//	} else {
//    	die("$filename No Write.");
//	}

    $string = trim($string);

    if(!empty($string))
    if(!fwrite($handle, $string, strlen($string))) 
    {
        die("fwrite 1 $filename");
    }

	fclose($handle);
}

function loginTaobao($user = '', $pass = '')
{
    global $cookie_info_real;
    $cookie = getCookie('cookie.txt');
	//echo $cookie;
    $content = openhttp_login('http://www.alimama.com/index.htm', "", $cookie);
 	if(strstr(htmlspecialchars($content),'http://www.alimama.com/member/logout.htm')){
		return true;
    }

    date_default_timezone_set ( 'PRC' );

    //$user = 'alantong1446';//$_GET ['user'];
    //$pass = 'wyh841017';//$_GET ['pass'];

    $url = 'https://login.taobao.com/member/login.jhtml';

    //$data = 'TPL_username=' . $user . '&TPL_password=' . $pass . '&TPL_checkcode=&need_check_code=0&loginsite=0&newlogin=1&TPL_redirect_url=http%3A%2F%2Fi.taobao.com%2Fuser%2Fheadset.htm%3Fspm%3D0.0.0.0.3tIfn5%26tracelog%3DPhoto011&from=tb&fc=default&style=default&css_style=&tid=&support=000001&CtrlVersion=1%2C0%2C0%2C7&loginType=3&minititle=&minipara=&umto=NaN&pstrong=2&llnick=&sign=&need_sign=&isIgnore=&full_redirect=&popid=&callback=1&guf=&not_duplite_str=&need_user_id=&poy=&gvfdcname=10&gvfdcre=&from_encoding=&sub=&oslanguage=&sr=&osVer=windows%7C5.1&naviVer=ie%7C8';
    //$ua = 'ua=188u5ObXObBitH2MRYO9Oz0bASM1EzUrOTDGcM%3D%7CuKBnf0c%2Ft6%2BHH5eft89Xb7U%3D%7CuZFW7MtSC4eLQLuXzODHACe%2FmF94AplyOcK%2BgqV%2FpQ%3D%3D%7CvoZB%2B9NbU5TsZHy7g4uDRGxUDMvTm9PbHISMhENbE1tTlBx0fKZ8%7Cvzfw1%2FAq%7CvCTjxOM5%7CvaW9esDnbCBsYFz0A%2FQ%2FxL8kzyhkf4SvtE%2Bok2izRJ%2FEHzRvQ7R%2FhP9kj2jDSATfFOzQnADLkEiTCON4RN8ECMOY%2F5S4Q5hAW5AL0MsQC2xAG1DL57yHy5G7cqohbSEtelIKMit8VAwkTRoyakIr4Btg%2BxD3u6BbcGuQd0y3bDsTS2MK0YpReiENWnIqAmsnPMfs9wy37LuTy%2BOKQbrBWrFW3ZFKHTVtVTynm2GqUSqxWg0lfUUOFnEaRo1GvJDLgBs3bFcbw4%2Bj%2F6TDiBMvNB%2FUj5XJUmnSG%2BCbAOszf1MPVHO0kxhUGBS8S7zAvMAnPPdst6x3bIuwS5BnvOc8F0xgl1yn3EesS8CMV6%2BTn0SclHN7nJRze5ykPBVpET0awBo%3D%7CsqqSVe%2FIsinCiXIOMjVNaq2K8GmAh5%2BHQMgPh4%2BHQNiQt223%7Cs6uDRGOoUyizWH%2Bl%7CsKgw901FgtpSaq21%2FTodceqxCsFIU8q9elKVvSoN1w0%3D%7CsamhZkH7w7uTqyPkLgkRKVHZwelhaXHpsanxPTXSWmI6EkpCOhJKQioiKqI6QoivaHCqjVc%3D%7Ctu4pk7T%2B9D3Hy9Ds0KxXezDK0vUyiJCIoGf%2F5z368jUSNfLaUlpiuGI%3D%7Ct88IspXf1Rzm6vHN8Y12WhHr89QTC8zkbGQ85jw%3D%7CtNwboYbMxg%2F1%2BeLe4p5lSQL44McAaDD3%2FzgAKLCIUog%3D%7Ctd0aoIfNxw70%2BOPf459kSAP54cYBmeEmLumhmdH5I%2Fk%3D%7CqsIFv5jS2BHr5%2FzA%2FIB7Vxzm%2Ftkehg7JwQZOJr62bLY%3D%7Cq8MEvpnT2RDq5v3B%2FYF6Vh3n%2F9gfhx%2FY0Bdfx7837Tc%3D%7CqMAHvZrQ2hPp5f7C%2FoJ5VR7k%2FNscBAwEw8sMVHwEDNYM%7CqcEGvJvR2xLo5P%2FD%2F4N4VB%2Fl%2FdodBQ0V0todRS0lPec9%7CrsYBu5zW3BXv4%2FjE%2BIR%2FUxji%2Bt0aAgoi5e0qcurCilCK%7Cr%2Fcwiq0E%2FsSYs2jzv4XJ0p4FPhneZHx0TIuTy5NJjoZBZkGG7nYeNuw2%7CrNQTqY7Ezgf98erW6pZtQQrw6M8IAMevN18H3Qc%3D%7CrdUSqI8m3Oa6kUrRnafr8LwnHDv85CNL07sj%2BSM%3D%7Couri6i0l4soNZaLKwgV9FdLK0opNVT0V0sryml1lPRXSSkKFndUSKlKVvYVCakKFrbVyWlKVjRXSykKFneUier2VHdryilA%3D';
	/* POST参数 */
	$par = array(
		'TPL_username'=>$user,
		'TPL_password'=>$pass,
		'need_check_code'=>'',
		'TPL_checkcode'=>'',
		'loginsite'=>0,
		'newlogin'=>1,
		'TPL_redirect_url'=>urlencode('http://login.taobao.com/member/taobaoke/login.htm?is_login=1'),
		'from'=>'alimama',
		'fc'=>'default',
		'style'=>'minisimple',
		'css_style'=>'',
		'tid'=>'XOR_1_000000000000000000000000000000_665C475344787D070870067F',
		//'tid'=>'',
		'support'=>'000001',
		'CtrlVersion'=>urlencode('1,0,0,7'),
		'loginType'=>3,
		'minititle'=>'',
		'minipara'=>'',
		'umto'=>'Td8678f6552c1a89c38bc040e1170c9d0',
		'pstrong'=>2,
		'llnick'=>'',
		'need_sign'=>'',
		'sign'=>'',
		'isIgnore'=>'',
		'full_redirect'=>'true',
		'popid'=>'',
		'callback'=>'1',
		'guf'=>'',
		'not_duplite_str'=>'',
		//'not_duplite_str'=>'',
		'need_user_id'=>'',
		'poy'=>'',
		'gvfdcname'=>'10',
		//'gvfdcre'=>'687474703A2F2F7777772E616C696D616D612E636F6D2F6D656D6265722F6C6F67696E2E68746D3F73706D3D302E302E302E302E333942773772',
		'gvfdcre'=>'',
		'from_encoding'=>'',
		'sub'=>'',
		'oslanguage'=>'zh-CN',
		'sr'=>'1440*900',
		'osVer'=>urlencode('windows|6.1'),
		'naviVer'=>urlencode('firefox|29')
	);
	/* END - POST参数 */
	foreach($par as $k=>$v){
		$data .= $k.'='.$v.'&';
	}
	$referer = 'https://login.taobao.com/member/login.jhtml?style=minisimple&from=alimama&redirectURL=http%3A%2F%2Flogin.taobao.com%2Fmember%2Ftaobaoke%2Flogin.htm%3Fis_login%3d1&full_redirect=true&disableQuickLogin=true';
	
	/* 取得token */
    $html = openhttp_login($url, $data, '', $referer, '', 0);//普通模拟登陆
    $token_info = json_decode($html,true);
//	echo $html;
    if($token_info['state']){
	    $token = trim($token_info['data']['token']);
		$loginsuccess = 1;
    }else{//验证码模拟登陆
		$token_info = null;
		//header("Content-type: text/html; charset=utf-8");
		$checkcode = getCheckcode($referer);
		//echo $checkcode;
		$andcheckcode = 'need_check_code=true&TPL_checkcode='.$checkcode;
        //echo trim(getCheckcode($referer));
		$data = preg_replace('/need_check_code=&TPL_checkcode=/',$andcheckcode,$data);
		echo $data;
//		$ch = curl_init();
//		curl_setopt($ch,CURLOPT_URL,$referer);
//		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false); 
//		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
//		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
//		$result = curl_exec($ch);
//		curl_close($ch);
//		echo $result;
		
		$html = openhttp_login($url,$data ,'', $referer, '', 0);
		echo $html;
		$token_info = json_decode($html,true);
		if($token_info['state']){
			$token = trim($token_info['data']['token']);
			$loginsuccess = 1;
		}else{
			$token = 'none';
			$loginsuccess = 0;
		}
    }
//	echo $html;
	/* end - token */
	
	//存储cookie
	if($loginsuccess){
		$url = 'https://passport.alipay.com/mini_apply_st.js?site=0&token=' . $token . '&callback=vstCallback69';
		$html = openhttp_login($url, '', '', $referer, '', 0);
		
		$st_info = get_items($html, array('"st":"'), array('"'));
		$st = $st_info[0];
		//echo $st;
		
		$url = 'https://login.taobao.com/member/vst.htm?st=' . $st . '&params=style%3Ddefault%3F%3D%26longLogin%3D0%26TPL_username%3D'.$user.'%26loginsite%3D0%26from_encoding%3D%26not_duplite_str%3D%26guf%3D%26full_redirect%3D%26isIgnore%3D%26need_sign%3D%26sign%3D%26from%3Dalimama%26TPL_redirect_url%3Dhttp%3A%2F%2Flogin.taobao.com%2Fmember%2Ftaobaoke%2Flogin.htm%3Fis_login%3D1%26full_redirect%3Dtrue%26disableQuickLogin%3Dtrue%26tracelog%3DPhoto011%26_ksTS%3D1369726452671_81%26callback%3Djsonp82';
		$html = openhttp_login($url, '', '', '', '', 1);
		//echo $html;
		$cookie_info = get_taobao_header_cookie($html);
		//echo $cookie_info;
		$url_info = get_items($html, array('"url":"'), array('"'));
		$url = $url_info[0];
		

		$html = openhttp_login($url, '', array2cookie($cookie_info), '', '', 1);
		$cookie_info_2 = get_taobao_header_cookie($html);
		//print_r($cookie_info_2);
		//获取最终的cookie
		$cookie_info_real = array_merge($cookie_info, $cookie_info_2);
		
		$url = 'http://u.alimama.com/union/myunion/myOverview.htm';
		$html = openhttp_login($url,"",array2cookie($cookie_info_real),"","",1);
//		echo $html;
		
		saveCookie(array2cookie($cookie_info_real), 'file');
		
		return true;
	}else{
		//echo $html;
		return false;
	}
	// end - 存储cookie
    //-------------1进入alimama页面
    //$url = 'http://u.alimama.com/union/myunion/myOverview.htm';
    //===========================
    //$url = 'http://pub.alimama.com/pubauc/searchAuctionList.json?q=http%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D'.$iid;
    //===========================
    //$url = 'http://www.alimama.com/user/limit_status.htm';
    //echo $url;
    //============================
    //$html = openhttp_login($url, '', array2cookie($cookie_info_real), '', '', 0);
    //===============================
    //echo $html;
    /*$charset = get_items($html, array('charset='), array('"'));
    if(strtolower($charset[0]) == 'utf-8')
    {
        $html = iconv('utf-8','gbk', $html);
    }
     */
        
    //$html = iconv('gbk', 'utf-8',$html);
    //return  $html;
    //================================
    //$result = json_decode($html,1);
    //if($result)
    //    return $result['data']['pagelist'][0]['commissionRate']/100;
    /*=================================
    if(isLoginisLogin($html)){
		saveCookie(array2cookie($cookie_info_real), 'file');
        return $html;
        return 1;
    }

    return -1;
     */
}
//
//function getCommissionRate($iid){
//    global $cookie_info_real;
//	$cookie = getCookie('cookie.txt');
////	echo $cookie;
////    $url = 'http://pub.alimama.com/pubauc/searchAuctionList.json?q=http%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D'.$iid;
////    $html = openhttp_login($url, '',$cookie, '', '', 0);
////	$url = 'http://www.alimama.com/index.htm';
//	$url = 'http://u.alimama.com/union/myunion/myOverview.htm';
//	$html = openhttp_login($url,"",$cookie,"","",1);
//	echo $html;
//    $result = json_decode($html,1);
//    if($result){
//		if($result['data']['pagelist'])
//			return $result['data']['pagelist'][0]['commissionRate']/100;
//		else
//			return -1;
//	}else{
//		return -2;
//	}
//}
function getCommissionRate($iid){
    $resp = file_get_contents('http://d.qumai.org/tool/action.php?action=autologin&type=getall&iid='.$iid);
    $rule  = '/"commissionRate":(\d+\.?\d+)/is';
    preg_match_all($rule,$resp,$result,PREG_SET_ORDER);
    if($result)
        return $result[0][1];
    else
        return -1;
}
if($_GET['loginAlimamaTest']){
	if(loginTaobao('liushiyan8','liujun987')){
		echo 'login success';
		//getCommissionRate('123456789');
	}else{
		echo 'login failed';
	}
}
?>
