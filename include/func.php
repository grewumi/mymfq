<?php
require 'checklogo.php';
require 'checkrate.php';
require 'getvolume.php';
require 'pregcaijicontent.php';
function getapiurl($website){
	$apiIp = '121.199.33.15';
	return 'http://'.$apiIp.'/uzcaiji/type/'.$website.'.html';
}

function get_url_content($url) {
	$contents=file_get_contents($url);
	if($contents){
		return $contents;
	}elseif(function_exists("curl_init")){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
		$contents = curl_exec($ch);
		curl_close($ch);
		return $contents;
	}
}

// N维数组去空值
function array_no_empty($arr) {
    if (is_array($arr)) {
        foreach ( $arr as $k => $v ) {
            if (empty($v)) unset($arr[$k]);
            elseif (is_array($v)) {
                $arr[$k] = array_no_empty($v);
            }
        }
    }
    return $arr;
}

//cookie设置
function ssetcookie($var, $value, $life=0) {
	setcookie($GLOBALS['G_SP']['SC']['cookiepre'].$var, $value, $life?($GLOBALS['G_SP']['timestamp']+$life):0, $GLOBALS['G_SP']['SC']['cookiepath'], $GLOBALS['G_SP']['SC']['cookiedomain'], $_SERVER['SERVER_PORT']==443?1:0);
}

//清空cookie
function clearcookie() {
	ssetcookie('auth', '', -86400 * 365);
	$GLOBALS['G_SP']['supe_uid'] = '';
}

//字符串解密加密
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

	$ckey_length = 4;	// 随机密钥长度 取值 0-32;
				// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
				// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
				// 当此值为 0 时，则不产生随机密钥

	$key = md5($key ? $key : $GLOBALS['G_SP']['ext']['spUcenter']['UC_KEY']);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}

/*
*功能：php完美实现下载远程图片保存到本地
*参数：文件url,保存文件目录,保存文件名称，使用的下载方式
*当保存文件名称为空时则使用远程文件原来的名称
*/
function getImage($url,$save_dir='',$filename='',$type=0){
	if(trim($url)==''){
		return array('file_name'=>'','save_path'=>'','error'=>1);
	}
	if(trim($save_dir)==''){
		$save_dir='./';
	}
    if(trim($filename)==''){//保存文件名
        $ext=strrchr($url,'.');
        if($ext!='.gif'&&$ext!='.jpg'){
			return array('file_name'=>'','save_path'=>'','error'=>3);
		}
        $filename=time().$ext;
    }
	if(0!==strrpos($save_dir,'/')){
		$save_dir.='/';
	}
	//创建保存目录
	if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
		return array('file_name'=>'','save_path'=>'','error'=>5);
	}
    //获取远程文件所采用的方法 
    if($type){
		$ch=curl_init();
		$timeout=5;
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false); 
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		$img=curl_exec($ch);
		curl_close($ch);
		//echo $img;
    }else{
		//echo $url;
		ob_start();
		readfile($url);
		$img = ob_get_contents(); 
		ob_end_clean(); 
    }
    //$size=strlen($img);
	//echo $img;
    //文件大小 
	//echo $save_dir.$filename;
    $fp2=@fopen($save_dir.$filename,'w');
    fwrite($fp2,$img);
    fclose($fp2);
	unset($img,$url);
    return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0);
}
//获取文件列表
function list_dir($dir){
	$result = array();
	if (is_dir($dir)){
		$file_dir = scandir($dir);
		foreach($file_dir as $file){
			if ($file == '.' || $file == '..'){
				continue;
			}
			elseif (is_dir($dir.$file)){
				$result = array_merge($result, list_dir($dir.$file.'/'));
			}
			else{
				array_push($result, $dir.$file);
			}
		}
	}
	return $result;
}
?>