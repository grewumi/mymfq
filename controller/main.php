<?php
class main extends spController{
	public function __construct(){
		parent::__construct();
		$this->supe_uid = $GLOBALS['G_SP']['supe_uid'];
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		//echo $agent;
                
                $this->procats = spClass("m_procat")->findAll('isshow=1','type asc');
                
		$is_pc = strpos($agent,'windows nt') ? true : false;
		$is_iphone = strpos($agent,'iphone') ? true : false;
		$is_ipad = strpos($agent,'ipad') ? true : false;
		$is_android = strpos($agent,'android') ? true : false;
//		if($is_pc){
//			;
//		}
//		if($is_iphone){
//			header("Location:http://m.yimiaofengqiang.com".$_SERVER[REQUEST_URI]);
//		}
//		if($is_ipad){
//			header("Location:http://m.yimiaofengqiang.com".$_SERVER[REQUEST_URI]);
//		}
//		if($is_android){
//			 header("Location:http://m.yimiaofengqiang.com".$_SERVER[REQUEST_URI]);
//		}
		
	}
	public function view(){
		$this->display("front/index_bak.html");
	}
	
	public function mailindex($mailindex=1){
		$this->index($mailindex);
	}
	
	public function deal(){
		$id = $this->spArgs('id');
		$pros = spClass("m_pro");
		$pro = $pros->find(array('id'=>$id));
                if(strpos($pro['link'],'item.taobao'))
                    $this->single = 1;
                $this->pro = $pro;
                $this->dujia = json_decode(file_get_contents("http://www.yimiaofengqiang.com/?jsonp=1&othersync=1"),1);
		$this->display("front/deal.html");
	}
	
	public function outitems(){
		$pros = spClass("m_pro");
		$pro = $pros->findAll('act_from=20 or type=85 or type=86 or type=87');
		$this->outitems = $pro;
		$this->display("front/outitems.html");		
	}
	
