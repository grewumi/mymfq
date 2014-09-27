<?php
class user extends spController{
	public function __construct(){
		parent::__construct();
		$this->ucenter = spClass('spUcenter');
		if($_SESSION['user'])
			$this->uname = $_SESSION['user'];
	}
	public function register(){
		$this->registersuccess = 0;
		if($this->spArgs("submit")){
			$username = $this->spArgs("username");
			$password = $this->spArgs("password");
			$email = $this->spArgs("email");
			$questionid = $this->spArgs("questionid");
			$answer = $this->spArgs("answer");
			$uid = $this->ucenter->uc_user_register($username,$password,$email);
			if($uid <= 0){
				if($uid == -1){
					$this->regnote = 'ע��ʧ�ܣ��û������Ϸ�';
				}elseif($uid == -2){
					$this->regnote = 'ע��ʧ�ܣ�����Ҫ����ע��Ĵ���';
				}elseif($uid == -3){
					$this->regnote = 'ע��ʧ�ܣ��û����Ѿ�����';
				}elseif($uid == -4){
					$this->regnote = 'ע��ʧ�ܣ�Email ��ʽ����';
				}elseif($uid == -5){
					$this->regnote = 'ע��ʧ�ܣ�Email ������ע��';
				}elseif($uid == -6){
					$this->regnote = 'ע��ʧ�ܣ��� Email �Ѿ���ע��';
				}else{
					$this->regnote = 'ע��ʧ�ܣ�δ����';
				}
			}else{
				$this->registersuccess = 1;
				$this->regnote = 'ע��ɹ�!!';
			}
				
		}
		 
		$this->display("front/register.html");
	}
	public function login(){
		$loginstatus = $this->spArgs('cmd');
		if($loginstatus == 'out')
			$_SESSION['user'] = 0;
		if($this->spArgs("submit")){
			$username = $this->spArgs("username");
			$password = $this->spArgs("password");
			$email = $this->spArgs("email");
			$questionid = $this->spArgs("questionid");
			$answer = $this->spArgs("answer");
			$userinfo = $this->ucenter->uc_user_login($username,$password,$email);
			$uid = $userinfo[0];
			$uname = $userinfo[1];
			//var_dump($userinfo);
			if($uid > 0) {
				$this->loginsuccess = 1;
				$userinfo = $this->ucenter->uc_get_user($username);
				$_SESSION['user'] = $uname;
				$this->loginnote = '��¼�ɹ�';
			} elseif($uid == -1) {
				$this->loginnote = '�û�������,���߱�ɾ��';
			} elseif($uid == -2) {
				$this->loginnote = '�����';
			} else {
				$this->loginnote = 'δ����';
			}
		}
		
		if($_SESSION['user']){
			header("Location:/user/iinfo");
		}
		$this->display("front/login.html");
	}
	public function iinfo(){
		//echo $_SESSION['user'];
		$this->display("front/iinfo.html");
	}
	
}