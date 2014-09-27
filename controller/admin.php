<?php
class admin extends spController{
	public function __construct(){
		parent::__construct();
		import('public-data.php');
		import("function_login_taobao.php");
		//loginTaobao('liushiyan8','liujun987');
		
		global $caijiusers,$website;
		$this->caijiusers = $caijiusers;
		
		// postdt>=curdate()为当日添加，不包括更新
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
			
		
		// 登录判断
		if($this->spArgs()){
			if($this->spArgs('username')=='admin' && $this->spArgs('password')=='xiaozhuzhu5678'){
				$_SESSION['admin'] = 1;
				header("Location:/admin.html");
			}elseif($this->spArgs('username') && $this->spArgs('password')){
				foreach($this->caijiusers as $k=>$v){
					if($this->spArgs('username') == $v['username'] && $this->spArgs('password') == $v['password']){
						$_SESSION['iscaijiuser'] = $this->spArgs('username');
					}
				}
				// 二次验证
				//$url_last = '.uz.taobao.com/view/front/getusernick.php';
				//$url = 'http://'.$_SESSION['iscaijiuser'].$url_last;
				// END - 二次验证
				if($_SESSION['iscaijiuser'])
					header("Location:/dbselect.html");
			} 
		}
		else{
			header("Location:/login.html");
		} 
		
		$this->display("admin/login.html");
	}
	
	
	// 获取单品信息
	public function getiteminfo(){
		/* if(!$_SESSION['admin'])
			header("Location:/login.html"); */
		
		$iid = $this->spArgs('iid');
		
		$catmaps = spClass("m_catmap");
		import('tbapi.php');
		
		$item = getItemDetail($iid);
		
		// 递归取得淘宝二级节点
		$pcid = getPcid($item['cid']);
		$pcid = $pcid['cid'];
		// end - 递归取得淘宝二级节点
		
		// 查询fstk_catmap对应类目
		$catMap = $catmaps->find(array('cid'=>$pcid),'','type');
		//var_dump($catMap);
		if($catMap){ //如果商品类目有映射
			$item['cat'] = (int)$catMap['type'];
		}else{
			$item['cat'] = 42;
		}
		// end - 查询fstk_catmap对应类目
			
		// 字符转换
		$item['title'] = iconv('utf-8','gb2312',$item['title']);
		$item['title'] = preg_replace('/【.+?】/i','',$item['title']);
		$item['nick'] = iconv('utf-8','gb2312',$item['nick']);
		// end - 字符转换
		//$item['sid'] = getShop($item['nick']);
		//var_dump($item);
		echo '{"iid":"'.$item['iid'].'","title":"'.$item['title'].'","nick":"'.$item['nick'].'","pic":"'.$item['pic'].'","oprice":"'.$item['oprice'].'","st":"'.$item['st'].'","et":"'.$item['et'].'","cid":"'.$item['cid'].'","link":"'.$item['link'].'","rank":'.$item['rank'].',"postdt":"'.$item['postdt'].'","ischeck":'.$item['ischeck'].',"volume":'.$item['volume'].',"carriage":'.$item['carriage'].',"shopshow":'.$item['shopshow'].',"shopv":'.$item['shopv'].',"cat":'.$item['cat'].'}';
	}
	
	// 后台首页
	public function index(){
		ini_set('memory_limit','256M');
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		$pros = spClass("m_pro");
		// 所有没有下架的商品统计
		if($result = $pros->findAll('st<=curdate() and et>=curdate()')){
			$this->allPros = count($result);
		}
		// 当天提交的没有下架的商品统计
		if($result = $pros->findAll('st<=curdate() and et>=curdate() and postdt>=curdate()')){
			$this->todayPros = count($result);
		}
		// 过期商品
		$guoqis = $pros->findAll('et<curdate()');
		$this->guoqis = count($guoqis);
		
		
		$this->indexCur = 1;
		$this->display("admin/index.html");
	}
	
