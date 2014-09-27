<?php
/////////////////////////////////////////////////////////////////
// SpeedPHP����PHP���, Copyright (C) 2008 - 2010 SpeedPHP.com //
/////////////////////////////////////////////////////////////////

define('SP_VERSION', '3.1.89.1'); // ��ǰ��ܰ汾
if (substr(PHP_VERSION, 0, 1) != '5')exit("SpeedPHP��ܻ���Ҫ��PHP5��");
/**
 * spCore
 *
 * SpeedPHPӦ�ÿ�ܵ�ϵͳִ�г���
 */

// ����ϵͳ·��
if(!defined('SP_PATH')) define('SP_PATH', dirname(__FILE__).'/SpeedPHP');
if(!defined('APP_PATH')) define('APP_PATH', dirname(__FILE__).'/app');

// ������ĺ�����
require(SP_PATH."/spFunctions.php");

// ���������ļ�
$GLOBALS['G_SP'] = spConfigReady(require(SP_PATH."/spConfig.php"),$spConfig);

// ���������ļ�����һЩȫ�ֱ����Ķ���
if('debug' == $GLOBALS['G_SP']['mode']){
	define("SP_DEBUG",TRUE); // ��ǰ���ڵ���ģʽ��
}else{
	define("SP_DEBUG",FALSE); // ��ǰ���ڲ���ģʽ��
}
// ����ǵ���ģʽ���򿪾������
if (SP_DEBUG) {
	if( substr(PHP_VERSION, 0, 3) == "5.3" ){
		error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
	}else{
		error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
	}
} else {
	error_reporting(0);
}
@set_magic_quotes_runtime(0);

// �Զ�����SESSION
if($GLOBALS['G_SP']['auto_session'])@session_start();

// �������MVC�ܹ��ļ�
import($GLOBALS['G_SP']["sp_core_path"]."/spController.php", FALSE, TRUE);
import($GLOBALS['G_SP']["sp_core_path"]."/spModel.php", FALSE, TRUE);
import($GLOBALS['G_SP']["sp_core_path"]."/spView.php", FALSE, TRUE);

// ���ڶ���Ŀ¼��ʹ��SpeedPHP���ʱ���Զ���ȡ��ǰ���ʵ��ļ���
if('' == $GLOBALS['G_SP']['url']["url_path_base"]){
	if(basename($_SERVER['SCRIPT_NAME']) === basename($_SERVER['SCRIPT_FILENAME']))
		$GLOBALS['G_SP']['url']["url_path_base"] = $_SERVER['SCRIPT_NAME'];
	elseif (basename($_SERVER['PHP_SELF']) === basename($_SERVER['SCRIPT_FILENAME']))
		$GLOBALS['G_SP']['url']["url_path_base"] = $_SERVER['PHP_SELF'];
	elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === basename($_SERVER['SCRIPT_FILENAME']))
		$GLOBALS['G_SP']['url']["url_path_base"] = $_SERVER['ORIG_SCRIPT_NAME'];
}

// ��ʹ��PATH_INFO������£���·�ɽ���Ԥ����
if(TRUE == $GLOBALS['G_SP']['url']["url_path_info"] && !empty($_SERVER['PATH_INFO'])){
	$url_args = explode("/", $_SERVER['PATH_INFO']);$url_sort = array();
	for($u = 1; $u < count($url_args); $u++){
		if($u == 1)$url_sort[$GLOBALS['G_SP']["url_controller"]] = $url_args[$u];
		elseif($u == 2)$url_sort[$GLOBALS['G_SP']["url_action"]] = $url_args[$u];
		else {$url_sort[$url_args[$u]] = isset($url_args[$u+1]) ? $url_args[$u+1] : "";$u+=1;}}
	if("POST" == strtoupper($_SERVER['REQUEST_METHOD'])){$_REQUEST = $_POST =  $_POST + $url_sort;
	}else{$_REQUEST = $_GET = $_GET + $url_sort;}
}

// ����ִ��·��
$__controller = isset($_REQUEST[$GLOBALS['G_SP']["url_controller"]]) ? 
	$_REQUEST[$GLOBALS['G_SP']["url_controller"]] : 
	$GLOBALS['G_SP']["default_controller"];
$__action = isset($_REQUEST[$GLOBALS['G_SP']["url_action"]]) ? 
	$_REQUEST[$GLOBALS['G_SP']["url_action"]] : 
	$GLOBALS['G_SP']["default_action"];

// �Զ�ִ���û�����
if(TRUE == $GLOBALS['G_SP']['auto_sp_run'])spRun();