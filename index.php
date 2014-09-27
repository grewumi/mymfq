<?php
define("SP_PATH",dirname(__FILE__).'/SpeedPHP');
define("APP_PATH",dirname(__FILE__));
define('LOCALDEVELOP',false);
date_default_timezone_set('Asia/Shanghai');
if(LOCALDEVELOP){
    $dbpasswd = '';
    $ucapi = 'http://ucenter.com';
}else{
    $dbpasswd = 'N]j]78R>jPKEML7edAC(';
    $ucapi = 'http://yonghu.yimiaofengqiang.com';
}
require 'config.php';
require(SP_PATH."/SpeedPHP.php");
import('md5password.php');
import("func.php");
import('common.php');
checkauth();
spRun(); 
