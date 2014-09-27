<?php
class virtualapi extends spController{
  public function loginAlimama(){
    import("function_login_taobao.php");
	
	/* 
	if(!empty($_POST['username']) && !empty($_POST['password'])){
        header("Content-type: text/html; charset=gbk");
        $user = trim($_POST['username']);
        $pass = trim($_POST['password']);

        loginTaobao($user, $pass);
       
     }*/
	
	$r = loginTaobao('liushiyan8','liujun987');
	if($r==2)
		return 'use cookie';
	else
		return 'use checkcode';
    //return getCommissionRate('17555570604');
    //$this->display("admin/loginAlimama.html");
  } 
  public function getCommission($iid){
		return array('use'=>loginAlimama(),'CommissionRate'=>getCommissionRate($iid));
  }
  public function getCheckcode(){
	$imgurl = urldecode($this->spArgs("imgurl"));
	//$imgurl .= '&_r_='.time().rand(000,1000);
	//echo $imgurl;
	$img = getImage($imgurl,'tmp','Checkcode.jpg',1);
	//var_dump($img);
	$softID = '96821';
	$softKey = 'af21e117eb4a4befb55ab75d16ffa997';
	$userName = 'lemontea';
	$passWord = 'uu.86#set.';
	$codeType = '1004';
	import('uuapi.php');
	$obj = spClass('uuApi');
	$obj->setSoftInfo($softID,$softKey);
	$loginStatus=$obj->userLogin($userName,$passWord);
	if($loginStatus>0){
		//echo '您的用户ID为：'.$loginStatus.'<br/>';
		$getPoint=$obj->getPoint($userName,$passWord);
		if(!$getPoint)
			echo '您帐户内的剩余题分还有：'.$getPoint.'<br/><br/>';//负数就是错误代码
		else{
			//下面开始识别	
			$file = array(
				'name'=>'Checkcode.jpg',
				'type'=>'image/jpeg',
				'size'=>filesize('/tmp/Checkcode.jpg'),
				'tmp_name'=>'Checkcode.jpg'
			);
			$result=$obj->autoRecognition($file,$codeType);

			echo $result;
		}
	}else{
		echo '登录失败，错误代码为：'.$loginStatus.'<br/>';
	}
  }
  
  public function checklogo(){
	$iid = $this->spArgs('iid');
	$where = $this->spArgs('where');
	$shop = $this->spArgs('shop');
	if(!$shop)
		$shop = 'c';
	if($where=='left'){
		//checkleftlogo($iid,$shop);
		echo '{"show":'.checkleftlogo($iid,$shop).'}';		
	}elseif($where=='dec'){
		echo '{"show":'.checkxqylogo($iid,$shop).'}';
	} 
  }
  
  public function checkrate(){
	  $iid = $this->spArgs('iid');
	  echo '{"show":'.checkrate(trim($iid)).'}';		
  }
  
   public function getvolume(){
	  $iid = $this->spArgs('iid');
	  $shop = $this->spArgs('shop');
	  echo '{"show":'.getvolume($iid,$shop).'}';		
  }
}
?>

