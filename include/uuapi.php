<?php
/**
	说明：此类函数是优优云图片识别平台的API接口,调用类中的函数可以进行图片识别
		 优优云官网：www.uuwise.com
		 QQ：87280085
	注意：使用之前，需要在本文件同一个目录下面建立一个everyone用户可读可写的临时图片存放目录，名称为：tempimg
	
	类中的公有函数：
		 setSoftInfo($softID,$softKey);				//设置软件ID和KEY
		 userLogin($userName,$passWord);			//用户登录,登录成功返回用户的ID
		 getPoint($userName,$passWord);				//获取用户剩余题分
		 upload($imagePath,$codeType);				//根据图片路径上传,返回验证码在服务器的ID,$codeType取值查看：http://www.uuwise.com/price.html
		 getResult($codeID);						//根据验证码ID获取识别结果
		 autoRecognition($imagePath,$codeType);		//将upload和getResult放到一个函数来执行,返回验证码识别结果
		 reportError($codeID);						//识别结果不正确报错误
		 regUser($userName,$userPassword)			//注册新用户,注册成功返回新用户的ID
		 pay($userName,$Card);						//充值题分，充值成功返回用户当前题分
	
	类中的公有变量：
		 $macAddress='00e021ac7d';					//客户机的mac地址,服务器暂时没有用,后期准备用于绑定mac地址		赋值方法： $obj->macAddress='00e021ac7d'; 
		 $timeOut='60000';							//超时时间,建议不要改动此值									赋值方法： $obj->timeOut=60000;
		 
	函数调用方法：
		 需要先new一个对象
		 $obj=new uuApi;
		 $obj->setSoftInfo('2097','b7ee76f547e34516bc30f6eb6c67c7db');	//如何获取这两个值？请查看这个页面：http://dll.uuwise.com/index.php?n=ApiDoc.GetSoftIDandKEY
		 $obj->userLogin('userName','userPassword');
		 $result=autoRecognition($imagePath,$codeType);
	错误代码：
		-9999  临时图片目录为空！ 需要在uuapi.php同目录下建立一个everyone用户可读写的tempimg目录
		-9998  CodeID不是数字
*/

class uuApi{
	
	private $softID;
	private $softKEY;
	private $userName;
	private $userPassword;
	
	private $uid;
	private $userKey;
	private $softContentKEY;
	
	private $dest_folder = "/tmp/";	//临时图片文件夹
	private $uuUrl;
	private $uhash;
	private $uuVersion='1.1.0.1';
	private $userAgent;
	private $gkey;
	private $sessionIsMatch=true;
	
	private $enablelog = true;			//是否启用日志功能，true为开启，false为关闭

	public $macAddress='00e021ac7d';	//客户机的mac地址,服务器暂时没有用,后期准备用于绑定mac地址		赋值方法： $obj->macAddress='00e021ac7d'; 
	public $timeOut=60000;				//超时时间,建议不要改动此值									赋值方法： $obj->timeOut=60000;
	
