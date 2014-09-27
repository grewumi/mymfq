<?php
class main extends spController{
	public function __construct(){
		parent::__construct();
		include 'Mobile_Detect.php';
		$detect = spClass('Mobile_Detect');
		if ($detect->isMobile()){
			if($detect->isiOS()){
				//echo 'ƻ���ն˵�¼';// �����iOS��ϵͳ�Ļ���ִ������Ĵ���
				//$detect->version('iPhone') // 3.1 (float)
				
			}
			if($detect->isAndroidOS()){
				//echo '��׿�ն˵�¼';
				//echo $detect->version('Android'); // 2.1 (float)
				// �����androidϵͳ�Ļ���ִ������Ĵ���
			}
		}else{
			;//header("Location:http://www.yimiaofengqiang.com/");
		}
		if($detect->isTablet()){
			// ����ͻ�����ƽ����ԵĻ���ִ������Ĵ���
		}else{
			;//header("Location:http://www.yimiaofengqiang.com/");
		}
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
		$this->pro = $pro;
		$this->display("front/deal.html");
	}
	
	public function index($mode=false){
		/* $to = "241776039@qq.com";
		$subject = "Test mail";
		$message = "Hello! This is a simple email message.";
		$from = "test@432gou.com";
		$headers = "From: $from";
		mail($to,$subject,$message,$headers);
		echo "Mail Sent."; */
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
				'200_9999'=>'nprice>=200'
		);
		// end - ����Key��ֵ��Ӧ������,����ȡֵ
		$baseSql = 'st<=curdate() and ischeck=1 and type!=87';
		$baseSqlYu = 'st<=curdate() and ischeck=1';
		$order = 'rank asc,postdt desc';				
		
		$procat = $this->spArgs('procat');
		$page = $this->spArgs('page',1);
		$type = $this->spArgs('type');
		$price = $this->spArgs('price');
		
		$m_procats = spClass("m_procat");
		$procats = $m_procats->findAll('isshow=1','type asc');
		
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
		
		$itemsTemp = $pros->spPager($page,56)->findAll($where,$order);
		
		// ������foreach & �ı������ֵ��ʱ�����һ�����ݴ��� & ����,�������һ�������ظ�
		for($i=0;$i<count($itemsTemp);$i++){
			$itemsTemp[$i]['title'] = preg_replace('/��.+?��/i','',$itemsTemp[$i]['title']);
			$itemsTemp[$i]['title'] = preg_replace('/����׬��/i','',$itemsTemp[$i]['title']);
			$itemsTemp[$i]['oprice'] = number_format($itemsTemp[$i]['oprice'],2);
			$temp_npriceTail = explode('.',strval(number_format($itemsTemp[$i]['nprice'],2)));
			$itemsTemp[$i]['nprice_tail'] = $temp_npriceTail[1];
		}	
		//var_dump($itemsTemp);
		$itemList = array(array(),array());
		if(!empty($itemsTemp)){
			foreach($itemsTemp as $k=>$v){
				array_push($itemList[$k%2],$v);
			}
		}
		//var_dump($itemList);
		if(!$procat && !$type && !$price)
			$this->index = "index";
		$this->procat = $procat;
		$this->type = $type;
		$this->price = $price;
		$this->procats = $procats;
		$this->pager = $pros->spPager()->getPager();
		$this->items = $itemList;
		$this->admin = $_SESSION['admin'];
		
		// �����̬ҳ��
		/* $content = $this->getView()->fetch("front/index.html");
		$fp = fopen("front/day/update.html","w");
		fwrite($fp, $content);
		fclose($fp); */
		//spClass('spHtml')->make(array('main','index'));
		// END �����̬ҳ��
		if($mode)
			$this->display("front/mailindex.html");
		else
			$this->display("front/index.html");
	}
	public function user(){
		$pros = spClass("m_pro");
		$m_procats = spClass("m_procat");
		$procats = $m_procats->findAll('isshow=1','type asc');
		$this->procats = $procats;
		
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
				'ww'=>$_POST['ww'],
				'zk'=>@ceil(10*$_POST['nprice']/$_POST['oprice']),
				'link'=>'http://item.taobao.com/item.htm?id='.$_POST['iid'],
				'ischeck'=>0,
				'rank'=>500,
				'postdt'=>date('Y-m-d H:i:s'),
				'phone'=>$_POST['phone'],
				//���������ʱ��δ��
				'cat'=>$_POST['cat']
			);
			if($this->isInThere($item['iid'])){
				$submitTips = '��Ʒ�Ѵ���,�����ظ����';
			}else{
				$art = $pros->create($item);
				if($art){	//�޸ĳɹ�����ת
					$submitTips = '��ӳɹ�';
					header("{spUrl c=main a=user}");
				}else
					$submitTips = '���ʧ��';
			}
		}
		if($this->spArgs('searchIid')){
			if($this->isInThere($this->spArgs('sIid'))){ // ��Ʒ�Ƿ����
				$pro = $pros->find(array('iid'=>$this->spArgs('sIid')));
			
				if($pro['ischeck']){
					if($pro['ischeck']==1)
						$searchTips = '���ͨ����';
					elseif($pro['ischeck']==2)
						$searchTips ='��˲�ͨ����('.$pro['reason'].')';						
				}else{
					$searchTips = '�������...';
				}
				
			}else 
				$searchTips = '����Ʒ��δ������';
		}
		// �ύ��ʾ
		$this->searchTips = $searchTips;
		$this->submitTips = $submitTips;
		$this->display("front/user.html");
	}
	
	public function isInThere($iid,$table='pro',$field=null){
		$pros = spClass("m_pro");
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