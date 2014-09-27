<?php
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
?>