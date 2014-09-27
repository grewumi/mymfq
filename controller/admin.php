<?php
class admin extends spController{
	public function __construct(){
		parent::__construct();
		$this->supe_uid = $GLOBALS['G_SP']['supe_uid'];
		import('public-data.php');
		import("function_login_taobao.php");
		global $caijiusers,$website;
		$this->caijiusers = $caijiusers;
		$this->mode = $this->spArgs('mode');
		// postdt>=curdate()Ϊ������ӣ�����������
		$pros = spClass('m_pro');
		$where = 'st<=curdate() and et>=curdate() and ischeck=1 and postdt>=curdate()';
		
		foreach($website as $k=>&$v){
			if($k!='none')
				$v['tcounts'] = count($pros->findAll('act_from='.$v['actType'].' and '.$where));
		}
		
		$this->website = $website; 
	} 
	
	public function login(){		
		$cmd = $this->spArgs('cmd');
		if($cmd=='out'){
			if($_SESSION['admin'] || $_SESSION['iscaijiuser']){
				if($_SESSION['admin'])
					$_SESSION['admin'] = null;
				else 
					$_SESSION['iscaijiuser'] = null;
			}
		}
			
		
		// ��¼�ж�
		if($this->spArgs()){
			if($this->spArgs('username')=='admin' && $this->spArgs('password')=='bingqiling1234'){
				$_SESSION['admin'] = 1;
				header("Location:/admin.html");
			}elseif($this->spArgs('username') && $this->spArgs('password')){
				foreach($this->caijiusers as $k=>$v){
					if($this->spArgs('username') == $v['username'] && $this->spArgs('password') == $v['password']){
						$_SESSION['iscaijiuser'] = $this->spArgs('username');
					}
				}
				// ������֤
				//$url_last = '.uz.taobao.com/view/front/getusernick.php';
				//$url = 'http://'.$_SESSION['iscaijiuser'].$url_last;
				// END - ������֤
				if($_SESSION['iscaijiuser'])
					header("Location:/dbselect.html");
			} 
		}
		else{
			header("Location:/login.html");
		} 
		
		$this->display("admin/login.html");
	}
	
	
	// ��ȡ��Ʒ��Ϣ
	public function getiteminfo(){
		/* if(!$_SESSION['admin'])
			header("Location:/login.html"); */
		
		$iid = trim($this->spArgs('iid'));
		
		$catmaps = spClass("m_catmap");
		import('tbapi.php');
		
		$item = getItemDetail($iid);
//                echo $item;
                if($item<0){
                    echo '{"iid":"-1"}';
                }else{
                    // �ݹ�ȡ���Ա������ڵ�
                    if($GLOBALS['G_SP']['autocat']){
                        $pcid = getPcidNew($item['cid']);
                        $pcid = $pcid['cid'];

                        // ��ѯfstk_catmap��Ӧ��Ŀ
                        $catMap = $catmaps->find(array('cid'=>$pcid),'','type');
                        //var_dump($catMap);
                        if($catMap){ //�����Ʒ��Ŀ��ӳ��
                                $item['cat'] = (int)$catMap['type'];
                        }else{
                                $item['cat'] = 42;
                        }
                    }
                    // end - �ݹ�ȡ���Ա������ڵ�


        //	    echo $pcid;
                    // end - ��ѯfstk_catmap��Ӧ��Ŀ

                    // �ַ�ת��
                    $item['title'] = iconv('utf-8','gb2312',$item['title']);
                    $item['title'] = preg_replace('/��.+?��/i','',$item['title']);
                    $item['nick'] = iconv('utf-8','gb2312',$item['nick']);
                    $item['volume'] = getvolume($iid,$item['shopshow']);
                    if(!$item['volume'])
                            $item['volume'] = -1;
                    // end - �ַ�ת��
                    //$item['sid'] = getShop($item['nick']);
                    //var_dump($item);
                    echo '{"iid":"'.$item['iid'].'","title":"'.$item['title'].'","nick":"'.$item['nick'].'","pic":"'.$item['pic'].'","oprice":"'.$item['oprice'].'","st":"'.$item['st'].'","et":"'.$item['et'].'","cid":"'.$item['cid'].'","link":"'.$item['link'].'","rank":'.$item['rank'].',"postdt":"'.$item['postdt'].'","ischeck":'.$item['ischeck'].',"volume":'.$item['volume'].',"carriage":'.$item['carriage'].',"shopshow":'.$item['shopshow'].',"shopv":'.$item['shopv'].',"cat":'.$item['cat'].',"commission_rate":'.$item['commission_rate'].'}';

                }
	}
	public function getCommissionRate($iid){
		if(getCommissionRate('38510058624')=='-2'){//cookieģ���¼ʧ��
			if(loginTaobao('liushiyan8','liujun987'))//���µ�¼(��֤���¼),����cookie
				$this->loginalimama = 1;
			else
				$this->loginalimama = 0;
			
			if($this->loginalimama)//��¼�ɹ�
				return getCommissionRate($iid);
			else
				return -2;
		}else{//cookieģ���½
			return getCommissionRate($iid);
		}
	}
	// ��̨��ҳ
	public function index(){
		ini_set('memory_limit','256M');
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		$pros = spClass("m_pro");
		// ����û���¼ܵ���Ʒͳ��
		if($result = $pros->findAll('st<=curdate() and et>=curdate()')){
			$this->allPros = count($result);
		}
		// �����ύ��û���¼ܵ���Ʒͳ��
		if($result = $pros->findAll('st<=curdate() and et>=curdate() and postdt>=curdate()')){
			$this->todayPros = count($result);
		}
		// ������Ʒ
		$guoqis = $pros->findAll('et<curdate()');
		$this->guoqis = count($guoqis);
		
		
		$this->indexCur = 1;
		$this->display("admin/index.html");
	}
	
	// ��Ʒ����
	public function xuqi(){
		$id = $this->spArgs("id");
		if($this->mode=='try'){
			$pros = spClass("m_try_items");
			$referUrl = spUrl('admin','pro',array('mode'=>'try'));
		}else{
			$pros = spClass("m_pro");
			$referUrl = spUrl('admin','pro',array('mode'=>'pro'));
		}
		$pros->update(array('id'=>$id),array('et'=>date("Y-m-d",time()+24*60*60*7)));
		header("Location:".$referUrl);
	}
	