	public function setSoftInfo($id,$key)
	{
		if($id&&$key){
			$this->softID=$id;
			$this->softKEY=$key;
			$this->uhash=md5($id.strtoupper($key));
			return 'YES';
		}
		return 'NO';
	}
	private function iswriteable($file){
		if(is_dir($file)){
			$dir=$file;
			if($fp = @fopen("$dir/test.txt", 'w')){
				@fclose($fp);
				@unlink("$dir/test.txt");
				$writeable = true;
			}else{
				$writeable = false;
			}
		}else{
			if($fp = @fopen($file, 'a+')){
				@fclose($fp);
				$writeable = true;
			}else{
				$writeable = false;
			}
		}
		return $writeable;
	}
	private function getServerUrl($Server)
	{
		$url = "http://common.taskok.com:9000/Service/ServerConfig.aspx";
		$result=$this->uuGetUrl($url,array(),$postData=false);
		preg_match_all("/\,(.*?)\:101\,(.*?)\:102\,(.*?)\:103/", $result, $match_index);
		$arr=array_filter($match_index);
		if(empty($arr)){return '-1001';}
		switch($Server)
		{
			case 'service':
				return 'http://'.$match_index[1][0];
				break;
			case 'upload':
				return 'http://'.$match_index[2][0];
				break;
			case 'code':
				return 'http://'.$match_index[3][0];
				break;
			default:
				return '-1006';
				exit();
		}
		curl_close($this->uuUrl);
	}
	public function userLogin($userName,$passWord)
	{
		if(!($this->softID&&$this->softKEY))
		{
			return '-1';
		}
		if(!($userName&&$passWord)){ return '-1';}
		$this->userName=$userName;
		$this->userPassword=$passWord;
		$this->userAgent=md5(strtoupper($this->softKEY).strtoupper($this->userName)).$this->macAddress;
		
		@session_start();
		if(!(@$_SESSION['userKey'])){$this->sessionIsMatch=false;}
		if(!(@$_SESSION['uid'])){$this->sessionIsMatch=false;}

		if($this->sessionIsMatch){
			$this->userKey=$_SESSION['userKey'];
			$this->uid=$_SESSION['uid'];
			$this->softContentKEY=md5(strtolower($this->userKey.$this->softID.$this->softKEY));
			$this->gkey=md5(strtoupper($this->softKEY.$this->userName)).$this->macAddress;
			return $this->uid;
		}

		$url = $this->getServerUrl('service').'/Upload/Login.aspx?U='.$this->userName.'&P='.md5($this->userPassword).'&R='.mktime(time());
		$result=$this->uuGetUrl($url);
		if($result>0)
		{
			$this->userKey=$result;
			$_SESSION['userKey']=$this->userKey;
			$this->uid=explode("_",$this->userKey);
			$this->uid=$this->uid[0];
			$_SESSION['uid']=$this->uid;
			$this->softContentKEY=md5(strtolower($this->userKey.$this->softID.$this->softKEY));
			$this->gkey=md5(strtoupper($this->softKEY.$this->userName)).$this->macAddress;			
			return $this->uid;
		}
		return $result;

	}
	public function getPoint($userName,$passWord)
	{
		if(!($userName&&$passWord)){ return '-1';}
		if($this->softID<1){return '-1';}
		$url = $this->getServerUrl('service').'/Upload/GetScore.aspx?U='.$userName.'&P='.md5($passWord).'&R='.mktime(time()).'&random='.md5($userName.$this->softID);
		$result=$this->uuGetUrl($url);
		return $result;
	}
	public function upload($imageData,$codeType,$auth=false)
	{	
		//if(!file_exists($imageData["tmp_name"])){return '-1003';}
		//if(!$this->iswriteable($this->dest_folder)){ return '-9999';};

		$fname = $imageData["name"];
		$fname_array = explode('.',$fname);
		$extend = $fname_array[count($fname_array)-1];
		$uptypes = array(
			'image/jpg',
			'image/jpeg',
			'image/png',
			'image/pjpeg',
			'image/gif',
			'image/bmp',
			'image/x-png'
		);
		if(!in_array($imageData["type"],$uptypes)){return '-3007';}
		//$randval = date('Y-m-dgis').'-'.mt_rand(1000000,9999999);
		//$saveImgPath=$this->dest_folder.$randval.'.'.$extend;
		//move_uploaded_file($imageData["tmp_name"],$saveImgPath);	//修改文件
		//if(!file_exists(realpath($saveImgPath))){ return '-1003';};
		if(!is_numeric($codeType)){return '-3004';}
		$data=array(
			//'img'=>'@'.realpath($saveImgPath),
			'img'=>'@'.realpath('tmp/Checkcode.jpg'),
			'key'=>$this->userKey,
			'sid'=>$this->softID,
			'skey'=>$this->softContentKEY,
			'TimeOut'=>$this->timeOut,
			'Type'=>$codeType
		);
		$ver=array(
			'Version'=>'100',
		);

		if($auth){$data=$data+$ver;}
		$url = $this->getServerUrl('upload').'/Upload/Processing.aspx?R='.mktime(time());
		//print_r($data);
		$result=$this->uuGetUrl($url,$data);
		//@unlink($saveImgPath);	//删除上传的文件
		//@unlink('/tmp/Checkcode.jpg');
		return $result;
	}
	public function getResult($codeID)
	{
		if(!is_numeric($codeID)){return '-9998';}
		$url = $this->getServerUrl('code').'/Upload/GetResult.aspx?KEY='.$this->userKey.'&ID='.$codeID.'&Random='.mktime(time());
		$result='-3';
		$timer=0;
		while($result=='-3'&&($timer<$this->timeOut))
		{
			$result=$this->uuGetUrl($url,false,false);
			usleep(100000);	//一百毫秒一次
		}
		curl_close($this->uuUrl);
		if($result=='-3')
		{
			return '-1002';	
		}
		return $result;
	}
	public function autoRecognition($imagePath,$codeType)
	{
		$result=$this->upload($imagePath,$codeType,$auth=true);
		//echo $result;
		if($result>0){
			$arrayResult=explode("|",$result);
			if(!empty($arrayResult[1])){return $arrayResult[1];}
			return $this->getResult($result);
		}else{
			return -1;
		}
		//return $result;
	}
	private function uuGetUrl($url,$postData=false,$closeUrl=true)
	{
		$log=date('Y-m-d H:i:s').' 请求连接为：'.$url."\r\n";

		$uid=isset($this->uid)?($this->uid):'100';
		$default=array(
			'Accept: text/html, application/xhtml+xml, */*',
			'Accept-Language: zh-CN',
			'Connection: Keep-Alive',
			'Cache-Control: no-cache',
			'SID:'.$this->softID,
			'HASH:'.$this->uhash,
			'UUVersion:'.$this->uuVersion,
			'UID:'.$uid,
			'User-Agent:'.$this->userAgent,
			'KEY:'.$this->gkey,
		);
		
		$this->uuUrl = curl_init();
		curl_setopt($this->uuUrl, CURLOPT_HTTPHEADER, ($default));
		curl_setopt($this->uuUrl, CURLOPT_TIMEOUT, $this->timeOut);
		curl_setopt($this->uuUrl, CURLOPT_URL,$url);
		curl_setopt($this->uuUrl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->uuUrl, CURLOPT_HEADER, false);
		curl_setopt($this->uuUrl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->uuUrl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->uuUrl, CURLOPT_VERBOSE, false);
		curl_setopt($this->uuUrl, CURLOPT_AUTOREFERER, true);
		curl_setopt($this->uuUrl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->uuUrl, CURLOPT_HTTPGET, true);
		if($postData)
		{
			curl_setopt($this->uuUrl, CURLOPT_POST, true);
			curl_setopt($this->uuUrl, CURLOPT_POSTFIELDS, $postData);
		}

		$info=curl_exec($this->uuUrl);

		$log=$log.date('Y-m-d H:i:s').' 返回结果为为：'.$info."\r\n";
		$this->outlogs($log);
		if($info == false)
        {
			 //return "cURL Error (".curl_errno($this->uuUrl)."): ".curl_error($this->uuUrl)."\n"; //curl错误

			 $log=$log."cURL Error (".curl_errno($this->uuUrl)."): ".curl_error($this->uuUrl)."\r\n";
			 curl_close($this->uuUrl);
			 $this->outlogs($log);
			 return '-1002';
        }else {
			$this->outlogs($log);
			curl_close($this->uuUrl);
            return trim($info);
        }		
	}
	public function reportError($codeID)
	{
		if(!is_numeric($codeID)){return '-9998';}
		if($this->softContentKEY&&$this->userKey)
		{
			$url = $this->getServerUrl('code').'/Upload/ReportError.aspx?key='.$this->userKey.'&ID='.$codeID.'&sid='.$this->softID.'&skey='.$this->softContentKEY.'&R='.mktime(time());
			$result=$this->uuGetUrl($url);
			if($result=='OK')
			{
				return 'OK';	
			}
			return $result;
		}
		return '-1';
	}
	public function regUser($userName,$userPassword)
	{
		if($this->softID&&$this->softKEY)
		{
			if($userName&&$userPassword)
			{
				$data=array(
					'U'=>$userName,
					'P'=>$userPassword,
					'sid'=>$this->softID,
					'UKEY'=>md5(strtoupper($userName).$userPassword.$this->softID.strtolower($this->softKEY)),
				);
				$url=$this->getServerUrl('service').'/Service/Reg.aspx';
				return $this->uuGetUrl($url,$data);
			}
			return '-1';
		}
		return '-1';
	}
	
	public function pay($userName,$Card)
	{
		if($this->softID&&$this->softKEY)
		{
			if($userName&&$Card)
			{
				$data=array(
					'U'=>$userName,
					'card'=>$Card,
					'sid'=>$this->softID,
					'pkey'=>md5(strtoupper($userName).$this->softID.$this->softKEY.strtoupper($Card)),
				);
				$url=$this->getServerUrl('service').'/Service/Pay.aspx';
				return $this->uuGetUrl($url,$data);
			}
			return '-1';
		}
		return '-1';	
	}
	private function outlogs($str){
		if($this->enablelog){
			$logname=$this->dest_folder.'/UUWiselog'.date('Y-m-d').'.txt';
			if($this->iswriteable($logname)){
				$open=fopen($logname,"a" );
				fwrite($open,$str);
				fclose($open);
			}else{
				return '-9999';
			}
		}
	}
}
?>