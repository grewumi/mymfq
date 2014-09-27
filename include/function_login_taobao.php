<?php
global $cookie_info_real;

function openhttp_login($url, $post='', $cookie='', $referfer='', $host='', $show_header = 1)
{
    $header = array();

	if(!empty($host))
	{
		$header[] = "Host: ".$host;
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_COOKIE,$cookie);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

	//登录阿里妈妈添加
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);

	if(!empty($referfer)) curl_setopt($ch, CURLOPT_REFERER, $referfer);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:22.0) Gecko/20100101 Firefox/22.0");
	if(count($header)) curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	if($show_header ==1)
	{
		curl_setopt ($ch, CURLOPT_HEADER, 1);
	}
    if(!empty($post)) curl_setopt($ch, CURLOPT_POST, 1);
    if(!empty($post)) curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

	$return = curl_exec($ch);

	curl_close($ch);

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

function read_log($filename = '')
{
    return trim(file_get_contents($filename));

	//$filename = "log/log.txt";

	if(!$handle = fopen($filename, "r")) {
 		print "读取文件: $filename 出错";
 		exit;
	}

	while(!feof($handle))
  	{
  		$rows[] = trim(fgets($handle));
  	}

	fclose($handle);

	return $rows;
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
	if (is_writable($filename)) {
		if(!$handle = fopen($filename, "w")) {
         	die("open $filename error.");
		}
	} else {
    	die("$filename No Write.");
	}

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
    $cookie = getCookie('file');
    $content = openhttp_login('http://www.alimama.com/index.htm', "", $cookie);

    if(strstr($content, '您正在使用的阿里妈妈帐号是'))
    {
        return 2;
    }

    date_default_timezone_set ( 'PRC' );

    //$user = 'alantong1446';//$_GET ['user'];
    //$pass = 'wyh841017';//$_GET ['pass'];

    $url = 'https://login.taobao.com/member/login.jhtml';

    //$data = 'TPL_username=' . $user . '&TPL_password=' . $pass . '&TPL_checkcode=&need_check_code=0&loginsite=0&newlogin=1&TPL_redirect_url=http%3A%2F%2Fi.taobao.com%2Fuser%2Fheadset.htm%3Fspm%3D0.0.0.0.3tIfn5%26tracelog%3DPhoto011&from=tb&fc=default&style=default&css_style=&tid=&support=000001&CtrlVersion=1%2C0%2C0%2C7&loginType=3&minititle=&minipara=&umto=NaN&pstrong=2&llnick=&sign=&need_sign=&isIgnore=&full_redirect=&popid=&callback=1&guf=&not_duplite_str=&need_user_id=&poy=&gvfdcname=10&gvfdcre=&from_encoding=&sub=&oslanguage=&sr=&osVer=windows%7C5.1&naviVer=ie%7C8';
    $data = 'ua=188u5ObXObBitH2MRYO9Oz0bASM1EzUrOTDGcM%3D%7CuKBnf0c%2Ft6%2BHH5eft89Xb7U%3D%7CuZFW7MtSC4eLQLuXzODHACe%2FmF94AplyOcK%2BgqV%2FpQ%3D%3D%7CvoZB%2B9NbU5TsZHy7g4uDRGxUDMvTm9PbHISMhENbE1tTlBx0fKZ8%7Cvzfw1%2FAq%7CvCTjxOM5%7CvaW9esDnbCBsYFz0A%2FQ%2FxL8kzyhkf4SvtE%2Bok2izRJ%2FEHzRvQ7R%2FhP9kj2jDSATfFOzQnADLkEiTCON4RN8ECMOY%2F5S4Q5hAW5AL0MsQC2xAG1DL57yHy5G7cqohbSEtelIKMit8VAwkTRoyakIr4Btg%2BxD3u6BbcGuQd0y3bDsTS2MK0YpReiENWnIqAmsnPMfs9wy37LuTy%2BOKQbrBWrFW3ZFKHTVtVTynm2GqUSqxWg0lfUUOFnEaRo1GvJDLgBs3bFcbw4%2Bj%2F6TDiBMvNB%2FUj5XJUmnSG%2BCbAOszf1MPVHO0kxhUGBS8S7zAvMAnPPdst6x3bIuwS5BnvOc8F0xgl1yn3EesS8CMV6%2BTn0SclHN7nJRze5ykPBVpET0awBo%3D%7CsqqSVe%2FIsinCiXIOMjVNaq2K8GmAh5%2BHQMgPh4%2BHQNiQt223%7Cs6uDRGOoUyizWH%2Bl%7CsKgw901FgtpSaq21%2FTodceqxCsFIU8q9elKVvSoN1w0%3D%7CsamhZkH7w7uTqyPkLgkRKVHZwelhaXHpsanxPTXSWmI6EkpCOhJKQioiKqI6QoivaHCqjVc%3D%7Ctu4pk7T%2B9D3Hy9Ds0KxXezDK0vUyiJCIoGf%2F5z368jUSNfLaUlpiuGI%3D%7Ct88IspXf1Rzm6vHN8Y12WhHr89QTC8zkbGQ85jw%3D%7CtNwboYbMxg%2F1%2BeLe4p5lSQL44McAaDD3%2FzgAKLCIUog%3D%7Ctd0aoIfNxw70%2BOPf459kSAP54cYBmeEmLumhmdH5I%2Fk%3D%7CqsIFv5jS2BHr5%2FzA%2FIB7Vxzm%2Ftkehg7JwQZOJr62bLY%3D%7Cq8MEvpnT2RDq5v3B%2FYF6Vh3n%2F9gfhx%2FY0Bdfx7837Tc%3D%7CqMAHvZrQ2hPp5f7C%2FoJ5VR7k%2FNscBAwEw8sMVHwEDNYM%7CqcEGvJvR2xLo5P%2FD%2F4N4VB%2Fl%2FdodBQ0V0todRS0lPec9%7CrsYBu5zW3BXv4%2FjE%2BIR%2FUxji%2Bt0aAgoi5e0qcurCilCK%7Cr%2Fcwiq0E%2FsSYs2jzv4XJ0p4FPhneZHx0TIuTy5NJjoZBZkGG7nYeNuw2%7CrNQTqY7Ezgf98erW6pZtQQrw6M8IAMevN18H3Qc%3D%7CrdUSqI8m3Oa6kUrRnafr8LwnHDv85CNL07sj%2BSM%3D%7Couri6i0l4soNZaLKwgV9FdLK0opNVT0V0sryml1lPRXSSkKFndUSKlKVvYVCakKFrbVyWlKVjRXSykKFneUier2VHdryilA%3D&TPL_username='.$user.'&TPL_password='.$pass.'&TPL_checkcode=&need_check_code=&loginsite=0&newlogin=1&TPL_redirect_url=http%3A%2F%2Flogin.taobao.com%2Fmember%2Ftaobaoke%2Flogin.htm%3Fis_login%3D1&from=alimama&fc=default&style=minisimple&css_style=&tid=XOR_1_000000000000000000000000000000_63584054400B0F717B750379&support=000001&CtrlVersion=1%2C0%2C0%2C7&loginType=3&minititle=&minipara=&umto=Tbbde2080d975691d0e4e8065e7433eb7&pstrong=2&llnick=&sign=&need_sign=&isIgnore=&full_redirect=true&popid=&callback=1&guf=&not_duplite_str=&need_user_id=&poy=&gvfdcname=&gvfdcre=687474703A2F2F7777772E616C696D616D612E636F6D2F6D656D6265722F6C6F67696E2E68746D3F73706D3D302E302E302E302E333942773772&from_encoding=&sub=&oslanguage=&sr=1440*900&osVer=windows%7C6.1&naviVer=ie%7C9';


    $referer = 'https://login.taobao.com/member/login.jhtml?style=minisimple&from=alimama&redirectURL=http%3A%2F%2Flogin.taobao.com%2Fmember%2Ftaobaoke%2Flogin.htm%3Fis_login%3d1&full_redirect=true&disableQuickLogin=true';

    $html = openhttp_login($url, $data, '', $referer, '', 0);
    //echo $html;
    $token_info = json_decode($html, true);
    //var_dump($token_info);
    if($token_info['state'] == 1)
    {
	    $token = trim($token_info['data']['token']);
    }
    else
    {
        //$html = iconv('gbk', 'utf-8', $html);
        //$html = iconv('utf-8','gbk',$html);
        //$token_info = json_decode($html, true);
        
        die('get token error :: '.$token_info['message']);
    }

    $url = 'https://passport.alipay.com/mini_apply_st.js?site=0&token=' . $token . '&callback=vstCallback69';
    $html = openhttp_login($url, '', '', $referer, '', 0);
    $st_info = get_items($html, array('"st":"'), array('"'));
    $st = $st_info[0];

    $url = 'https://login.taobao.com/member/vst.htm?st=' . $st . '&params=style%3Ddefault%3F%3D%26longLogin%3D0%26TPL_username%3D'.$user.'%26loginsite%3D0%26from_encoding%3D%26not_duplite_str%3D%26guf%3D%26full_redirect%3D%26isIgnore%3D%26need_sign%3D%26sign%3D%26from%3Dalimama%26TPL_redirect_url%3Dhttp%3A%2F%2Flogin.taobao.com%2Fmember%2Ftaobaoke%2Flogin.htm%3Fis_login%3D1%26full_redirect%3Dtrue%26disableQuickLogin%3Dtrue%26tracelog%3DPhoto011%26_ksTS%3D1369726452671_81%26callback%3Djsonp82';
    $html = openhttp_login($url, '', '', '', '', 1);
    $cookie_info = get_taobao_header_cookie($html);
    $url_info = get_items($html, array('"url":"'), array('"'));
    $url = $url_info[0];


    $html = openhttp_login($url, '', array2cookie($cookie_info), '', '', 1);
    $cookie_info_2 = get_taobao_header_cookie($html);

    //获取最终的cookie
    $cookie_info_real = array_merge($cookie_info, $cookie_info_2);

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
    if(isLogin($html))
    {
	saveCookie(array2cookie($cookie_info_real), 'file');
        return $html;
        return 1;
    }

    return -1;
     */
}
function getCommissionRate($iid){
    global $cookie_info_real;
    $url = 'http://pub.alimama.com/pubauc/searchAuctionList.json?q=http%3A%2F%2Fitem.taobao.com%2Fitem.htm%3Fid%3D'.$iid;
    $html = openhttp_login($url, '', array2cookie($cookie_info_real), '', '', 0);
    $result = json_decode($html,1);
    if($result)
        return $result['data']['pagelist'][0]['commissionRate']/100;
}