	// 商品管理
	public function pro(){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		$type = $this->spArgs('type');
		$sh = $this->spArgs('sh');
		$q = $this->spArgs('q');
		
		$pros = spClass("m_pro");
		
		$page = $this->spArgs('page',1);

		$where = 'st<=curdate() and et>=curdate() and ischeck=1';
		$order = 'rank asc,postdt desc';
		
		if($type==87)
			$where .= ' and type='.$type;
		else 
			$where .= ' and type!=87';
		if($sh=='no')
			$where = 'ischeck=0';
		elseif($sh=='ck2')
			$where = 'ischeck=2';
		if($q)
			$where = 'iid='.$q;
		$itemsTemp = $pros->spPager($page,56)->findAll($where,$order);
		
		$this->items = $itemsTemp;
		$this->pager = $pros->spPager()->getPager();
                
                $this->proCur = 1;
		$this->display("admin/pro.html");
	}
	// 商品审核
	public function checkpro(){
		$id = $this->spArgs("id");
		$pros = spClass("m_pro");
		$pro = $pros->find(array('id'=>$id));
		if($_POST['checkit']){
			if($_POST['checkpro']==1){
				if($pros->update(array('id'=>$id),array('ischeck'=>1,'type'=>87)))
					echo '操作成功,商品已通过审核！';
			}
			elseif($_POST['checkpro']==2){
				if($_POST['reason'] || $_POST['reasonSelect']){
					if($_POST['reasonSelect']){
						foreach($_POST['reasonSelect'] as $v){
							$reason .= $v;
						}
					}
					if($_POST['reason']){
						$reason .= $_POST['reason'];
					}
					if($pros->update(array('id'=>$id),array('ischeck'=>2,'reason'=>'亲 '.$reason)))
						echo '操作成功,商品不通过审核！';
				}else
					echo '操作失败,请填写备注！';
			}
				header("Location:/pro/sh/no.html");
		}
		
		$this->pro = $pro; 
		$this->display('admin/checkpro.html');
	}
	
	// 查找是否存在
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
	