	public function index($mode=false){
		/* $to = "241776039@qq.com";
		$subject = "Test mail";
		$message = "Hello! This is a simple email message.";
		$from = "test@432gou.com";
		$headers = "From: $from";
		mail($to,$subject,$message,$headers);
		echo "Mail Sent."; */
		header("Access-Control-Allow-Origin:*");
		$jsonp = $this->spArgs('jsonp');
		// ����
		$searchKey = $this->spArgs('searchKey');
		$q = urldecode($this->spArgs('q'));
		
		// ת��url����
		if($searchKey)
			header("Location:?q=".$searchKey);
		
		// url��������
		$this->q = urlencode($q);
		if($q)
			$q = "title like '%".$q."%'";
						
		// �۸�����Url������ӦSql��ѯ��
		$sqlPrice = array(
				'1'=>'nprice<=1',
				'1_10'=>'nprice<10 and nprice>0',
				'10_20'=>'nprice<20 and nprice>10',
				'20_30'=>'nprice<30 and nprice>20',
				'30_40'=>'nprice<40 and nprice>30',
				'10_50'=>'nprice<50 and nprice>=10',
				'50_100'=>'nprice<100 and nprice>=50',
				'100_200'=>'nprice<200 and nprice>=100',
				'200_9999'=>'nprice>=200',
                                '40_9999'=>'nprice>40',    
		);
		// end - ����Key��ֵ��Ӧ������,����ȡֵ ȥ����Ʒ��������et>=curdate()
		$baseSql = 'st<=curdate() and ischeck=1 and type!=87';
		$baseSqlYu = 'st<=curdate() and ischeck=1';
		$order = 'rank asc,postdt desc';				
		
		$procat = $this->spArgs('procat');
		$page = $this->spArgs('page',1);
		$type = $this->spArgs('type');
		$price = $this->spArgs('price');
				
		$pros = spClass("m_pro");
			
		if($procat || $type || $price){
			if($procat)
				$where = $baseSql.' and cat='.$procat;
			if($type)
				$where = $baseSqlYu.' and type='.$type;
			if($price)
				$where = $baseSql.' and '.$sqlPrice[$price];
		}else{
			$where = $baseSql;
		}
		
		// ����
		if($q){
			$where = $q.' and '.$baseSql;
		}
		
                $itemsTemp = $pros->spPager($page,56)->findAll($where.' and classification=1',$order);
                $itemsC1 = $pros->findAll($where.' and classification=2',$order);//$pros->spPager($page,56)->findAll($where,$order);
		
                // ������foreach & �ı������ֵ��ʱ�����һ�����ݴ��� & ����,�������һ�������ظ�
		for($i=0;$i<count($itemsC1);$i++){
			$itemsC1[$i]['title'] = preg_replace('/��.+?��/i','',$itemsC1[$i]['title']);
			$itemsC1[$i]['title'] = preg_replace('/����׬��/i','',$itemsC1[$i]['title']);
			$itemsC1[$i]['oprice'] = number_format($itemsC1[$i]['oprice'],2);
			$temp_npriceTail = explode('.',strval(number_format($itemsC1[$i]['nprice'],2)));
			$itemsC1[$i]['nprice_tail'] = $temp_npriceTail[1];
		}
                
		// ������foreach & �ı������ֵ��ʱ�����һ�����ݴ��� & ����,�������һ�������ظ�
		for($i=0;$i<count($itemsTemp);$i++){
			$itemsTemp[$i]['title'] = preg_replace('/��.+?��/i','',$itemsTemp[$i]['title']);
			$itemsTemp[$i]['title'] = preg_replace('/����׬��/i','',$itemsTemp[$i]['title']);
			$itemsTemp[$i]['oprice'] = number_format($itemsTemp[$i]['oprice'],2);
			$temp_npriceTail = explode('.',strval(number_format($itemsTemp[$i]['nprice'],2)));
			$itemsTemp[$i]['nprice_tail'] = $temp_npriceTail[1];
		}	
		
		//var_dump($itemsTemp);
		$itemList = $itemsTemp;
		
		$smarty = $this->getView();
		//$smarty->caching = true; // ��������
		//$smarty->cache_lifetime = 480; // ҳ�滺��8����
		
		//var_dump($itemList);
		if(!$procat && !$type && !$price)
			$smarty->assign("index",'index');//$this->index = "index";
		$smarty->assign("procat",$procat);//$this->procat = $procat;
		$smarty->assign("type",$type);//$this->type = $type;
		$smarty->assign("price",$price);//$this->price = $price;
		$smarty->assign("pager",$pros->spPager()->getPager());//$this->pager = $pros->spPager()->getPager();
		$smarty->assign("items",$itemList);//$this->items = $itemList;
                $smarty->assign("itemsC1",$itemsC1);//$this->items = $itemList;
		$smarty->assign("admin",$_SESSION['admin'],true);//$this->admin = $_SESSION['admin'];
		
		// �����̬ҳ��
		/* $content = $this->getView()->fetch("front/index.html");
		$fp = fopen("front/day/update.html","w");
		fwrite($fp, $content);
		fclose($fp); */
		//spClass('spHtml')->make(array('main','index'));
		// END �����̬ҳ��
		if($mode)
			$smarty->display("front/mailindex.html");
		else
			if($jsonp){
				foreach($itemList as $k=>&$iv){
					foreach($iv as $k=>&$v){
						$v['title'] = iconv('gbk','utf-8',$v['title']);
					}
				}
				echo json_encode($itemList);
			}else
				$smarty->display("front/index.html");
	}
	public function user($mode='pro'){//�û����� && ����
                $users = spClass("m_u");
		if(!$this->supe_uid)
			header("Location:/?c=user&a=login&refer=".urlencode($_SERVER['REDIRECT_URL']));
		else
			$uinfo = $users->find(array('uid'=>$this->supe_uid));
		$mode = $this->spArgs("mode");
		if($mode=='try')
			$pros = spClass("m_try_items");
		elseif($mode=='pro')
			$pros = spClass("m_pro");
		// ����
		$this->mode = $mode;
		$this->ac = $this->spArgs("ac");
		if(!$this->ac)
			$this->ac = 'bm';
		
	
		
		if($this->ac=='bm'){ // �û�����
			if($_POST['userReport']){
				$item = array(
					'iid'=>$_POST['iid'],
					'pic'=>$_POST['pic'],
					'oprice'=>$_POST['oprice'],
					'nprice'=>$_POST['nprice'],
					'st'=>$_POST['st'],
					'et'=>date("Y-m-d",86400*7+time()),
					'title'=>$_POST['title'],
					'carriage'=>$_POST['carriage'],
					'num'=>(int)$_POST['num'],//�����
					'nick'=>$_POST['ww'],
                                        'ww'=>$_POST['ww'],
					'zk'=>@ceil(10*$_POST['nprice']/$_POST['oprice']),
					'link'=>'http://item.taobao.com/item.htm?id='.$_POST['iid'],
					'ischeck'=>0,
					'rank'=>500,
					'postdt'=>date('Y-m-d H:i:s'),
					'phone'=>$_POST['phone'],
					'commission_rate'=>$_POST['commissionrate'],
					'volume'=>$_POST['volume'],
					'channel'=>2,//����Ϊ�û������������ɼ�������Ϊ1(Ĭ��Ҳ�ǲɼ���������)
					//���������ʱ��δ��
					'cat'=>$_POST['cat']
				);
                                if($mode=='try')
                                    $item['gailv'] = 1000;
                                
                                if($_COOKIE['ymfq_dpww']==$item['ww']){
                                    if($this->isInThere($item['iid'])){//����Ѵ������ݿ�
                                            $iteminfo = $pros->find(array('iid'=>trim($item['iid'])));
                                            $channel = $iteminfo['channel'];
                                            if($channel==1){
                                                    //����ǲɼ��ģ�����������Ϊ��������,������Ϊδ���״̬
                                                    $pros->update(array('iid'=>trim($item['iid'])),array('channel'=>2,'ischeck'=>0));
                                            }elseif($channel==2){
                                                    //����Ѿ��Ǳ����ģ���������״̬
                                                    if($iteminfo['ischeck']==0){
                                                            $submitTips = '��Ʒ�ѱ���,�����ظ�������';
                                                    }elseif($iteminfo['ischeck']==1){
                                                            $submitTips = '��Ʒ��ͨ�����,��ȴ��������ߣ�';
                                                    }elseif($iteminfo['ischeck']==2){
                                                            $submitTips = '��Ʒδͨ�����,����ϵ��������';
                                                    }
                                            }
                                    }else{
                                            $art = $pros->create($item);
                                            if($art){	//�޸ĳɹ�����ת
                                                    $submitTips = '�ѳɹ��ύ�������ĵȴ���ˣ�';
//                                                    header("{spUrl c=main a=user}");
                                            }else
                                                    $submitTips = '�ύʧ�ܣ���ˢ��ҳ�������ύ��';
                                    }                                    
                                }else{
                                    $submitTips = '������Ʒ�ǰ󶨰������������̣������±�����';
                                }

			}
		}elseif($this->ac=='cx'){ // ��������
			if($this->spArgs('searchIid')){
				if($mode=='pro')
					$isInThere = $this->isInThere($this->spArgs('sIid'));
				elseif($mode=='try')
					$isInThere = $this->isInThere($this->spArgs('sIid'),'try_items');
				if($isInThere){ // ��Ʒ�Ƿ����
					$pro = $pros->find(array('iid'=>trim($this->spArgs('sIid'))));
					// ֻ������ƷΪ����������ʱ��Ż���ʾ���״̬������ǲɼ���������ʾΪδ����
					if($pro['channel']==2){
						if($pro['ischeck']){
							if($pro['ischeck']==1)
								$searchTips = '���ͨ����';
							elseif($pro['ischeck']==2)
								$searchTips ='��˲�ͨ����('.$pro['reason'].')';						
						}else{
							$searchTips = '�������...';
						}
					}else{
						$searchTips = '����Ʒ��δ������';
					}
				}else // ���û�����ݿ�鵽Ҳ��δ����
					$searchTips = '����Ʒ��δ������';
			}
			
		}
		
		// �ύ��ʾ
		$this->searchTips = $searchTips;
		$this->submitTips = $submitTips;
		$this->display("front/user.html");
	}
        public function baoming(){
		if(!$this->supe_uid)
			header("Location:/?c=user&a=login&refer=".urlencode($_SERVER['REDIRECT_URL']));
		$this->display("front/baoming.html");
	}
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
}