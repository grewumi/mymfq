<?php
/////////////////////////////////////////////////////////////////
// SpeedPHP����PHP���, Copyright (C) 2008 - 2010 SpeedPHP.com //
/////////////////////////////////////////////////////////////////

/**
 * spView ������ͼ��
 */
class spView {
	/**
	 * ģ������ʵ��
	 */
	public $engine = null;
	/**
	 * ģ���Ƿ������
	 */
	public $displayed = FALSE;

	/**
	 * ���캯��������ģ�������ʵ��������
	 */
	public function __construct()
	{
		if(FALSE == $GLOBALS['G_SP']['view']['enabled'])return FALSE;
		if(FALSE != $GLOBALS['G_SP']['view']['auto_ob_start'])ob_start();
		$this->engine = spClass($GLOBALS['G_SP']['view']['engine_name'],null,$GLOBALS['G_SP']['view']['engine_path']);
		if( $GLOBALS['G_SP']['view']['config'] && is_array($GLOBALS['G_SP']['view']['config']) ){
			$engine_vars = get_class_vars(get_class($this->engine));
			foreach( $GLOBALS['G_SP']['view']['config'] as $key => $value ){
				if( array_key_exists($key,$engine_vars) )$this->engine->{$key} = $value;
			}
		}
		if( !empty($GLOBALS['G_SP']['sp_app_id']) && isset($this->engine->compile_id) )$this->engine->compile_id = $GLOBALS['G_SP']['sp_app_id'];
		// ������Ŀ¼�Ƿ��д
		if( empty($this->engine->no_compile_dir) && (!is_dir($this->engine->compile_dir) || !is_writable($this->engine->compile_dir)))__mkdirs($this->engine->compile_dir);
		spAddViewFunction('T', array( 'spView', '__template_T'));
		spAddViewFunction('spUrl', array( 'spView', '__template_spUrl'));
	}

	/**
	 * ���ҳ��
	 * @param tplname ģ���ļ�·��
	 */
	public function display($tplname)
	{
		try {
				$this->addfuncs();
				$this->displayed = TRUE;
				if($GLOBALS['G_SP']['view']['debugging'] && SP_DEBUG)$this->engine->debugging = TRUE;
				$this->engine->display($tplname);
		} catch (Exception $e) {
			spError( $GLOBALS['G_SP']['view']['engine_name']. ' Error: '.$e->getMessage() );
		}
	}
	
	/**
	 * ע����ͼ����
	 */
	public function addfuncs()
	{
		if( is_array($GLOBALS['G_SP']["view_registered_functions"]) ){
			foreach( $GLOBALS['G_SP']["view_registered_functions"] as $alias => $func ){
				if( is_array($func) && !is_object($func[0]) )$func = array(spClass($func[0]),$func[1]);
				$this->engine->registerPlugin("function", $alias, $func);
				unset($GLOBALS['G_SP']["view_registered_functions"][$alias]);
			}
		}
	}
	/**
	 * ����spUrl�ĺ�������spUrl����ģ����ʹ�á�
	 * @param params ����Ĳ���
	 */
	public function __template_spUrl($params)
	{
		$controller = $GLOBALS['G_SP']["default_controller"];
		$action = $GLOBALS['G_SP']["default_action"];
		$args = array();
		$anchor = null;
		foreach($params as $key => $param){
			if( $key == $GLOBALS['G_SP']["url_controller"] ){
				$controller = $param;
			}elseif( $key == $GLOBALS['G_SP']["url_action"] ){
				$action = $param;
			}elseif( $key == 'anchor' ){
				$anchor = $param;
			}else{
				$args[$key] = $param;
			}
		}
		return spUrl($controller, $action, $args, $anchor);
	}
	/**
	 * ����T�ĺ�������T����ģ����ʹ�á�
	 * @param params ����Ĳ���
	 */
	public function __template_T($params)
	{
		return T($params['w']);
	}
}

/**
 * spHtml
 * ��̬HTML������
 */
