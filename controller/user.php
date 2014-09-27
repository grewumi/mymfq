<?php
import("tousers.php");
class user extends spController{
	public function __construct(){
		parent::__construct();
		$this->supe_uid = $GLOBALS['G_SP']['supe_uid'];
		$this->ucenter = spClass('spUcenter');
		$this->users = spClass("m_u");
		$this->member = spClass("m_member");
		$this->ggw = spClass("m_ggw");
                $this->mode = $this->spArgs("mode");
                $this->procats = spClass("m_procat")->findAll('isshow=1','type asc');
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
			$vcode = spClass('spVerifyCode');
			if($vcode->verify($this->spArgs('verifycode'))) {
				//ͨ����֤
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
					$this->regnote .= '<a href="/?c=user&a=login&cmd=reg">����ע��</a>';
				}else{
					$this->registersuccess = 1;
					$this->regnote = 'ע��ɹ�!!';
					$this->regnote .= '<a href="/?c=user&a=login">������¼</a>';
				}
			}else{
				//û��ͨ����֤
				$this->regnote = 'ע��ʧ�ܣ���֤�����';
				$this->regnote .= '<a href="/?c=user&a=login&cmd=reg">����ע��</a>';
			}
			
				
		}
	}
	/*
	 * 
	 */
	public function login(){
		/* ����ǩ�� */
//		$app_key = '21726073';
//		$secret='c23972d5f868ce97b17e66298a228136';
//		$timestamp=time()."000";
//		$message = $secret.'app_key'.$app_key.'timestamp'.$timestamp.$secret;
//		$mysign=strtoupper(hash_hmac("md5",$message,$secret));
//		setcookie("timestamp",$timestamp);
//		setcookie("sign",$mysign);
		/*  END - ����ǩ��*/
		//var_dump($_COOKIE);
	
		$loginstatus = $this->spArgs('cmd');	
		$refer = $this->spArgs("refer");
		if($refer)
			ssetcookie('_refer',$refer);
		else
			ssetcookie('_refer','');
		if($loginstatus == 'out'){
			clearcookie();
			header("Location:/?c=user&a=login");
		}elseif($loginstatus == 'reg'){
			$this->register();
		}else{
			if($this->spArgs("submit")){
				$username = $this->spArgs("username");
				$password = $this->spArgs("password");
				$email = $this->spArgs("email");
				$questionid = $this->spArgs("questionid");
				$answer = $this->spArgs("answer");
				$userinfo = $this->ucenter->uc_user_login($username,$password);
				$mtime = explode(' ', microtime());
				$uinfo = array(
					'uid'=>$userinfo[0],
					'username'=>$userinfo[1],
					'password'=>md5($userinfo[0].'|'.$mtime[1]),
					'email'=>$userinfo[3],
				);
				//var_dump($uinfo);
				//echo $uinfo['password'];
				$vcode = spClass('spVerifyCode');
				if($vcode->verify($this->spArgs('verifycode'))) {
					//ͨ����֤
					if($uinfo['uid'] > 0) {
						$this->loginsuccess = 1;
						$GLOBALS['G_SP']['supe_uid'] = $uinfo['uid'];
						//����cookie
						ssetcookie('auth', authcode($uinfo['password'].'\t'.$uinfo['uid'], 'ENCODE'), 31536000);
						ssetcookie('loginuser', $uinfo['username'], 31536000);                                         
						// end - ����cookie
						$this->loginnote = '��¼�ɹ�';
					} elseif($uinfo['uid'] == -1) {
						$this->loginnote = '�û�������,���߱�ɾ��';
					} elseif($uinfo['uid'] == -2) {
						$this->loginnote = '�������';
					} else {
						$this->loginnote = 'δ����';
					}
				}else{
					//û��ͨ����֤
					$this->loginnote = '��¼ʧ�ܣ���֤�����';
					$this->loginnote .= '<a href="/?c=user&a=login&cmd=reg">���µ�¼</a>';
				}
				
			}
		}
		if($GLOBALS['G_SP']['supe_uid']){ // ��¼�ɹ���
			//var_dump($uinfo);
                    	
			if(!$this->member->find(array('uid'=>$GLOBALS['G_SP']['supe_uid']))){//û���ҵ��û������������ݵ�member��
				$this->member->create($uinfo);
			}
			if(!$this->users->find(array('uid'=>$GLOBALS['G_SP']['supe_uid']))){
				//echo '���û����';
				$newuser = array(
					'uid'=>$GLOBALS['G_SP']['supe_uid'],
					'username'=>$uinfo['username'],
					//'lastlogin'=>date("Y-m-d H:i:s")
				);
				//var_dump($newuser);
				$this->users->create($newuser);
				//echo $this->users->dumpSql();
				//echo '�û�������';
			}else{
				$this->users->update(array('uid'=>$GLOBALS['G_SP']['supe_uid']),array('lastlogin'=>date("Y-m-d H:i:s")));
			}
			
			// �û����
			$groups = $this->users->find(array('uid'=>$GLOBALS['G_SP']['supe_uid']));
			$group = $groups['group'];
			
                        $uinfos = $this->users->find(array('username'=>$uinfo['username']));
                        ssetcookie('dpww',$uinfos['ww'], 31536000); 
                        
			if($_COOKIE[$GLOBALS['G_SP']['SC']['cookiepre'].'_refer'])
				header("Location:".$_COOKIE[$GLOBALS['G_SP']['SC']['cookiepre'].'_refer']);
			else
				switchtogrouppage($group);		
		}
		$this->cmd = $loginstatus;
		$this->display("front/login.html");
	}
	
	public function iinfo(){
		if(!$GLOBALS['G_SP']['supe_uid'])
			header("Location:/?c=user&a=login");
		$act = $this->spArgs("act");
		$this->act = $act;
		$uinfos = $this->member->find(array('uid'=>$GLOBALS['G_SP']['supe_uid']));
		$this->uname = $uinfos['username'];
		
		$uinfo = $this->users->find(array('username'=>$uinfos['username']));
		//var_dump($uinfo);
		
		$this->lastlogin = $uinfo['lastlogin'];
		$this->ww = $uinfo['ww'];
		$this->hyjf = $uinfo['hyjf'];
		$this->ggws = $this->ggw->findAll(array('username'=>$this->uname));
		//var_dump($this->ggws);
		if($this->spArgs("submit")){
			$ww = $this->spArgs("ww");
			$iid = $this->spArgs("iid");
                        if($ww){
                            if($this->users->update(array('username'=>$this->uname),array('ww'=>$ww))){
                                ssetcookie('dpww',$ww, 31536000); 
                            }
                        }
				
			if($iid){
				if($this->hyjf>=900){
					$this->ggw->create(array('username'=>$this->uname,'iid'=>$iid,'dh'=>1));
					$this->users->update(array('username'=>$this->uname),array('hyjf'=>$this->hyjf-900));
				}
			}
		}
		
		if($act=='cz'){
			if($this->spArgs("submit")){
				$total = intval($_POST['money']);
				if(!$total) {
				  $total = 900;
				} 
//				$pay_bank = trim($_POST['pay_bank']);
//				$account = $_POST['_account'];
				$base_path = 'http://'.$_SERVER['HTTP_HOST'].'/?c=user&a=iinfo&act=cz';echo $base_path;
				$order_id = date('Y-m-d H:i:s', time()); //ʱ��ֵ��ΪΨһ�Ķ���ID��
				$subject = $body = '��ֵ'.$total.'Ԫ';
				$out_trade_no = date('Y-m-d H:i:s',time());
				//���������ID����2088��ͷ��16λ������
				$partner = '2088311838983110';
				//��ȫ�����룬�����ֺ���ĸ��ɵ�32λ�ַ�
				$security_code = '5fac3wolaqxry1kqg8s7z5jcij8fsd5h';
				//ǩԼ֧�����˺Ż�����֧�����ʻ�
				$seller_email = 'jianquds@163.com';
				$_input_charset = "UTF-8";
				$sign_type = "MD5"; //ǩ����ʽ
				$transport = 'https';//�ַ������ʽ
				$parameter = array(
				  "service"        => "create_direct_pay_by_user",  //��������
				  "partner"        => $partner,         //�����̻���
				  "return_url"     => $base_path.'alipay/return',      //ͬ������
				  "notify_url"     => $base_path.'alipay/notify',      //�첽����
				  "_input_charset" => 'UTF-8',  //�ַ�����Ĭ��ΪGBK
				  "subject"        => $subject,       //��Ʒ���ƣ�����
				  "body"           => $subject,       //��Ʒ����������
				  "out_trade_no"   => $out_trade_no,     //��Ʒ�ⲿ���׺ţ������֤Ψһ�ԣ�
				  "price"          => $total,           //��Ʒ���ۣ�����۸���Ϊ0��
				  "payment_type"   => "1",              //Ĭ��Ϊ1,����Ҫ�޸�
				  "quantity"       => "1",              //��Ʒ����������
				  "paymethod"        => 'directPay',
				  "defaultbank"        => '',
				  "logistics_fee"      => '0.00',        //�������ͷ���
				  "logistics_payment"  =>'BUYER_PAY',   //�������ø��ʽ��SELLER_PAY(����֧��)��BUYER_PAY(���֧��)��BUYER_PAY_AFTER_RECEIVE(��������)
				  "logistics_type"     =>'EXPRESS',     //�������ͷ�ʽ��POST(ƽ��)��EMS(EMS)��EXPRESS(�������)
				  //"receive _mobile" => ��,         //�ջ����ֻ�
				  "show_url"       => $base_path,        //��Ʒ�����վ
				  "seller_email"   => $seller_email,     //�������䣬����
				);
				import("alipay.class.inc.php");
				$alipay = new alipay_service($parameter, $security_code, $sign_type);
				$link = $alipay->create_url();
				header("Location: ".$link); 
			}
		}
                if($act=='bmbb'){
                    if($this->mode=='try'){
                            $pros = spClass("m_try_items");
                    }else{
                            $pros = spClass("m_pro");
                    }
                    $bmbb = $pros->findAll('ww="'.$this->ww.'" and channel=2');
//                  echo $pros->dumpSql();
                    $this->bmbb = $bmbb;
                }
		$uinfo = $this->ucenter->uc_get_user($this->uname);
		$this->uemail = $uinfo[2];//var_dump($uinfo);
		$this->display("front/iinfo.html");
	}
	
	public function _vcode(){
		$vcode = spClass('spVerifyCode');
		$vcode->display();
	}
	
}