	// 商品添加
	public function addpro($mode='pro'){
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		$pros = spClass("m_pro");
		$actfrom = spClass("m_actfrom");
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
					'rank'=>(int)$_POST['rank'],
					'title'=>$_POST['title'],
					'link'=>'http://item.taobao.com/item.htm?id='.$_POST['iid'],
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
					'nick'=>$_POST['ww']
			);
			if($_POST['forward'])
				$item['postdt'] = date("Y-m-d H:i:s");
			if($mode!='try'){// 试用商品添加
				if($this->isInThere($item['iid'])){
					$submitTips = '商品已存在,请勿重复添加';
				}else{
					$art = $pros->create($item);
					if($art){	//修改成功后跳转
						$submitTips = '添加成功';
						header("{spUrl c=admin a=pro}");
					}else
						$submitTips = '添加失败';
				}
			}else{
				if($this->isInThere($item['iid'],'try_items')){
					$submitTips = '商品已存在,请勿重复添加';
				}else{
					$item['istry'] = 1;
					$item['gailv'] = $_POST['gailv'];
					$art = $pros->create($item);
					if($art){	//修改成功后跳转
						$submitTips = '添加成功';
						header("{spUrl c=admin a=pro}");
					}
					else
						$submitTips = '添加失败';
				}
			}
		}
	
		if($mode=='try')
			$this->tryadd = "tryadd";
		$this->st = date("Y-m-d");
		$this->et = date("Y-m-d",86400*7+time());
		
		$actfroms = $actfrom->findAll();
		$proCats = $proCat->findAll();
		// 商品类别
		$this->actfroms = $actfroms;
		$this->proCats = $proCats;
		// 提交提示
		$this->submitTips = $submitTips;
		$this->display("admin/addpro.html");
	}
	
	// 删除过期商品
	public function delgq(){
		$pros = spClass("m_pro");
		if($pros->delete('et<curdate()'))
			header("Location:/admin.html");
	}
	// 商品删除
	public function delpro(){
		$id = $this->spArgs('id');
		$pros = spClass("m_pro");
		if($pros->delete(array('id'=>$id)))
			header("Location:/pro.html");
	}
	// 商品修改
	public function modpro($mode='pro'){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		$pros = spClass("m_pro");
		$actfrom = spClass("m_actfrom");
		$proCat = spClass("m_procat");
		
		$id = $this->spArgs('id');
		
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
					'rank'=>(int)$_POST['rank'],
					'title'=>$_POST['title'],
					'link'=>'http://item.taobao.com/item.htm?id='.$_POST['iid'],
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
					'nick'=>$_POST['ww']
			);
			if($_POST['forward']){
				$item['st'] = date('Y-m-d');
				$item['postdt'] = date('Y-m-d H:i:s');
			}
			if($mode!='try'){
				$art = $pros->update(array('id'=>$id),$item);
			}else{
				//$item['istry'] = 1;
				//$item['gailv'] = $_POST['gailv'];
				//$art = self::$proDAO->autoExecute('try_items',$item,'update','where id="'.$id.'"');
			}
			if($art){ // 修改成功后跳转
				$submitTips = '修改成功';
				if($item!='try')
					header("{spUrl c=admin a=pro}");
				//else
				//	header("Location:/d/tryadmin");
			}else
				$submitTips = '修改失败';
		}
		
		
		$pro = $pros->find(array('id'=>$id));
		$actfroms = $actfrom->findAll();
		$proCats = $proCat->findAll();
		
		$this->submitTips = $submitTips;
		$this->pro = $pro;
		$this->actfroms = $actfroms;
		$this->proCats = $proCats;
		$this->display("admin/modpro.html");
	}
	
	// 用户管理
	public function yonghu(){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		//$pros = spClass("m_pro");
                $this->yonghuCur =1;
		$this->display("admin/yonghu.html");
	}
	
	// 友链管理
	public function link(){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		//$pros = spClass("m_pro");
                $this->linkCur =1;
		$this->display("admin/link.html");
	}
	
	// 广告管理
	public function ad(){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		//$pros = spClass("m_pro");
                $this->adCur =1;
		$this->display("admin/ad.html");
	}
	
	// 后台商品采集页
	public function proget(){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		//import('public-data.php');
		$pros = spClass('m_pro');		
		$where = 'st<=curdate() and et>=curdate() and ischeck=1 and postdt>=curdate()';
		$noyj = $this->spArgs('noyj');
		if($noyj=='yes')
                    $items = $pros->findAll($where.' and commission_rate=-1');
		$this->items = $items;
		
                $this->itemCounts = count($items);
                
		foreach($items as $k=>$v){
                    $iidarr[] = array(iid=>$v['iid']);
		}	
				
		$this->iidarr = $iidarr;
					
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
		// 采集开春哥
		ini_set('memory_limit', '64M'); // 内存超载
		ini_set('pcre.backtrack_limit', 999999999); // 回溯超载
		ini_set('pcre.recursion_limit', 99999); // 资源开大就行
		// end - 采集开春哥
                
		$pros = spClass('m_pro');		
		$where = 'st<=curdate() and et>=curdate() and ischeck=1 and postdt>=curdate()';
		$items = $pros->findAll($where.' and commission_rate=-1');
		foreach($items as $k=>$v){
                    $yj = getCommissionRate($v['iid']);
                    $itemTemp = array('commission_rate'=>$yj);
                    $pros->update(array('iid'=>$v['iid']),$item);
		}	 
		$this->display("admin/uzcaiji.html");
		header("Location:/proget.html");
	}
	
	// 采集
	public function uzcaijiapi(){
		set_time_limit(0);
		// 采集开春哥
		ini_set('memory_limit', '64M'); // 内存超载
		ini_set('pcre.backtrack_limit', 999999999); // 回溯超载
		ini_set('pcre.recursion_limit', 99999); // 资源开大就行
		// end - 采集开春哥
		
		import('uzcaiji-class.php');
		$xiaiCaiji = spClass('UzCaiji');
			
		$type = $this->spArgs('type');
		$actType = $this->website[$type]['actType'];
		
		
		// 采集接口输出
		if($actType){
			if($actType == 15){
				$caijiarr = array();
				for($page=1;$page<=1;$page++){
					$xiaiCaiji->Caiji($type,$page);
					$caijiarr = array_merge($caijiarr,$xiaiCaiji->getitems());
				}
				echo json_encode($caijiarr);
			}elseif($actType == 4 || $actType == 11 || $actType == 20){ 
				//$pages = $xiaiCaiji->Caiji($type,'',3);
				//$pages = @ceil($pages/45);
				
				$caijiarr = array();
				for($page=1;$page<=2;$page++){
					$xiaiCaiji->Caiji($type,$page);
					$caijiarr = array_merge($caijiarr,$xiaiCaiji->getitems());
				}
				echo json_encode($caijiarr);
			}elseif($actType == 10 || $actType == 16){ // 秒杀通采集5页
				$caijiarr = array();
				for($page=1;$page<=5;$page++){
					$xiaiCaiji->Caiji($type,$page);
					$caijiarr = array_merge($caijiarr,$xiaiCaiji->getitems());
				}
				echo json_encode($caijiarr);
			}else{
				//echo $actType;
				$xiaiCaiji->Caiji($type,'',2);
			}
		}else{
			echo '没有选择采集站点!';
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
					
				// 现价  && 图片
				$item['nprice'] = $v['nprice'];
				if($v['pic'])
					$item['pic'] = $v['pic'];
				// end - 现价  && 图片
					
				// 递归取得淘宝二级节点
				$pcid = getPcid($item['cid']);
				$pcid = $pcid['cid'];
				// end - 递归取得淘宝二级节点
		
				// 查询fstk_catmap对应类目
				$catMap = $catmaps->find(array('cid'=>$pcid),'','type');
				//var_dump($catMap);
				if($catMap){ //如果商品类目有映射
					$item['cat'] = (int)$catMap['type'];
				}else{
					$item['cat'] = 42;
				}
				// end - 查询fstk_catmap对应类目
					
				// 字符转换
				$item['title'] = iconv('utf-8','gb2312',$item['title']);
				$item['title'] = preg_replace('/【.+?】/i','',$item['title']);
				$item['nick'] = iconv('utf-8','gb2312',$item['nick']);
				// end - 字符转换
					
				$item['act_from'] = $actType;
				$item['last_modify'] = date("Y-m-d H:i:s");
				//var_dump($item);
				if(!$pros->find(array('iid'=>$v['iid']))){ //没找到
					$item['postdt'] = date("Y-m-d H:i:s");
					if(!$pros->create($item))
						echo $v['iid'].' 添加失败,数据库操作失败!<br/>';
					else{
						//$this->upyjscript($v['iid'],$actType);
						//$this->updateyjPhp($v['iid']);
						echo $v['iid'].' 添加成功!<br/>';
					}
				}else{
					//$itemPostdt = $pros->find(array('iid'=>$v['iid']));
					//$item['postdt'] = $itemPostdt['postdt'];
					if(!$pros->update(array('iid'=>$v['iid']),$item))
						echo $v['iid'].' 更新失败,数据库操作失败!<br/>';
					else{
						//$this->upyjscript($v['iid'],$actType);
						//$this->updateyjPhp($v['iid']);
						echo $v['iid'].' 更新成功!<br/>';
					}
		
				}
			}
		}
		$this->display("admin/uzcaiji.html");
	}
	
	// 一键采集
	public function yjuzcaiji(){
		foreach($this->website as $k=>$v){
			if($k!='none')
				$this->uzcaiji();
		}
		$this->display("admin/uzcaiji.html");
	}
	
	// 采集
	public function uzcaiji($type=null){
		
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		set_time_limit(0);
              
                
		// 采集开春哥
		ini_set('memory_limit', '64M'); // 内存超载
		ini_set('pcre.backtrack_limit', 999999999); // 回溯超载
		ini_set('pcre.recursion_limit', 99999); // 资源开大就行
		// end - 采集开春哥
		//loginTaobao('天呐你真帅','pp1044835155');
		
		/*echo '<script type="text/javascript" src="http://code.jquery.com/jquery-1.5.2.min.js"></script>
			<script src="http://a.tbcdn.cn/apps/top/x/sdk.js?appkey=21511111"></script>';
		// TOP API
		$timestamp=time()."000";
		//$app_key = '12636285';
		//$secret = '63e664fafc1f3f03a7b8ad566c42819d';
		$app_key = '21511111';
		$secret = '4b7df3004e66b43f4632e2a85fe3f308';
		$message = $secret.'app_key'.$app_key.'timestamp'.$timestamp.$secret;
		$mysign=strtoupper(hash_hmac("md5",$message,$secret));
		setcookie("timestamp",$timestamp);
		setcookie("sign",$mysign);
		// END TOP API 
                 * 
                 */
	
		import('uzcaiji-class.php');
		$xiaiCaiji = spClass('UzCaiji');
				
		//采集
		if(!$type)
			$type = $this->spArgs('type');
		
			
		$actType = $this->website[$type]['actType'];
		/* $pros = spClass("m_pro");
		$catmaps = spClass("m_catmap");
		import('tbapi.php'); */
		//echo $caiji.'<br/>';
		if($actType){
			if($actType == 15){ // 赚宝
				for($page=1;$page<=1;$page++){
					//$xiaiCaiji->Caiji($type,$page);
					$xiaiCaiji->Caiji($type);
					$items = $xiaiCaiji->getitems();
					$this->getitems($items, $actType);
				}
			}elseif($actType == 4  || $actType == 11 || $actType == 20){ // 卷皮  && 九块邮  && 美美日志
				//$pages = $xiaiCaiji->Caiji($type,'',3);
				//$pages = @ceil($pages/45);
				$pages = 2;
				for($page=1;$page<=$pages;$page++){
					$xiaiCaiji->Caiji($type,$page);
					$items = $xiaiCaiji->getitems();
					//var_dump($items);
					$this->getitems($items, $actType);
				}
			}elseif($actType == 10 || $actType == 16){ // 秒杀通,特价疯抢采集5页
				for($page=1;$page<=5;$page++){
					$xiaiCaiji->Caiji($type,$page);
					$items = $xiaiCaiji->getitems();
					$this->getitems($items, $actType);
				}
			}else{
				$xiaiCaiji->Caiji($type);
				$items = $xiaiCaiji->getitems();
				//var_dump($items);
				$this->getitems($items, $actType);
			}
		}else{
			echo '没有选择采集站点!';
		}
	
		//$this->website = $website;
		$this->display("admin/uzcaiji.html");
	}
	public function postDataToUzPhp($item,$uz){
		//var_dump($item);
		if($uz=='admin'){
			$url = 'http://yinxiang.uz.taobao.com/d/getdata';
		}elseif($uz=='cong'){
			$url = 'http://zhekouba.uz.taobao.com/d/getdata';
		}else{
			$url = 'http://'.$uz.'.uz.taobao.com/d/getdata';
		}
		$contents = "pic=$item[pic]&&cat=$item[cat]&&iid=$item[iid]&&oprice=$item[oprice]&&nprice=$item[nprice]&&st=$item[st]&&et=$item[et]&&act_from=$item[act_from]&&rank=$item[rank]&&title=$item[title]&&link=$item[link]&&slink=$item[slink]&&volume=$item[volume]&&postdt=$item[postdt]&&xujf=$item[xujf]&&remark=$item[remark]&&type=$item[type]&&content=$item[content]&&zk=$item[zk]&&carriage=$item[carriage]&&commission_rate=$item[commission_rate]&&ischeck=$item[ischeck]&&last_modify=$item[last_modify]";
		$opts = array(
				'http'=>array(
						'method'=>"POST",
						'content'=>$contents,
						'timeout'=>900,
				));
		//echo $contents.'<br />';
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
		ini_set('memory_limit', '126M'); // 内存超载
		ini_set('pcre.backtrack_limit', 999999999); // 回溯超载
		ini_set('pcre.recursion_limit', 99999); // 资源开大就行
		set_time_limit(0);
		
		if(!$_SESSION['admin'])
			if(!$_SESSION['iscaijiuser'])
				header("Location:/login.html");
		header("Content-Type: text/html; charset=gbk");
		
		$baseSql = 'st<=curdate() and et>=curdate() and ischeck=1';
		$noAd = 'type!=87';
		$baseSql .= ' and '.$noAd; // 当天的预告过滤
		
		$pros = spClass('m_pro');
		
		// 各个平台的数据单独导
		$type = $this->spArgs('type');
		$actfrom = $this->website[$this->spArgs('type')]['actType'];
		if($actfrom){ // 每个平台选择			
			$page = $this->spArgs('page',1);
			if($actfrom==2 || $actfrom==6 || $actfrom==9)// 会员购,VIP特惠，VIP购优惠不过滤佣金
				$where = 'act_from='.$actfrom.' and '.$baseSql.' and postdt>=curdate()';
			else{
				if(COMISSIONRATESORT)
					$where = 'act_from='.$actfrom.' and '.$baseSql.' and postdt>=curdate()  and commission_rate>=5';
				else
					$where = 'act_from='.$actfrom.' and '.$baseSql.' and postdt>=curdate()';
			}
		}else{ // 全部
			$page = $this->spArgs('page',1);
			if(COMISSIONRATESORT)
				$where = $baseSql.' and postdt>=curdate() and commission_rate>=5';
			else
				$where = $baseSql.' and postdt>=curdate()';
		}
		
		$items = $pros->findAll($where); //,'commission_rate asc'
		foreach($items as $k=>$v){
			if(is_numeric($v['iid'])){
				if(empty($v['phone']))
					$v['phone'] = '123456789';
				$v['rank'] = 500;
				if(!$v['shopshow'])
					$v['shopshow']=1;
				if(!$v['shopv'])
					$v['shopv']=0;
				$v['title'] = preg_replace('/【.+?】/i','',$v['title']);
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
	}
	
	public function upyjscript($iid,$actType){
		echo '<script language="javascript">';
		echo "TOP.api('rest', 'get',{
		method:'taobao.taobaoke.widget.items.convert',
		fields:'commission_rate,promotion_price,volume',
		num_iids:$iid,
		
		},function(resp){
		if(resp.error_response){
				alert('taobao.taobaoke.widget.items.convert接口获取商信息品失败!'+resp.error_response.msg);
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
	
	// 更新佣金插件PHP版
	public function updateyjPhp($iid,$cookie=''){
		/*set_time_limit(0);
		// 采集开春哥
		ini_set('memory_limit', '64M'); // 内存超载
		ini_set('pcre.backtrack_limit', 999999999); // 回溯超载
		ini_set('pcre.recursion_limit', 99999); // 资源开大就行
		// end - 采集开春哥
		$this->url = 'http://tao.as/tools/getcommission.php?item_id='.$iid;
		$result = file_get_contents($this->url);
		
		// 匹配佣金
		$yjptn = '/<tr>(.+?)<\/tr>/is';
		preg_match_all($yjptn,$result,$yjarr,PREG_SET_ORDER);
		$yjptn = '/<strong>(.+?)%<\/strong>/i';
		preg_match_all($yjptn,$yjarr[0][1],$yjarr1,PREG_SET_ORDER);
		$yj = $yjarr1[0][1];
		
                 * 
                 */
                $pros = spClass('m_pro');
                $yj = getCommissionRate($iid);
                //echo $yj.'<br/>';
		$item['commission_rate'] = $yj;
		if($iid && $yj){
			$pros->update(array('iid'=>$iid),$item);
		}
		//print_r($yjarr1);
	}
	// 更新佣金插件
	public function updateyjonce(){
		if(!$_SESSION['admin'])
			header("Location:/login.html");
		
		$pros = spClass('m_pro');
		$yj = $this->spArgs('zk');
		$iid = $this->spArgs('iid');
		$item['commission_rate'] = $yj;
		//echo $iid.' 佣金'.$yj.'<br/>';
		if($iid && $yj){
			$pros->update(array('iid'=>$iid),$item);
		}
		$this->display("admin/uzcaiji.html");
	}
	// END 更新佣金插件
	// 数据导出
	public function dbselect(){
		if(!$_SESSION['admin'])
			if(!$_SESSION['iscaijiuser'])
				header("Location:/login.html");
			
		$baseSql = 'st<=curdate() and et>=curdate() and ischeck=1';
		$noAd = 'type!=87';
		$baseSql .= ' and '.$noAd; // 当天的预告过滤
		
		
		$pros = spClass('m_pro');
		
		// 当天采集的数据
//		if(COMISSIONRATESORT){
//			$where = $baseSql.' and postdt>=curdate() and commission_rate>=5';
//		}
//		else{    
//			$where = $baseSql.' and postdt>=curdate()';
//		}
		
		
		// 各个平台的数据单独导
		$type = $this->spArgs('type');
		$actfrom = $this->website[$this->spArgs('type')]['actType'];
		if($actfrom){ // 每个平台选择			
			$page = $this->spArgs('page',1);
			if($actfrom==2 || $actfrom==6 || $actfrom==9)// 会员购,VIP特惠，VIP购优惠不过滤佣金
				$where = 'act_from='.$actfrom.' and '.$baseSql.' and postdt>=curdate()';
			else{
				if(COMISSIONRATESORT)
					$where = 'act_from='.$actfrom.' and '.$baseSql.' and postdt>=curdate()  and commission_rate>=5';
				else
					$where = 'act_from='.$actfrom.' and '.$baseSql.' and postdt>=curdate()';
			}
		}else{ // 全部
			$page = $this->spArgs('page',1);
			if(COMISSIONRATESORT)
				$where = $baseSql.' and postdt>=curdate() and commission_rate>=5';
			else
				$where = $baseSql.' and postdt>=curdate()';
		}
		
		$itemsTemp = $pros->spPager($page,50)->findAll($where);
		
		// 采集用户的信息
		if($_SESSION['iscaijiuser']){
			$this->iscaijiuser = $_SESSION['iscaijiuser'];
			$this->username = $this->caijiusers[$_SESSION['iscaijiuser']]['nick'];
		}
		
		$this->type = $type;
		$this->actfrom = $actfrom;
		$this->pager = $pros->spPager()->getPager();
                
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
	
	// 数据导出
	public function sqlout(){
		
		if(!$_SESSION['admin'])
			if(!$_SESSION['iscaijiuser'])
				header("Location:/login.html");
		
		$actfrom =	$this->website[$this->spArgs('type')]['actType'];
		$pros = spClass('m_pro');
		//echo $actfrom.'best<br/>';
		if($actfrom){		
			if($actfrom==2 || $actfrom==6 || $actfrom==9)// 会员购,VIP特惠，VIP购优惠不过滤佣金
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
		if($_SESSION['iscaijiuser']=='yuansu')// 爆划算接口
			$sqlout_fir = "INSERT INTO `items` (`iid`,`title`,`picurl`,`itemurl`,`price`,`prom`,`nick`,`categoryid`,`partid`,`status`,`top`,`gg`,`report`,`freeshipping`,`stock`,`sorts`,`starttime`,`endtime`) VALUES ";
		else // fstk_数据库的输出
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
				$v['title'] = preg_replace('/【.+?】/i','',$v['title']);
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
				if($_SESSION['iscaijiuser']=='ifengqiang' || $_SESSION['iscaijiuser']=='chuang' || $_SESSION['iscaijiuser']=='360tuan' || $_SESSION['iscaijiuser']=='tongqu' || $_SESSION['iscaijiuser']=='tbcsh' || $_SESSION['iscaijiuser']=='9kuaigou'){
					if($v['nprice']<10)
						$v['act_from'] = 2;
					else 
						$v['act_from'] = 3;
				}else{
					$v['act_from'] = 1;
				}
				if($_SESSION['iscaijiuser']=='9kuaigou'){ // 九块购
					if($v['cat']==27)
						$v['cat']=22;
					$sqlout_sec .= $sqlout_fir.' ("'.$v['title'].'","'.$v['oprice'].'","'.$v['nprice'].'","'.$v['pic'].'","'.$v['st'].'","'.$v['et'].'","'.$v['type'].'","'.$v['cat'].'","'.$v['ischeck'].'","http://item.taobao.com/item.htm?id='.$v['iid'].'","'.$v['rank'].'","'.$v['num'].'","'.$v['slink'].'","'.$v['ww'].'","'.$v['snum'].'","'.$v['xujf'].'",now(),"'.$v['zk'].'","'.$v['iid'].'","'.$v['volume'].'","'.$v['content'].'","'.$v['remark'].'","'.$v['nick'].'","'.$v['reason'].'","'.$v['carriage'].'","'.$v['commission_rate'].'",now(),"'.$v['click_num'].'","'.$v['phone'].'","'.$v['act_from'].'","'.$v['shopshow'].'","'.$v['shopv'].'")  ON DUPLICATE KEY UPDATE last_modify=now(),cat='.$v['cat'].',pic="'.$v['pic'].'",et="'.$v['et'].'",commission_rate='.$v['commission_rate'].';';
				}elseif($_SESSION['iscaijiuser']=='yuansu'){ // 爆划算
					//$sqlout_sec = null;
					$oldcat = $v['cat'];
					if($oldcat==20)
						$leimu = 19;
					elseif($oldcat==21)
						$leimu = 20;
					elseif($oldcat==22)
						$leimu = 21;
					elseif($oldcat==23)
						$leimu = 22;
					elseif($oldcat==24)
						$leimu = 23;
					elseif($oldcat==25)
						$leimu = 24;
					elseif($oldcat==26)
						$leimu = 25;
					elseif($oldcat==27)
						$leimu = 27;
					elseif($oldcat==28)
						$leimu = 28;
					elseif($oldcat==42)
						$leimu = 29;
					else
						$leimu = 29;
					$st = date('Ymd',strtotime($v['st']));
					$et = date('Ymd',strtotime($v['et']));
					$v['pic'] = preg_replace('/_310x310.jpg/i','',$v['pic']);
					$sqlout_sec .= $sqlout_fir.' ("'.$v['iid'].'","'.$v['title'].'","'.$v['pic'].'","http://item.taobao.com/item.htm?id='.$v['iid'].'","'.$v['oprice'].'","'.$v['nprice'].'","'.$v['ww'].'","'.$leimu.'","13","1","1","0","","'.$v['carriage'].'","0","140","'.$st.'","'.$et.'") ON DUPLICATE KEY UPDATE prom="'.$v['nprice'].'",commrate="'.$v['commission_rate']*$v['nprice'].'";';
				}else{
					$sqlout_sec .= $sqlout_fir.' ("'.$v['title'].'","'.$v['oprice'].'","'.$v['nprice'].'","'.$v['pic'].'","'.$v['st'].'","'.$v['et'].'","'.$v['type'].'","'.$v['cat'].'","'.$v['ischeck'].'","http://item.taobao.com/item.htm?id='.$v['iid'].'","'.$v['rank'].'","'.$v['num'].'","'.$v['slink'].'","'.$v['ww'].'","'.$v['snum'].'","'.$v['xujf'].'",now(),"'.$v['zk'].'","'.$v['iid'].'","'.$v['volume'].'","'.$v['content'].'","'.$v['remark'].'","'.$v['nick'].'","'.$v['reason'].'","'.$v['carriage'].'","'.$v['commission_rate'].'",now(),"'.$v['click_num'].'","'.$v['phone'].'","'.$v['act_from'].'","'.$v['shopshow'].'","'.$v['shopv'].'")  ON DUPLICATE KEY UPDATE last_modify=now(),cat='.$v['cat'].',et="'.$v['et'].'",commission_rate='.$v['commission_rate'].';';
				}
				//echo $sqlout_sec;
			}
		}
		echo $sqlout_sec;
		$this->display("admin/uzcaiji.html");
	}
	
	// 淘客报表
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