class spHtml
{
	private $spurls = null;
	/**
	 * ���ɵ�����̬ҳ��
	 * 
	 * @param spurl spUrl�Ĳ���
	 * @param alias_url ����HTML�ļ������ƣ����������alias_url����ʹ������������Ŀ¼�������Ϊ�ļ�������ʽ����HTML�ļ���
	 * @param update_mode    ����ģʽ��Ĭ��2Ϊͬʱ�����б��ļ�
	 * 0�ǽ������б�
	 * 1�ǽ������ļ�
	 */
	public function make($spurl, $alias_url = null, $update_mode = 2)
	{
		if(1 == spAccess('r','sp_html_making')){$this->spurls[] = array($spurl, $alias_url); return;}
		@list($controller, $action, $args, $anchor) = $spurl;
		if( $url_item = spHtml::getUrl($controller, $action, $args, $anchor, TRUE) ){
			@list($baseuri, $realfile) = $url_item;$update_mode = 1;
		}else{
			$file_root_name = ( '' == $GLOBALS['G_SP']['html']['file_root_name'] ) ? 
									'' : $GLOBALS['G_SP']['html']['file_root_name'].'/';
			if( null == $alias_url ){
				$filedir = $file_root_name .date('Y/n/d').'/';
				$filename = substr(time(),3,10).substr(mt_rand(100000, substr(time(),3,10)),4).".html";
			}else{
				$filedir = $file_root_name.dirname($alias_url) . '/';
				$filename = basename($alias_url);
			}
			$baseuri = rtrim(dirname($GLOBALS['G_SP']['url']["url_path_base"]), '/\\')."/".$filedir.$filename;
			$realfile = APP_PATH."/".$filedir.$filename;
		}
		if( 0 == $update_mode or 2 == $update_mode )spHtml::setUrl($spurl, $baseuri, $realfile);
		if( 1 == $update_mode or 2 == $update_mode ){
			$remoteurl = 'http://'.$_SERVER["SERVER_NAME"].':'.$_SERVER['SERVER_PORT'].
										'/'.ltrim(spUrl($controller, $action, $args, $anchor, TRUE), '/\\');
			$cachedata = file_get_contents($remoteurl);
			if( FALSE === $cachedata ){
				$cachedata = $this->curl_get_file_contents($remoteurl);
				if( FALSE === $cachedata )spError("�޷��������ȡҳ�����ݣ����飺<br />1. spUrl���ɵ�ַ�Ƿ���ȷ��<a href='{$remoteurl}' target='_blank'>����������</a>��<br />2. ����php.ini��allow_url_fopenΪOn��<br />3. ����Ƿ����ǽ��ֹ��APACHE/PHP�������硣<br />4. ���鰲װCURL�����⡣");
			}
			__mkdirs(dirname($realfile));
			file_put_contents($realfile, $cachedata);
		}
	}
	