	// ��Ʒ����
	public function pro(){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		$type = $this->spArgs('type');
		$sh = $this->spArgs('sh');
		$q = $this->spArgs('q');
		$status = $this->spArgs('status');
                $classification = $this->spArgs('classification')?$this->spArgs('classification'):1;
		
		if($this->mode=='try'){
			$pros = spClass("m_try_items");
			$this->tryItemCur = 1;
		}
		else{
			$pros = spClass("m_pro");
			$this->proCur = 1;
		}
		
		$page = $this->spArgs('page',1);

		$where = 'st<=curdate() and et>=curdate() and ischeck=1 and type!=87';
		$order = 'rank asc,postdt desc';
		
                if($type){
                    if($type==87)
                        $where = 'st<=curdate() and et>=curdate() and ischeck=1 and type='.$type;
                    else
                        $where .= ' and type='.$type;
                }
		
                if($classification)
                    $where .= ' and classification='.$classification;
                                
		if($sh=='no')
			$where = 'ischeck=0';
		elseif($sh=='ck2')
			$where = 'ischeck=2';
		
		if($status)
			$where = 'et<curdate()';
		
		if($q)
			$where = 'iid='.$q;
//                echo $where;
		$itemsTemp = $pros->spPager($page,56)->findAll($where,$order);
		
		$this->items = $itemsTemp;
		$this->pager = $pros->spPager()->getPager();
                $this->type = $type;
		$this->sh = $sh;
                $this->classification = $classification;
                
		$this->display("admin/pro.html");
	}
	// ��Ʒ���
	public function checkpro(){
		$id = $this->spArgs("id");
		if($this->mode=='try'){
			$pros = spClass("m_try_items");
		}else{
			$pros = spClass("m_pro");
		}
		$pro = $pros->find(array('id'=>$id));
                
                $userinfo = spClass("m_u")->find(array('ww'=>$pro['ww']));
		$uinfo =spClass('spUcenter')->uc_get_user($userinfo['username']);
		$uemail = $uinfo[2];//var_dump($uinfo);
                
		if($_POST['checkit']){
			if($_POST['checkpro']==1){
                                if($pros->update(array('id'=>$id),array('ischeck'=>1,'type'=>87))){
//                                    echo $pros->dumpSql();
                                    $mailsubject = '��ͨ����ˣ�';
                                    echo '�����ɹ�,��Ʒ��ͨ����ˣ�';
                                }					
			}elseif($_POST['checkpro']==2){
				if($_POST['reason'] || $_POST['reasonSelect']){
					if($_POST['reasonSelect']){
						foreach($_POST['reasonSelect'] as $v){
							$reason .= $v;
						}
					}
					if($_POST['reason']){
						$reason .= $_POST['reason'];
					}
					if($pros->update(array('id'=>$id),array('ischeck'=>2,'reason'=>'�� '.$reason))){
                                                $mailsubject = '��ͨ����ˣ�('.$reason.')';
						echo '�����ɹ�,��Ʒ��ͨ����ˣ�';
                                        }
				}else
					echo '����ʧ��,����д��ע��';
			}
                        
                        $mailbody = "<h1>����������Ʒ</h1><br />"
                        . "<a target='_blank' href='".$pro[link]."'>".$pro[title]."</a><h2><span style='color:red'>".$mailsubject."</span></h2><br />"
                        . "��ϵQQ:350544519";
                
                        if($uemail){
                            import("email.class.php");
                            $smtpserver = "smtp.163.com";//SMTP������
                            $smtpserverport =25;//SMTP�������˿�
                            $smtpusermail = "yimiaofengqiang@163.com";//SMTP���������û�����
                            $smtpuser = "yimiaofengqiang@163.com";//SMTP���������û��ʺ�
                            $smtppass = "z123456";//SMTP���������û�����

                            $smtpemailto = $uemail;//���͸�˭
                            $mailsubject = $mailsubject;//�ʼ�����
                            $mailbody = $mailbody;//�ʼ�����
                            $mailtype = "HTML";//�ʼ���ʽ��HTML/TXT��,TXTΪ�ı��ʼ�
                            ##########################################
                            $smtp = spClass("smtp");
                            $smtp->smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//�������һ��true�Ǳ�ʾʹ�������֤,����ʹ�������֤.
                            $smtp->debug = FALSE;//�Ƿ���ʾ���͵ĵ�����Ϣ
                            $smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);
                        }
                
//				header("Location:/pro/sh/no.html");
		}

                               
		$this->pro = $pro; 
		$this->display('admin/checkpro.html');
	}
	
	// �����Ƿ����
	public function isInThere($iid,$table='pro',$field=null){
		$pros = spClass("m_".$table);
		if($field){
			$count = 0;
			foreach (self::$dao->query('select * from '.self::$dbconfig['DBPREFIX'].$table.' where enunick='.$field) as $row) {
				$count += 1;
			}
		}else{
			$count = 0;
			foreach ($pros->find(array('iid'=>$iid)) as $row) {
				$count += 1;
			}
		}
		return $count;
	}
	
	// ��Ʒ���
	public function addpro($mode='pro'){
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		if($this->mode=='try'){
			$pros = spClass("m_try_items");
			$referUrl = spUrl('admin','pro',array('mode'=>'try'));
			$this->tryItemCur = 1;
		}else{
			$pros = spClass("m_pro");
			$referUrl = spUrl('admin','pro',array('mode'=>'pro'));
			$this->proCur = 1;
		}
		
//		$pros = spClass("m_pro");
		$actfrom = spClass("m_actfrom");
                $classification = spClass("m_classification");
		$proCat = spClass("m_procat");
		
		if($_POST['modPro']){
			$item = array(
					'pic'=>$_POST['pic'],
					'iid'=>$_POST['iid'],
					'oprice'=>$_POST['oprice'],
					'nprice'=>$_POST['nprice'],
					'st'=>$_POST['st'],
					'et'=>$_POST['et'],
					'cat'=>$_POST['cat'],
					'act_from' =>$_POST['act_from'],
                                        'classification' =>$_POST['classification'],
					'rank'=>(int)$_POST['rank'],
					'title'=>$_POST['title'],
					'link'=>$_POST['link'],
					'volume'=>(int)$_POST['volume'],
					'remark'=>$_POST['remark'],
					'zk'=>@ceil(10*$_POST['nprice']/$_POST['oprice']),
					'carriage'=>(int)$_POST['carriage'],
					'last_modify'=>date('Y-m-d H:i:s'),
					'ischeck'=>1,
					'postdt'=>date("Y-m-d"),
					'type'=>$_POST['type'],
					'shopshow'=>$_POST['shopshow'],
					'shopv'=>$_POST['shopv'],
					'ww'=>$_POST['ww'],
					'nick'=>$_POST['ww'],
			);
			if($_POST['commissionrate'])
				$item['commission_rate'] = $_POST['commissionrate'];
			else
				$item['commission_rate'] = -1;
			if($_POST['forward'])
				$item['postdt'] = date("Y-m-d H:i:s");
			if($this->mode!='try'){// ������Ʒ���
                                if($this->isInThere($item['iid'])){
					$submitTips = '��Ʒ�Ѵ���,�����ظ����';
				}else{
					$art = $pros->create($item);
					if($art){	//�޸ĳɹ�����ת
						$submitTips = '��ӳɹ�';
                                                if($GLOBALS['G_SP']['ajaxToUz']['addpro'])
                                                    $this->postDataToUzPhp($item,'admin');
//						header("Location:".$referUrl);
					}else
						$submitTips = '���ʧ��';
				}
			}else{// ������Ʒ���
                                unset($item['classification']);
				if($this->isInThere($item['iid'],'try_items')){
					$submitTips = '������Ʒ�Ѵ���,�����ظ����';
				}else{
					$item['istry'] = 1;
					$item['gailv'] = $_POST['gailv'];
					$art = $pros->create($item);
					if($art){	//�޸ĳɹ�����ת
						$submitTips = '��ӳɹ�';
						header("Location:".$referUrl);
					}
					else
						$submitTips = '���ʧ��';
				}
			}
		}
	
		if($mode=='try')
			$this->tryadd = "tryadd";
		$this->st = date("Y-m-d");
		$this->et = date("Y-m-d",86400*7+time());
		
		$actfroms = $actfrom->findAll();
		$proCats = $proCat->findAll();
                $classifications = $classification->findAll();
		// ��Ʒ���
		$this->actfroms = $actfroms;
		$this->proCats = $proCats;
                $this->classifications = $classifications;
		// �ύ��ʾ
		$this->submitTips = $submitTips;
		$this->display("admin/addpro.html");
	}
	
	// ɾ��������Ʒ
	public function delgq(){
	//	import('tbapi.php');
		$pros = spClass("m_pro");
		$gqPros = $pros->findAll('et<curdate()');
	//	foreach($gqPros as $k=>$v){
	//		$info = getItemDetail($v['iid']);
	//		$pros->update(array('iid'=>$v['iid']),array('et'=>$info['et']));
	//	}
		if($pros->delete('et<curdate()'))
			header("Location:/admin.html");
	}
	// ��Ʒɾ��
	public function delpro(){
		$id = $this->spArgs('id');
		if($this->mode=='try'){
			$pros = spClass("m_try_items");
			$referUrl = spUrl('admin','pro',array('mode'=>'try'));
                }else{
			$pros = spClass("m_pro");
			$referUrl = spUrl('admin','pro',array('mode'=>'pro'));
                }
                $iteminfo = $pros->find(array('id'=>$id));
                if($pros->delete(array('id'=>$id))){
                    $item = array('iid'=>$iteminfo['iid'],'del'=>1);
                    if($GLOBALS['G_SP']['ajaxToUz']['delpro'])
                        $this->postDataToUzPhp($item,'admin');
//                    header("Location:".$referUrl);
                }
		
	}
	// ��Ʒ�޸�
	public function modpro($mode='pro'){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		if($this->mode=='try'){
			$pros = spClass("m_try_items");
			$referUrl = spUrl('admin','pro',array('mode'=>'try'));
			$this->tryItemCur = 1;
		}else{
			$pros = spClass("m_pro");
			$referUrl = spUrl('admin','pro',array('mode'=>'pro'));
			$this->proCur = 1;
		}
		
		$actfrom = spClass("m_actfrom");
                $classification = spClass("m_classification");
		$proCat = spClass("m_procat");
		
		$id = $this->spArgs('id');
		$pro = $pros->find(array('id'=>$id));
                
		if($_POST['modPro']){
			$item = array(
					'pic'=>$_POST['pic'],
					'iid'=>$_POST['iid'],
					'oprice'=>$_POST['oprice'],
					'nprice'=>$_POST['nprice'],
					'st'=>$_POST['st'],
					'et'=>$_POST['et'],
					'cat'=>$_POST['cat'],
					'act_from' =>$_POST['act_from'],
                                        'classification' =>(int)$_POST['classification'],
					'rank'=>(int)$_POST['rank'],
					'title'=>$_POST['title'],
					'link'=>$_POST['link'],
					'volume'=>(int)$_POST['volume'],
					'remark'=>$_POST['remark'],
					'zk'=>@ceil(10*$_POST['nprice']/$_POST['oprice']),
					'carriage'=>(int)$_POST['carriage'],
					'last_modify'=>date('Y-m-d H:i:s'),
					'ischeck'=>1,
					'type'=>$_POST['type'],
					'shopshow'=>$_POST['shopshow'],
					'shopv'=>$_POST['shopv'],
					'ww'=>$_POST['ww'],
					'nick'=>$_POST['ww'],
			);
			if($_POST['commissionrate'])
				$item['commission_rate'] = $_POST['commissionrate'];
			else
				$item['commission_rate'] = -1;
			if($_POST['forward']){
				$item['st'] = date('Y-m-d');
				$item['postdt'] = date('Y-m-d H:i:s');
                        }else{
                            $item['postdt'] = $pro['postdt'];
                        }
			if($this->mode!='try'){
				$art = $pros->update(array('id'=>$id),$item);
			}else{
                                unset($item['classification']);
				$item['istry'] = 1;
				$item['gailv'] = $_POST['gailv'];
				$art = $pros->update(array('id'=>$id),$item);
			}
			if($art){ // �޸ĳɹ�����ת
				$submitTips = '�޸ĳɹ�';
//                                var_dump($item);
                                if($GLOBALS['G_SP']['ajaxToUz']['modpro'])
                                    $this->postDataToUzPhp($item,'admin');
//				if($this->mode!='try')
//					header("Location:".$referUrl);
//				else
//					header("Location:".$referUrl);
			}else
				$submitTips = '�޸�ʧ��';
		}
		
		
		
		$actfroms = $actfrom->findAll();
                $classifications = $classification->findAll();
		$proCats = $proCat->findAll();
		
		$this->submitTips = $submitTips;
		$this->pro = $pro;
		$this->actfroms = $actfroms;
                $this->classifications = $classifications;
		$this->proCats = $proCats;
		$this->display("admin/modpro.html");
	}
	
	// �û�����
	public function yonghu(){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		$users = spClass("m_u");
		$usersinfo = $users->findAll();
		$this->usersinfo = $usersinfo;
		if($_POST['submit']){
			$username = $this->spArgs("username");
			$hyjf = $this->spArgs("hyjf");
			$Shyjf = $users->find(array('username'=>$username));
			$Nhyjf = $Shyjf['hyjf'] + $hyjf;
			$art = $users->update(array('username'=>$username),array('hyjf'=>$Nhyjf));
			if($art)
				$this->tips = "��ֵ�ɹ���,��ˢ��ҳ��鿴";
			else
				$this->tips = "��ֵʧ�ܣ�";
		}
		if($_POST['super']){
			if(md5($this->spArgs("mima"))=='918d06b0e3b05da224cfdf3223f37e23')
				$this->superadmin = 1;
		}
		//$pros = spClass("m_pro");
        $this->yonghuCur =1;
		$this->display("admin/yonghu.html");
	}
	
	// ��������
	public function link(){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		//$pros = spClass("m_pro");
                $this->linkCur =1;
		$this->display("admin/link.html");
	}
	
	// ������
	public function ad(){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		//$pros = spClass("m_pro");
                $this->adCur =1;
		$this->display("admin/ad.html");
	}
	
	// ��̨��Ʒ�ɼ�ҳ
	public function proget(){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		
		//import('public-data.php');
		$pros = spClass('m_pro');		
		$where = 'st<=curdate() and et>=curdate() and ischeck=1';
		$noyj = $this->spArgs('noyj');
		$date = $this->spArgs('date');
		if($noyj=='yes'){
			if($date=='today')
				$items = $pros->findAll($where.'  and postdt>=curdate() and commission_rate=-1');
			elseif($date=='all')
				$items = $pros->findAll($where.' and commission_rate=-1');
		}
		$this->items = $items;
		$this->itemCounts = count($items);
                
		foreach($items as $k=>$v){
			$iidarr[] = array(iid=>$v['iid']);
		}	
				
		$this->iidarr = $iidarr;
		$this->yjdate = $date;			
		//$timestamp=time()."000";
		//$app_key = '12636285';
		//$secret = '63e664fafc1f3f03a7b8ad566c42819d';
		//$app_key = '21511111';
		//$secret = '4b7df3004e66b43f4632e2a85fe3f308';
		//$message = $secret.'app_key'.$app_key.'timestamp'.$timestamp.$secret;
		//$mysign=strtoupper(hash_hmac("md5",$message,$secret));
		//setcookie("timestamp",$timestamp);
		//setcookie("sign",$mysign);
		$this->progetCur = 1;
		$this->display("admin/proget.html");
	}
	
	public function updateyj(){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		set_time_limit(0);
		// �ɼ�������
		ini_set('memory_limit', '64M'); // �ڴ泬��
		ini_set('pcre.backtrack_limit', 999999999); // ���ݳ���
		ini_set('pcre.recursion_limit', 99999); // ��Դ�������
		// end - �ɼ�������
        $date = $this->spArgs('date');        
		$pros = spClass('m_pro');
		$where = 'st<=curdate() and et>=curdate() and ischeck=1';
		if($date=='today')
			$items = $pros->findAll($where.' and postdt>=curdate() and commission_rate=-1');
		elseif($date=='all')
			$items = $pros->findAll($where.' and commission_rate=-1');
		
		foreach($items as $k=>$v){
			$yj = getCommissionRate($v['iid']);
			$itemTemp = array('commission_rate'=>$yj);
			$pros->update(array('iid'=>$v['iid']),$itemTemp);
		}	 
		$this->display("admin/uzcaiji.html");
		header("Location:/proget.html");
	}
	
	// �ɼ�
	public function uzcaijiapi(){
		set_time_limit(0);
		// �ɼ�������
		ini_set('memory_limit', '64M'); // �ڴ泬��
		ini_set('pcre.backtrack_limit', 999999999); // ���ݳ���
		ini_set('pcre.recursion_limit', 99999); // ��Դ�������
		// end - �ɼ�������
		
		import('uzcaiji-class.php');
		$xiaiCaiji = spClass('UzCaiji');
			
		$type = $this->spArgs('type');
		$actType = $this->website[$type]['actType'];
		
		
		// �ɼ��ӿ����
		if($actType){
			if($actType == 15){
				$caijiarr = array();
				for($page=1;$page<=1;$page++){
					$xiaiCaiji->Caiji($type,$page);
					$caijiarr = array_merge($caijiarr,$xiaiCaiji->getitems());
				}
				echo json_encode($caijiarr);
			}elseif($actType == 4 || $actType == 11){ 
				//$pages = $xiaiCaiji->Caiji($type,'',3);
				//$pages = @ceil($pages/45);
				
				$caijiarr = array();
				for($page=1;$page<=2;$page++){
					$xiaiCaiji->Caiji($type,$page);
					$caijiarr = array_merge($caijiarr,$xiaiCaiji->getitems());
				}
				echo json_encode($caijiarr);
			}elseif($actType == 10 || $actType == 16){ // ��ɱͨ�ɼ�5ҳ
				$caijiarr = array();
				for($page=1;$page<=5;$page++){
					$xiaiCaiji->Caiji($type,$page);
					$caijiarr = array_merge($caijiarr,$xiaiCaiji->getitems());
				}
				echo json_encode($caijiarr);
			}elseif($actType == 20){
				$dateTemp = date("Y-m-d",time()-3*24*60*60);
				$pros = spClass("m_pro");
				$data = $pros->findAll('act_from=20 and postdt>='.$dateTemp);
//				var_dump($data);
				foreach($data as $k =>$v){
					$all[] = array('iid'=>$v['iid'],'nprice'=>$v['nprice'],'pic'=>$v['pic']);
				}
				$jsonData['all'] = $all;
				echo json_encode($jsonData);
			}else{
				//echo $actType;
				$xiaiCaiji->Caiji($type,'',2);
			}
		}else{
			echo 'û��ѡ��ɼ�վ��!';
		}
			
		//$this->website = $website;
		$this->display("admin/uzcaiji.html");
	}
	
	public function getitems($items,$actType){
		import('tbapi.php');
		$pros = spClass("m_pro");
		$catmaps = spClass("m_catmap");
		foreach($items as $k=>$iv){
			foreach($iv as $v){
				//echo $v['iid'].'<br/>';
				$item = getItemDetail($v['iid']);
//                                var_dump($item);
                                if($item<0){
                                    echo $v['iid'].' ��ȡ��Ϣʧ��!<br/>';
                                }else{
//                                    echo $v['iid'].' ��ȡ��ϢCG!<br/>';
                                    // �ּ�  && ͼƬ
                                    $item['nprice'] = $v['nprice'];
                                    if($v['pic'])
                                            $item['pic'] = $v['pic'];
                                    // end - �ּ�  && ͼƬ

                                    // �ݹ�ȡ���Ա������ڵ�
                                    if($GLOBALS['G_SP']['autocat']){
                                        $pcid = getPcidNew($item['cid']);
                                        $pcid = $pcid['cid'];

                                        // ��ѯfstk_catmap��Ӧ��Ŀ
                                        $catMap = $catmaps->find(array('cid'=>$pcid),'','type');
                                        //var_dump($catMap);
                                        if($catMap){ //�����Ʒ��Ŀ��ӳ��
                                                $item['cat'] = (int)$catMap['type'];
                                        }else{
                                                $item['cat'] = 42;
                                        }
                                        // end - ��ѯfstk_catmap��Ӧ��Ŀ
                                    }
                                    // end - �ݹ�ȡ���Ա������ڵ�



                                    // �ַ�ת��
                                    $item['title'] = iconv('utf-8','gb2312',$item['title']);
                                    $item['title'] = preg_replace('/��.+?��/i','',$item['title']);
                                    $item['nick'] = iconv('utf-8','gb2312',$item['nick']);
                                    // end - �ַ�ת��

                                    if($actType)
                                            $item['act_from'] = $actType;
                                    else
                                            $item['act_from'] = 1;
                                    $item['last_modify'] = date("Y-m-d H:i:s");
                                    $item['volume'] = getvolume($v['iid'],$item['shopshow']);

                                    //var_dump($item);
                                    if(!$pros->find(array('iid'=>$v['iid']))){ //û�ҵ�
                                            $item['postdt'] = date("Y-m-d H:i:s");

                                            if(!$pros->create($item)){
                                                    echo $v['iid'].' ���ʧ��,���ݿ����ʧ��!<br/>';
                                            }else{
                                                    //$this->upyjscript($v['iid'],$actType);
                                                    //$this->updateyjPhp($v['iid']);
                                                    echo $v['iid'].' ��ӳɹ�!<br/>';
                                            }
                                    }else{
                                            unset($item['act_from']);
                                            unset($item['rank']);
                                            unset($item['cat']);
                                            //$item['et'] = date("Y-m-d",86400*7+time());
                                            //$itemPostdt = $pros->find(array('iid'=>$v['iid']));
                                            //$item['postdt'] = $itemPostdt['postdt'];

                                            if(!$pros->update(array('iid'=>$v['iid']),$item)){
                                                    echo $v['iid'].' ����ʧ��,���ݿ����ʧ��!<br/>';
                                            }else{
                                                    //$this->upyjscript($v['iid'],$actType);
                                                    //$this->updateyjPhp($v['iid']);
                                                    echo $v['iid'].' ���³ɹ�!<br/>';
                                            }

                                    }
				}	
				
			}
		}
		$this->display("admin/uzcaiji.html");
	}
	
	
	// һ���ɼ�
	public function yjuzcaiji(){
//		exec("uzcaiji.sh",$output);
//		var_dump($output);
	}
	
	// �ɼ�
	public function uzcaiji($type=null){
		
//		if(!$_SESSION['admin'])
//			header("Location:/login.html");
		
		set_time_limit(0);
              
                
		// �ɼ�������
		ini_set('memory_limit', '64M'); // �ڴ泬��
		ini_set('pcre.backtrack_limit', 999999999); // ���ݳ���
		ini_set('pcre.recursion_limit', 99999); // ��Դ�������
		// end - �ɼ�������
		
		import('uzcaiji-class.php');
		$xiaiCaiji = spClass('UzCaiji');
				
		//�ɼ�
		if(!$type)
			$type = $this->spArgs('type');
		
			
		$actType = $this->website[$type]['actType'];
		$pros = spClass("m_pro");
		/* $catmaps = spClass("m_catmap");
		import('tbapi.php'); */
		//echo $caiji.'<br/>';
		if($actType && $GLOBALS['G_SP']['autocat']){
			if($actType == 15){ // ׬��
                                 
                            for($page=1;$page<=1;$page++){
                               //$xiaiCaiji->Caiji($type,$page);
                               $xiaiCaiji->Caiji($type);
                               $items = $xiaiCaiji->getitems();
                               $this->getitems($items, $actType);
                           }
                                
			}elseif($actType == 11 || $actType == 4){ // ��Ƥ  && �ſ���  
				//$pages = $xiaiCaiji->Caiji($type,'',3);
				//$pages = @ceil($pages/45);
				$pages = 2;
				for($page=1;$page<=$pages;$page++){
					$xiaiCaiji->Caiji($type,$page);
					$items = $xiaiCaiji->getitems();
					//var_dump($items);
					$this->getitems($items, $actType);
				}
			}elseif($actType == 10 || $actType == 16){ // ��ɱͨ,�ؼ۷����ɼ�5ҳ
				for($page=1;$page<=5;$page++){
					$xiaiCaiji->Caiji($type,$page);
					$items = $xiaiCaiji->getitems();
					$this->getitems($items, $actType);
				}
			}elseif($actType == 20){
				$dateTemp = date("Y-m-d",time()-3*24*60*60);
				$pros->runSql('update fstk_pro set postdt=curdate() where act_from=20 and postdt>='.$dateTemp);
			}else{
				$xiaiCaiji->Caiji($type);
				$items = $xiaiCaiji->getitems();
				//var_dump($items);
				$this->getitems($items, $actType);
			}
		}else{
                    if(!$GLOBALS['G_SP']['autocat'] && $actType){
                        if($actType == 20){
                            $dateTemp = date("Y-m-d",time()-3*24*60*60);
                            $pros->runSql('update fstk_pro set postdt=curdate() where act_from=20 and postdt>='.$dateTemp);
			}
                        $xiaiCaiji->Caiji($type);
                        $items = $xiaiCaiji->getitems();
                        //var_dump($items);
                        $this->getitems($items, $actType);
                    }else
			echo 'û��ѡ��ɼ�վ��!';
		}
	
		//$this->website = $website;
		$this->display("admin/uzcaiji.html");
	}
	public function postDataToUzPhp($item,$uz){
//		var_dump($item);
		if($uz=='admin'){
			$url = 'http://yinxiang.uz.taobao.com/d/getdata';
		}elseif($uz=='cong'){
			$url = 'http://zhekouba.uz.taobao.com/d/getdata';
		}else{
			$url = 'http://'.$uz.'.uz.taobao.com/d/getdata';
		}
                if(!$item[del])
                    $contents = "pic=$item[pic]&&cat=$item[cat]&&iid=$item[iid]&&oprice=$item[oprice]&&nprice=$item[nprice]&&st=$item[st]&&et=$item[et]&&act_from=$item[classification]&&rank=$item[rank]&&title=$item[title]&&link=$item[link]&&slink=$item[slink]&&volume=$item[volume]&&postdt=$item[postdt]&&xujf=$item[xujf]&&remark=$item[remark]&&type=$item[type]&&content=$item[content]&&zk=$item[zk]&&carriage=$item[carriage]&&commission_rate=$item[commission_rate]&&ischeck=$item[ischeck]&&last_modify=$item[last_modify]&&ww=$item[ww]&&shopshow=$item[shopshow]&&shopv=$item[shopv]";
		else
                    $contents = "iid=$item[iid]&&del=$item[del]";
                $opts = array(
			'http'=>array(
					'method'=>"POST",
					'content'=>$contents,
					'timeout'=>900,
			));
//		echo $contents.'<br />';
		$context = stream_context_create($opts);
		
		$html = @file_get_contents($url, false, $context);
		
		echo $html;
	}
	
	public function postDataToUzScripts($q,$uz){
		echo '<script language="javascript">';
		echo '$(function(){';
		
		if($uz=='126789'){
			echo "$.post('http://".$uz.".uz.taobao.com/d/getdata?import=".$uz."',
			  {
			    'ak':'".$q."'
			  },
			  function(data,status){
			    ;
			  });";
		}elseif($uz=='admin'){
			echo "$.post('http://yinxiang.uz.taobao.com/d/getdata?import=".$uz."',
			  {
			    'ak':'".$q."'
			  },
			  function(data,status){
			    ;
			  });";
		}elseif($uz=='cong'){
			echo "$.post('http://zhekouba.uz.taobao.com/d/getdata?import=".$uz."',
			  {
			    'q':'".$q."'
			  },
			  function(data,status){
			    ;
			  });";
		}else{
			echo "$.post('http://".$uz.".uz.taobao.com/d/getdata?import=".$uz."',
			  {
			    'q':'".$q."'
			  },
			  function(data,status){
			    ;
			  });";
		}
		
		
		echo '});';	
		echo '</script>';
	}
	public function postDataToUz($mode='php'){
		set_time_limit(0);
		if(!$_SESSION['admin'])
			if(!$_SESSION['iscaijiuser'])
				header("Location:/login.html");
		header("Content-Type: text/html; charset=gbk");
		
		$baseSql = 'st<=curdate() and et>=curdate() and ischeck=1';
		$noAd = 'type!=87';
		$baseSql .= ' and '.$noAd; // �����Ԥ�����
		
		$control = spClass('m_control');
		$caiji_control = $control->find(array('type'=>1));
		if($caiji_control['isuse'])
			exit();
		else
			$control->update(array('type'=>1),array('isuse'=>1));
		
		
		//var_dump($control->find());
		$pros = spClass('m_pro');
		
		// һ��������������
		foreach($this->website as $k=>$v){
			if($k!='none'){
				if(COMISSIONRATESORT)
					$where = 'act_from='.$v['actType'].' and '.$baseSql.' and postdt>=curdate() and commission_rate>=5';
				else
					$where = 'act_from='.$v['actType'].' and '.$baseSql.' and postdt>=curdate()';
			}
			$items_zu['actfrom'.$v['actType']] = $pros->findAll($where,'commission_rate asc');//Ӷ���->����ϣ������ʱ��ͷ���������postdtʱ��Ϊnow(),
		}
		//var_dump($items_zu);
			
		foreach($items_zu as $k=>$iv){
			foreach($iv as $k=>$v){
				$itemsTemp[] = $v;
			}
		}
		
//		var_dump($itemsTemp);
		$itemsReal = $itemsTemp;
		
		if(count($itemsReal)>1000)
			$item_zu_tmp = array_chunk($itemsReal,1000);
		else 
			$item_zu_tmp[0] = $itemsReal;
		
		//var_dump($item_zu_tmp);
		//$items = $pros->findAll($where); //,'commission_rate asc'
		foreach($item_zu_tmp as $k=>$iv){
			foreach($iv as $k=>$v){
				if(is_numeric($v['iid'])){
					if(empty($v['phone']))
						$v['phone'] = '123456789';
					$v['rank'] = 500;
					if(!$v['shopshow'])
						$v['shopshow']=1;
					if(!$v['shopv'])
						$v['shopv']=0;
					$v['title'] = preg_replace('/��.+?��/i','',$v['title']);
					if($_SESSION['iscaijiuser']=='zhe800w' || $_SESSION['iscaijiuser']=='55128' || $_SESSION['iscaijiuser']=='haowo' || $_SESSION['iscaijiuser']=='xinxin' || $_SESSION['iscaijiuser']=='cong' || $_SESSION['iscaijiuser']=='lijie' || $_SESSION['iscaijiuser']=='x0123' || $_SESSION['iscaijiuser']=='9kuaigou' || $_SESSION['iscaijiuser']=='xx0123' || $_SESSION['iscaijiuser']=='ugou'){
						if($v['nprice']<10)
							$v['type'] = 2;
						elseif($v['nprice']>10 && $v['nprice']<20)
							$v['type'] = 3;
						elseif($v['nprice']>20 && $v['nprice']<30)
							$v['type'] = 5;
						elseif($v['nprice']>30 && $v['nprice']<40)
							$v['type'] = 7;
						elseif($v['nprice']>=40)
							$v['type'] = 4;
					}elseif($_SESSION['iscaijiuser']=='shiyonglianmeng'){
						if($v['nprice']<10)
							$v['type'] = 2;
						elseif($v['nprice']>10 && $v['nprice']<20)
							$v['type'] = 3;
						elseif(($v['nprice']/$v['oprice'])*10<=3)
							$v['type'] = 6;
						else
							$v['type'] = 8;

					}
					if($_SESSION['iscaijiuser']=='ifengqiang' || $_SESSION['iscaijiuser']=='9kuaigou'){
						if($v['nprice']<10)
							$v['act_from'] = 2;
						else
							$v['act_from'] = 3;
					}else{
						$v['act_from'] = 1;
					}
					if($_SESSION['iscaijiuser']=='cong'){
						$v['act_from'] = 3;
					}
					if($_SESSION['iscaijiuser']=='9kuaigou'){
						if($v['cat']==27)
							$v['cat']=22;
						$sqlout_sec .= $sqlout_fir." ('".$v['title']."','".$v['oprice']."','".$v['nprice']."','".$v['pic']."','".$v['st']."','".$v['et']."','".$v['type']."','".$v['cat']."','".$v['ischeck']."','http://item.taobao.com/item.htm?id=".$v['iid']."','".$v['rank']."','".$v['num']."','".$v['slink']."','".$v['ww']."','".$v['snum']."','".$v['xujf']."','".date("Y-m-d H:i:s")."','".$v['zk']."','".$v['iid']."','".$v['volume']."','".$v['content']."','".$v['remark']."','".$v['nick']."','".$v['reason']."','".$v['carriage']."','".$v['commission_rate']."','".date("Y-m-d H:i:s")."','".$v['click_num']."','".$v['phone']."','".$v['act_from']."','".$v['shopshow']."','".$v['shopv']."')  ON DUPLICATE KEY UPDATE last_modify=now(),cat=".$v['cat'].",et='".$v['et']."',commission_rate=".$v['commission_rate'].";";
					}
					else
						$sqlout_sec .= $sqlout_fir." ('".$v['title']."','".$v['oprice']."','".$v['nprice']."','".$v['pic']."','".$v['st']."','".$v['et']."','".$v['type']."','".$v['cat']."','".$v['ischeck']."','http://item.taobao.com/item.htm?id=".$v['iid']."','".$v['rank']."','".$v['num']."','".$v['slink']."','".$v['ww']."','".$v['snum']."','".$v['xujf']."','".date("Y-m-d H:i:s")."','".$v['zk']."','".$v['iid']."','".$v['volume']."','".$v['content']."','".$v['remark']."','".$v['nick']."','".$v['reason']."','".$v['carriage']."','".$v['commission_rate']."','".date("Y-m-d H:i:s")."','".$v['click_num']."','".$v['phone']."','".$v['act_from']."','".$v['shopshow']."','".$v['shopv']."')  ON DUPLICATE KEY UPDATE last_modify=now(),cat=".$v['cat'].",et='".$v['et']."',commission_rate=".$v['commission_rate'].";";
					if($_SESSION['iscaijiuser']=='yuansu')
						$v['pic'] = preg_replace('/_310x310.jpg/i','',$v['pic']);

					if($_SESSION['iscaijiuser']){
						if($mode=='php')
							$this->postDataToUzPhp($v,$_SESSION['iscaijiuser']);
						else 
							$this->postDataToUzScripts($sqlout_sec,$_SESSION['iscaijiuser']);
					}else{
						if($mode=='php')
							$this->postDataToUzPhp($v,'admin');
						else 
							$this->postDataToUzScripts($sqlout_sec,'admin');
					}
					$sqlout_sec = null;
				}

			}
			echo date("H:i:s").'��ͣ';
			sleep(210);
			echo date("H:i:s").'����';
		}
		$control->update(array('type'=>1),array('isuse'=>0));
	}
	
	public function upyjscript($iid,$actType){
		echo '<script language="javascript">';
		echo "TOP.api('rest', 'get',{
		method:'taobao.taobaoke.widget.items.convert',
		fields:'commission_rate,promotion_price,volume',
		num_iids:$iid,
		
		},function(resp){
		if(resp.error_response){
				alert('taobao.taobaoke.widget.items.convert�ӿڻ�ȡ����ϢƷʧ��!'+resp.error_response.msg);
				//console.log($iid);
				return false;
		}else{
				//console.log($iid);
				var item = resp.taobaoke_items.taobaoke_item;
				//console.log(item[0].commission_rate / 100);
				var zk = item[0].commission_rate / 100;
				//console.log(zk);
				$.ajax({
				type:'get',
				url:'/updateyjonce.html',
				data:'zk='+zk+'&actType=$actType&iid=$iid',
				dataType:'text',
		});
		}
		})";
		
		echo '</script>';
	}
	
	// ����Ӷ����PHP��
	public function updateyjPhp($iid,$cookie=''){
                $pros = spClass('m_pro');
		$yj = $this->getCommissionRate($iid);
		$item['commission_rate'] = $yj;
		$pros->update(array('iid'=>$iid),$item);
			
	}
	public function updatevolume(){
		$pros = spClass('m_pro');
		$where = 'st<=curdate() and et>=curdate() and ischeck=1 and volume=0 or volume=200';
		$items = $pros->findAll($where);
		foreach($items as $k=>$v){
			$volume = getvolume($v['iid'],$v['shopshow']);
			if($volume>=0){
				$itemTemp = array('volume'=>$volume);
				if($pros->update(array('iid'=>$v['iid']),$itemTemp))
					echo '���³ɹ�.<br />';
				else
					echo '����ʧ��.<br />';
			}else{
				echo '��ȡʧ��.<br />';
			}
			
		}
	}
	// ����Ӷ����
	public function updateyjonce(){
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		$pros = spClass('m_pro');
		$yj = $this->spArgs('zk');
		$iid = $this->spArgs('iid');
		$item['commission_rate'] = $yj;
		//echo $iid.' Ӷ��'.$yj.'<br/>';
		if($iid && $yj){
			$pros->update(array('iid'=>$iid),$item);
		}
		$this->display("admin/uzcaiji.html");
	}
	// END ����Ӷ����
	// ���ݵ���
	public function dbselect(){
		if(!$_SESSION['admin'])
			if(!$_SESSION['iscaijiuser'])
				header("Location:/login.html");
			
		$baseSql = 'st<=curdate() and et>=curdate() and ischeck=1';
		$noAd = 'type!=87';
		$baseSql .= ' and '.$noAd; // �����Ԥ�����
		
		
		$pros = spClass('m_pro');
		
		// һ���������
		if(SETAJAXTOUZ){
			$control = spClass("m_control");
			$caiji_control = $control->find(array('type'=>1));
			$this->caijiisuse = $caiji_control['isuse'];
		}else{
			$this->caijiisuse = 1;
		}
		
		// SQL�ļ��������
		if(SETFILETOUZ){
			$control = spClass("m_control");
			$getsql_control = $control->find(array('type'=>2));
			$this->getsqlisuse = $getsql_control['isuse'];
		}
		
		// ����ɼ�������
//		if(COMISSIONRATESORT){
//			$where = $baseSql.' and postdt>=curdate() and commission_rate>=5';
//		}
//		else{    
//			$where = $baseSql.' and postdt>=curdate()';
//		}
		
		
		// ����ƽ̨�����ݵ�����
		$type = $this->spArgs('type');
		$actfrom = $this->website[$this->spArgs('type')]['actType'];
		if($actfrom){ // ÿ��ƽ̨ѡ��			
			$page = $this->spArgs('page',1);
			if($actfrom==2 || $actfrom==6 || $actfrom==9)// ��Ա��,VIP�ػݣ�VIP���Żݲ�����Ӷ��
				$where = 'act_from='.$actfrom.' and '.$baseSql.' and postdt>=curdate()';
			else{
				if(COMISSIONRATESORT)
					$where = 'act_from='.$actfrom.' and '.$baseSql.' and postdt>=curdate()  and commission_rate>=5';
				else
					$where = 'act_from='.$actfrom.' and '.$baseSql.' and postdt>=curdate()';
			}
		}else{ // ȫ��
			$page = $this->spArgs('page',1);
			if(COMISSIONRATESORT)
				$where = $baseSql.' and postdt>=curdate() and commission_rate>=5';
			else
				$where = $baseSql.' and postdt>=curdate()';
		}
		
		$itemsTemp = $pros->spPager($page,50)->findAll($where);
		
		
		// �ɼ��û�����Ϣ
		if($_SESSION['iscaijiuser']){
			$this->iscaijiuser = $_SESSION['iscaijiuser'];
			$this->username = $this->caijiusers[$_SESSION['iscaijiuser']]['nick'];
		}
		
		$this->type = $type;
		$this->actfrom = $actfrom;
		$this->pager = $pros->spPager()->getPager();
        $this->todayoutsql = $filename = $_SESSION['iscaijiuser'].'-'.date("Y-m-d").'.sql';        
        $this->dbselectCur = 1;
		$this->display("admin/dbselect.html");
	}
	
	public function sqloutone(){
		$iid = $this->spArgs('iid');
		$pros = spClass('m_pro');
		$type = $this->spArgs('type');
		$rank = $this->spArgs('rank');
		$v = $pros->find(array('iid'=>$iid));
		if($type){
			$v[type] = $type;
			$v[rank] = $rank;
		}
		//var_dump($item);
		$sqlout_fir = "INSERT INTO `fstk_pro` (`title`, `oprice`, `nprice`, `pic`, `st`, `et`, `type`, `cat`, `ischeck`, `link`, `rank`, `num`, `slink`, `ww`, `snum`, `xujf`, `postdt`, `zk`, `iid`, `volume`, `content`, `remark`, `nick`, `reason`, `carriage`, `commission_rate`, `last_modify`, `click_num`, `phone`, `act_from`,`shopshow`,`shopv`) VALUES ";
		$sqlout_sec .= $sqlout_fir.' ("'.$v['title'].'","'.$v['oprice'].'","'.$v['nprice'].'","'.$v['pic'].'","'.$v['st'].'","'.$v['et'].'","'.$v['type'].'","'.$v['cat'].'","'.$v['ischeck'].'","http://item.taobao.com/item.htm?id='.$v['iid'].'","'.$v['rank'].'","'.$v['num'].'","'.$v['slink'].'","'.$v['ww'].'","'.$v['snum'].'","'.$v['xujf'].'",now(),"'.$v['zk'].'","'.$v['iid'].'","'.$v['volume'].'","'.$v['content'].'","'.$v['remark'].'","'.$v['nick'].'","'.$v['reason'].'","'.$v['carriage'].'","'.$v['commission_rate'].'",now(),"'.$v['click_num'].'","'.$v['phone'].'","'.$v['act_from'].'","'.$v['shopshow'].'","'.$v['shopv'].'")  ON DUPLICATE KEY UPDATE last_modify=now(),cat='.$v['cat'].',pic='.$v['pic'].',et="'.$v['et'].'",commission_rate='.$v['commission_rate'].';';
		echo $sqlout_sec;
		
	}
	
	//�����������ݴ洢Ϊsql�ļ�
	public function savesqltouz(){
		if(!$_SESSION['admin'])
			if(!$_SESSION['iscaijiuser'])
				header("Location:/login.html");
		$filename = $_SESSION['iscaijiuser'].date("Y-m-d");
		$baseSql = 'st<=curdate() and et>=curdate() and ischeck=1';
		$noAd = 'type!=87';
		$baseSql .= ' and '.$noAd; // �����Ԥ�����
	
		$pros = spClass('m_pro');
		
		// ��������
		$control = spClass('m_control');
		$getsql_control = $control->find(array('type'=>2));
		if($getsql_control['isuse'])
			exit();
		else
			$control->update(array('type'=>2),array('isuse'=>1));
		
		// һ��������������
		foreach($this->website as $k=>$v){
			if($k!='none'){
				if(COMISSIONRATESORT)
					$where = 'act_from='.$v['actType'].' and '.$baseSql.' and postdt>=curdate() and commission_rate>=5';
				else
					$where = 'act_from='.$v['actType'].' and '.$baseSql.' and postdt>=curdate()';
			}
			$items_zu['actfrom'.$v['actType']] = $pros->findAll($where,'commission_rate asc');//Ӷ���->����ϣ������ʱ��ͷ���������postdtʱ��Ϊnow(),
			//echo $pros->dumpSql().'<br/>';
		}
		//var_dump($items_zu);
			
		foreach($items_zu as $k=>$iv){
			foreach($iv as $k=>$v){
				$itemsTemp[] = $v;
			}
		}
		
		$itemsReal = $itemsTemp;
		
		if(count($itemsReal)>2500)
			$item_zu_tmp = array_chunk($itemsReal,2500);
		else 
			$item_zu_tmp[0] = $itemsReal;
		
		header("Content-Type:text/html;charset=UTF-8");
		
		$sqlout_fir = 'INSERT INTO fstk_pro(title,oprice,nprice,pic,st,et,type,cat,ischeck,link,rank,num,slink,ww,snum,xujf,postdt,zk,iid,volume,content,remark,nick,reason,carriage,commission_rate,last_modify,click_num,phone,act_from,shopshow,shopv) VALUES ';
		
		//����ļ���
		$datalist=list_dir('./tmp/sqlout/');
		foreach($datalist as $k=>$val){   
			unlink($val);
		}   
			
		foreach($item_zu_tmp as $k=>$iv){
			$i += 1;
			$file = fopen('./tmp/sqlout/'.$filename.'-part'.$i.'.sql',"w+");
			fclose($file);
			foreach($iv as $k=>$v){
				if(is_numeric($v['iid'])){
					if(empty($v['phone']))
						$v['phone'] = '123456789';
					$v['rank'] = 500;
					if(!$v['shopshow'])
						$v['shopshow']=1;
					if(!$v['shopv'])
						$v['shopv']=0;
					$v['title'] = preg_replace('/��.+?��/i','',$v['title']);
					if($_SESSION['iscaijiuser']=='jumei' || $_SESSION['iscaijiuser']=='tiangou' || $_SESSION['iscaijiuser']=='haowo' || $_SESSION['iscaijiuser']=='xinxin' || $_SESSION['iscaijiuser']=='cong' || $_SESSION['iscaijiuser']=='lijie' || $_SESSION['iscaijiuser']=='x0123' || $_SESSION['iscaijiuser']=='9kuaigou' || $_SESSION['iscaijiuser']=='xx0123' || $_SESSION['iscaijiuser']=='ugou'){
						if($v['nprice']<10)
							$v['type'] = 2;
						elseif($v['nprice']>10 && $v['nprice']<20)
							$v['type'] = 3;
						elseif($v['nprice']>20 && $v['nprice']<30)
							$v['type'] = 5;
						elseif($v['nprice']>30 && $v['nprice']<40)
							$v['type'] = 7;
						elseif($v['nprice']>=40)
							$v['type'] = 4;
					}elseif($_SESSION['iscaijiuser']=='shiyonglianmeng'){
						if($v['nprice']<10)
							$v['type'] = 2;
						elseif($v['nprice']>10 && $v['nprice']<20)
							$v['type'] = 3;
						elseif(($v['nprice']/$v['oprice'])*10<=3)
							$v['type'] = 6;
						else
							$v['type'] = 8;

					}
					if($_SESSION['iscaijiuser']=='ifengqiang' || $_SESSION['iscaijiuser']=='9kuaigou'){
						if($v['nprice']<10)
							$v['act_from'] = 2;
						else
							$v['act_from'] = 3;
					}else{
						$v['act_from'] = 1;
					}
					if($_SESSION['iscaijiuser']=='cong'){
						$v['act_from'] = 3;
					}
					$sqlout_sec = $sqlout_fir.' ("'.$v["title"].'","'.$v["oprice"].'","'.$v["nprice"].'","'.$v["pic"].'","'.$v["st"].'","'.$v["et"].'","'.$v["type"].'","'.$v["cat"].'","'.$v["ischeck"].'","http://item.taobao.com/item.htm?id='.$v["iid"].'","'.$v["rank"].'","'.$v["num"].'","'.$v["slink"].'","'.$v["ww"].'","'.$v["snum"].'","'.$v["xujf"].'","'.date("Y-m-d H:i:s").'","'.$v["zk"].'","'.$v["iid"].'","'.$v["volume"].'","'.$v["content"].'","'.$v["remark"].'","'.$v["nick"].'","'.$v["reason"].'","'.$v["carriage"].'","'.$v["commission_rate"].'","'.date("Y-m-d H:i:s").'","'.$v["click_num"].'","'.$v["phone"].'","'.$v["act_from"].'","'.$v["shopshow"].'","'.$v["shopv"].'")  ON DUPLICATE KEY UPDATE last_modify=now(),cat='.$v["cat"].',et="'.$v["et"].'",commission_rate="'.$v["commission_rate"].'";\n';
					//echo $sqlout_sec;
					$file = fopen('./tmp/sqlout/'.$filename.'-part'.$i.'.sql',"a+");
					if(!$file)
						echo '�ļ���ʧ��';
					//echo $sqlout_sec.'<br />';
					fwrite($file,iconv('gbk','utf-8',$sqlout_sec));
					$sqlout_sec = null;
				}
			}
			fclose($file);
		}
		
		//��ȡ�б� 
		$datalist=list_dir('./tmp/sqlout/');
		//var_dump($datalist);
		$zipfilename = "./tmp/".$filename.".zip"; //�������ɵ��ļ�������·����   
		unlink($zipfilename);
		if(!file_exists($zipfilename)){   
			//���������ļ�   
			$zip = new ZipArchive();//ʹ�ñ��࣬linux�迪��zlib��windows��ȡ��php_zip.dllǰ��ע��   
			if ($zip->open($zipfilename, ZIPARCHIVE::CREATE)!==TRUE) {   
				exit('�޷����ļ��������ļ�����ʧ��');
			}   
			foreach($datalist as $k=>$val){   
				if(file_exists($val)){   
					$zip->addFile($val,basename($val));//�ڶ��������Ƿ���ѹ�����е��ļ����ƣ�����ļ����ܻ����ظ�������Ҫע��һ��   
				}   
			}   
			$zip->close();//�ر�   
		}   
		if(!file_exists($zipfilename)){   
			exit("�޷��ҵ��ļ�"); //��ʹ���������п���ʧ�ܡ�������   
		}   
		header("Cache-Control: public"); 
		header("Content-Description: File Transfer"); 
		header('Content-disposition: attachment; filename='.basename($zipfilename)); //�ļ���   
		header("Content-Type: application/zip"); //zip��ʽ��   
//		header('Content-disposition: attachment; filename='.basename('./tmp/sqlout/'.$filename.'-part1.sql')); //�ļ���   
//		header("Content-Type: application/text"); //text��ʽ�� 
		header("Content-Transfer-Encoding: binary"); //��������������Ƕ������ļ�    
		header('Content-Length: '. filesize($zipfilename)); //������������ļ���С   
//		header('Content-Length: '. filesize('./tmp/sqlout/'.$filename.'-part1.sql')); //������������ļ���С   
		@readfile($zipfilename); 
//		@readfile('./tmp/sqlout/'.$filename.'-part1.sql');
		$control->update(array('type'=>2),array('isuse'=>0));
	}
	
	// ���ݵ���
	public function sqlout(){
		
		if(!$_SESSION['admin'])
			if(!$_SESSION['iscaijiuser'])
				header("Location:/login.html");
		
		$actfrom =	$this->website[$this->spArgs('type')]['actType'];
		$pros = spClass('m_pro');
		//echo $actfrom.'best<br/>';
		if($actfrom){		
			if($actfrom==2 || $actfrom==6 || $actfrom==9)// ��Ա��,VIP�ػݣ�VIP���Żݲ�����Ӷ��
				$where = 'act_from='.$actfrom.' and st<=curdate() and et>=curdate() and ischeck=1 and postdt>=curdate()';
			else{
				 if(COMISSIONRATESORT)
					 $where = 'act_from='.$actfrom.' and st<=curdate() and et>=curdate() and ischeck=1 and postdt>=curdate() and commission_rate>=5';
				 else
					 $where = 'act_from='.$actfrom.' and st<=curdate() and et>=curdate() and ischeck=1 and postdt>=curdate()';
			}
                            
		}else{	
			if(COMISSIONRATESORT)
				$where = 'st<=curdate() and et>=curdate() and ischeck=1 and postdt>=curdate() and commission_rate>=5';  
			else
				$where = 'st<=curdate() and et>=curdate() and ischeck=1 and postdt>=curdate()';
		}
		$page = $this->spArgs('page',1);
		if($_SESSION['iscaijiuser']=='yuansu')// ������ӿ�
			$sqlout_fir = "INSERT INTO `items` (`iid`,`title`,`picurl`,`itemurl`,`price`,`prom`,`nick`,`categoryid`,`partid`,`status`,`top`,`gg`,`report`,`freeshipping`,`stock`,`sorts`,`starttime`,`endtime`) VALUES ";
		else // fstk_���ݿ�����
			$sqlout_fir = "INSERT INTO `fstk_pro` (`title`, `oprice`, `nprice`, `pic`, `st`, `et`, `type`, `cat`, `ischeck`, `link`, `rank`, `num`, `slink`, `ww`, `snum`, `xujf`, `postdt`, `zk`, `iid`, `volume`, `content`, `remark`, `nick`, `reason`, `carriage`, `commission_rate`, `last_modify`, `click_num`, `phone`, `act_from`,`shopshow`,`shopv`) VALUES ";
		//echo $where.'<br/>';
		if(COMISSIONRATESORT)
			$items = $pros->spPager($page,50)->findAll($where,'commission_rate asc');
		else
			 $items = $pros->spPager($page,50)->findAll($where);
		foreach($items as $k=>$v){  
			if(is_numeric($v['iid'])){
				if(empty($v['phone']))
					$v['phone'] = '123456789';
				$v['rank'] = 500;
				$v['title'] = preg_replace('/��.+?��/i','',$v['title']);
				if($_SESSION['iscaijiuser']=='jumei' || $_SESSION['iscaijiuser']=='55128' || $_SESSION['iscaijiuser']=='tiangou' || $_SESSION['iscaijiuser']=='xinxin' || $_SESSION['iscaijiuser']=='cong' || $_SESSION['iscaijiuser']=='lijie' || $_SESSION['iscaijiuser']=='x0123' || $_SESSION['iscaijiuser']=='9kuaigou' || $_SESSION['iscaijiuser']=='xx0123' || $_SESSION['iscaijiuser']=='ugou'){
					if($v['nprice']<10)
						$v['type'] = 2;
					elseif($v['nprice']>10 && $v['nprice']<20)
						$v['type'] = 3;
					elseif($v['nprice']>20 && $v['nprice']<30)
						$v['type'] = 5;
					elseif($v['nprice']>30 && $v['nprice']<40)
						$v['type'] = 7;
					elseif($v['nprice']>=40)
						$v['type'] = 4;
				}elseif($_SESSION['iscaijiuser']=='shiyonglianmeng'){
					if($v['nprice']<10)
						$v['type'] = 2;
					elseif($v['nprice']>10 && $v['nprice']<20)
						$v['type'] = 3;
					elseif(($v['nprice']/$v['oprice'])*10<=3)
						$v['type'] = 6;
					else 
						$v['type'] = 8;
					
				}
				if($_SESSION['iscaijiuser']=='ifengqiang' || $_SESSION['iscaijiuser']=='chuang' || $_SESSION['iscaijiuser']=='360tuan' || $_SESSION['iscaijiuser']=='tongqu' || $_SESSION['iscaijiuser']=='tbcsh' || $_SESSION['iscaijiuser']=='9kuaigou' || $_SESSION['iscaijiuser']=='22888' || $_SESSION['iscaijiuser']=='282828' || $_SESSION['iscaijiuser']=='tblgj' || $_SESSION['iscaijiuser']=='tbypt'){
					if($v['nprice']<10)
						$v['act_from'] = 2;
					else 
						$v['act_from'] = 3;
				}else{
					$v['act_from'] = 1;
				}
				if($_SESSION['iscaijiuser']=='cong')
					$v['act_from'] = 3;
				if($_SESSION['iscaijiuser']=='9kuaigou'){ // �ſ鹺
					if($v['cat']==27)
						$v['cat']=22;
					$sqlout_sec .= $sqlout_fir.' ("'.$v['title'].'","'.$v['oprice'].'","'.$v['nprice'].'","'.$v['pic'].'","'.$v['st'].'","'.$v['et'].'","'.$v['type'].'","'.$v['cat'].'","'.$v['ischeck'].'","http://item.taobao.com/item.htm?id='.$v['iid'].'","'.$v['rank'].'","'.$v['num'].'","'.$v['slink'].'","'.$v['ww'].'","'.$v['snum'].'","'.$v['xujf'].'",now(),"'.$v['zk'].'","'.$v['iid'].'","'.$v['volume'].'","'.$v['content'].'","'.$v['remark'].'","'.$v['nick'].'","'.$v['reason'].'","'.$v['carriage'].'","'.$v['commission_rate'].'",now(),"'.$v['click_num'].'","'.$v['phone'].'","'.$v['act_from'].'","'.$v['shopshow'].'","'.$v['shopv'].'")  ON DUPLICATE KEY UPDATE last_modify=now(),cat='.$v['cat'].',pic="'.$v['pic'].'",et="'.$v['et'].'",commission_rate='.$v['commission_rate'].';';
				}else{
					if(in_array($_SESSION['iscaijiuser'],array('admin','jumei','cong','126789','tiangou')))
						$sqlout_sec .= $sqlout_fir.' ("'.$v['title'].'","'.$v['oprice'].'","'.$v['nprice'].'","'.$v['pic'].'","'.$v['st'].'","'.$v['et'].'","'.$v['type'].'","'.$v['cat'].'","'.$v['ischeck'].'","http://item.taobao.com/item.htm?id='.$v['iid'].'","'.$v['rank'].'","'.$v['num'].'","'.$v['slink'].'","'.$v['ww'].'","'.$v['snum'].'","'.$v['xujf'].'",now(),"'.$v['zk'].'","'.$v['iid'].'","'.$v['volume'].'","'.$v['content'].'","'.$v['remark'].'","'.$v['nick'].'","'.$v['reason'].'","'.$v['carriage'].'","'.$v['commission_rate'].'",now(),"'.$v['click_num'].'","'.$v['phone'].'","'.$v['act_from'].'","'.$v['shopshow'].'","'.$v['shopv'].'")  ON DUPLICATE KEY UPDATE last_modify=now(),cat='.$v['cat'].',et="'.$v['et'].'",commission_rate='.$v['commission_rate'].';';
					else
						$sqlout_sec .= $sqlout_fir.' ("'.$v['title'].'","'.$v['oprice'].'","'.$v['nprice'].'","'.$v['pic'].'","'.$v['st'].'","'.$v['et'].'","'.$v['type'].'","'.$v['cat'].'","'.$v['ischeck'].'","http://item.taobao.com/item.htm?id='.$v['iid'].'","'.$v['rank'].'","'.$v['num'].'","'.$v['slink'].'","'.$v['ww'].'","'.$v['snum'].'","'.$v['xujf'].'",now(),"'.$v['zk'].'","'.$v['iid'].'","'.$v['volume'].'","'.$v['content'].'","'.$v['remark'].'","'.$v['nick'].'","'.$v['reason'].'","'.$v['carriage'].'","'.$v['commission_rate'].'",now(),"'.$v['click_num'].'","'.$v['phone'].'","'.$v['act_from'].'","'.$v['shopshow'].'","'.$v['shopv'].'")  ON DUPLICATE KEY UPDATE last_modify=now(),cat='.$v['cat'].',et="'.$v['et'].'",commission_rate='.$v['commission_rate'].';';

				}
				//echo $sqlout_sec;
			}
		}
		echo $sqlout_sec;
		$this->display("admin/uzcaiji.html");
	}
	
	// �Կͱ���
	public function tkreport(){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		set_time_limit(0);
		import('tbapi.php');
		//$reports = array();
		while(1){
			$i=1;
			$report = gettkreport($i++);
			if(!$report)
				break;
			else 
				var_dump($report);		
		}
		//var_dump($reports);
                $this->tkreportCur = 1;
		$this->display("admin/tkreport.html");
	}
		
}
?>
