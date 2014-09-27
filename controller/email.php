<?php
class email extends spController{
	public function __construct() {
		parent::__construct();
		$this->mailbody = file_get_contents('http://'.$_SERVER['HTTP_HOST'].'/?c=mkhtml');
	}
	public function getemail(){
		$datalist=list_dir('./tmp/email/');
		$data = array();
		foreach($datalist as $v){
			$file[] = file($v);
		}
		foreach($file as $iv){
			foreach($iv as &$line){
				$data[] = trim($line);
			}
		}
		return $data;
	}
	public function sendemail($smtpemailto,$mailsubject,$mailbody){
		set_time_limit(0);
		import("smtp.php");
		$smtpserver = "smtp.163.com";//SMTP服务器
		$smtpserverport = 25;//SMTP服务器端口
		$smtpusermail = "yimiaofengqiang@163.com";//SMTP服务器的用户邮箱
		
		$smtpuser = "yimiaofengqiang@163.com";//SMTP服务器的用户帐号
		$smtppass = "z123456";//SMTP服务器的用户密码
		
		$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
		$smtpemailto = $smtpemailto?$smtpemailto:"247176039@qq.com";//发送给谁
		$mailsubject = $mailsubject?$mailsubject:"一秒疯抢根据您的偏好【精选多款单品】任您选！";//邮件主题
		$mailbody = $mailbody?$mailbody:$this->mailbody;//邮件内容
		
//		##########################################
		$smtp = spClass("smtp");
		$smtp->smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
		$smtp->debug = FALSE;//是否显示发送的调试信息
		$allemail = $this->getemail();
//		$smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);
//		var_dump($allemail);
		foreach($allemail as $smtpemailto){
			sleep(1);
			$smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);
		}
		
	}
	
}