	/**
	 * ��file_get_contentsʧЧʱ�����򽫵���CURL�����������������ݻ�ȡ
	 * @param url ���ʵ�ַ
	 */
	function curl_get_file_contents($url)
    {
    	if(!function_exists('curl_init'))return FALSE;
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $url);
        $contents = curl_exec($c);
        curl_close($c);
        if (FALSE === $contents)return FALSE;
        return $contents;
    }
	
	/**
	 * �������ɾ�̬ҳ��
	 * @param spurls ������ʽ��ÿ����һ��make()��ȫ������
	 */
	public function makeAll($spurls)
	{
		foreach( $spurls as $single ){
			list($spurl, $alias_url) = $single;
			$this->make($spurl, $alias_url, 0);
		}
		foreach( $spurls as $single ){
			list($spurl, $alias_url) = $single;
			$this->make($spurl, $alias_url, 1);
		}
	}
	
	public function start(){spAccess('w','sp_html_making',1);$this->spurls = null;}
	public function commit(){spAccess('c','sp_html_making');$this->makeAll($this->spurls);}

	/**
	 * ��ȡurl���б���򣬿��԰����ÿ����Ƿ����ļ�����
	 * @param controller    ���������ƣ�Ĭ��Ϊ����'default_controller'
	 * @param action    �������ƣ�Ĭ��Ϊ����'default_action' 
	 * @param args    ���ݵĲ�����������ʽ
	 * @param anchor    ��תê��
	 * @param force_no_check    �Ƿ��������ļ��Ƿ����
	 */
	public function getUrl($controller = null, $action = null, $args = null, $anchor = null, $force_no_check = FALSE)
	{
		if( $url_list = spAccess('r', 'sp_url_list') ){
			$url_list = explode("\n",$url_list);
			$args_en = !empty($args) ? json_encode($args) : "";
			$url_input = "{$controller}|{$action}|{$args_en}|$anchor|";
			foreach( $url_list as $url ){
				if( substr($url,0,strlen($url_input)) == $url_input ){
					$url_item = explode("|",substr($url,strlen($url_input)));
					if( TRUE == $GLOBALS['G_SP']['html']['safe_check_file_exists'] && TRUE != $force_no_check ){
						if( !is_readable($url_item[1]) )return FALSE;
					}
					return $url_item;
				}
			}
		}
		return FALSE;
	}
	
	/**
	 * д��url���б������make����ҳ��󣬽�spUrl������ҳ���ַд���б���
	 *
	 * @param spurl spUrl�Ĳ���
	 * @param baseuri URL��ַ��Ӧ�ľ�̬HTML�ļ����ʵ�ַ
     *
	 */
	public function setUrl($spurl, $baseuri, $realfile)
	{
		@list($controller, $action, $args, $anchor) = $spurl;
		$this->clear($controller, $action, $args, $anchor, FALSE);
		$args = !empty($args) ? json_encode($args) : '';
		$url_input = "{$controller}|{$action}|{$args}|{$anchor}|{$baseuri}|{$realfile}";
		if( $url_list = spAccess('r', 'sp_url_list') ){
			spAccess('w', 'sp_url_list', $url_list."\n".$url_input);
		}else{
			spAccess('w', 'sp_url_list', $url_input);
		}
	}

	/**
	 * �����̬�ļ�
	 * 
	 * @param controller    ��Ҫ���HTML�ļ��Ŀ���������
	 * @param action    ��Ҫ���HTML�ļ��Ķ������ƣ�Ĭ��Ϊ����ÿ�����ȫ������������HTML�ļ�
	 * ���������action���������action������HTML�ļ�
	 *
	 * @param args    ���ݵĲ�����Ĭ��Ϊ�ս�����ö����κβ���������HTML�ļ�
	 * ���������args��������ö���ִ�в���args��������HTML�ļ�
	 *
	 * @param anchor    ��תê�㣬Ĭ��Ϊ�ս�����ö����κ�ê�������HTML�ļ�
	 * ���������anchor��������ö�����ת��ê��anchor������HTML�ļ�
	 *
	 * @param delete_file    �Ƿ�ɾ�������ļ���FALSE��ֻɾ���б��иþ�̬�ļ��ĵ�ַ������ɾ�������ļ���
	 */
	public function clear($controller, $action = null, $args = FALSE, $anchor = '', $delete_file = TRUE)
	{
		if( $url_list = spAccess('r', 'sp_url_list') ){
			$url_list = explode("\n",$url_list);$re_url_list = array();
			if( null == $action ){
				$prep = "{$controller}|";
			}elseif( FALSE === $args ){
				$prep = "{$controller}|{$action}|";
			}else{
				$args = !empty($args) ? json_encode($args) : '';
				$prep = "{$controller}|{$action}|{$args}|{$anchor}|";
			}
			foreach( $url_list as $url ){
				if( substr($url,0,strlen($prep)) == $prep ){
					$url_tmp = explode("|",$url);$realfile = $url_tmp[5];
					if( TRUE == $delete_file )@unlink($realfile);
				}else{
					$re_url_list[] = $url;
				}
			}
			spAccess('w', 'sp_url_list', join("\n", $re_url_list));
		}
	}
	

	/**
	 * ���ȫ����̬�ļ�
	 * 
	 * @param delete_file    �Ƿ�ɾ�������ļ���FALSH��ֻɾ���б��иþ�̬�ļ��ĵ�ַ������ɾ�������ļ���
	 */
	public function clearAll($delete_file = FALSE)
	{
		if( TRUE == $delete_file ){
			if( $url_list = spAccess('r', 'sp_url_list') ){
				$url_list = explode("\n",$url_list);
				foreach( $url_list as $url ){
					$url_tmp = explode("|",$url);$realfile = $url_tmp[5];
					@unlink($realfile);
				}
			}
		}
		spAccess('c', 'sp_url_list');
	}
}