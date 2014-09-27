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
					$this->regnote = '注册失败：用户名不合法';
				}elseif($uid == -2){
					$this->regnote = '注册失败：包含要允许注册的词语';
				}elseif($uid == -3){
					$this->regnote = '注册失败：用户名已经存在';
				}elseif($uid == -4){
					$this->regnote = '注册失败：Email 格式有误';
				}elseif($uid == -5){
					$this->regnote = '注册失败：Email 不允许注册';
				}elseif($uid == -6){
					$this->regnote = '注册失败：该 Email 已经被注册';
				}else{
					$this->regnote = '注册失败：未定义';
				}
			}else{
				$this->registersuccess = 1;
				$this->regnote = '注册成功!!';
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
				$this->loginnote = '登录成功';
			} elseif($uid == -1) {
				$this->loginnote = '用户不存在,或者被删除';
			} elseif($uid == -2) {
				$this->loginnote = '密码错';
			} else {
				$this->loginnote = '未定义';
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