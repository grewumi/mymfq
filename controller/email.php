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
		$smtpserver = "smtp.163.com";//SMTP������
		$smtpserverport = 25;//SMTP�������˿�
		$smtpusermail = "yimiaofengqiang@163.com";//SMTP���������û�����
		
		$smtpuser = "yimiaofengqiang@163.com";//SMTP���������û��ʺ�
		$smtppass = "z123456";//SMTP���������û�����
		
		$mailtype = "HTML";//�ʼ���ʽ��HTML/TXT��,TXTΪ�ı��ʼ�
		$smtpemailto = $smtpemailto?$smtpemailto:"247176039@qq.com";//���͸�˭
		$mailsubject = $mailsubject?$mailsubject:"һ�������������ƫ�á���ѡ��Ʒ������ѡ��";//�ʼ�����
		$mailbody = $mailbody?$mailbody:$this->mailbody;//�ʼ�����
		
//		##########################################
		$smtp = spClass("smtp");
		$smtp->smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//�������һ��true�Ǳ�ʾʹ�������֤,����ʹ�������֤.
		$smtp->debug = FALSE;//�Ƿ���ʾ���͵ĵ�����Ϣ
		$allemail = $this->getemail();
//		$smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);
//		var_dump($allemail);
		foreach($allemail as $smtpemailto){
			sleep(1);
			$smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);
		}
		
	}
	
}