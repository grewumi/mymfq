<?php
/////////////////////////////////////////////////////////////////
// SpeedPHP����PHP���, Copyright (C) 2008 - 2010 SpeedPHP.com //
/////////////////////////////////////////////////////////////////

/**
 * spController ���������������� Ӧ�ó����е�ÿ������������Ӧ�̳���spController
 */
class spController { 

	/**
	 * ��ͼ����
	 */
	public $v;
	
	/**
	 * ��ֵ��ģ��ı���
	 */
	private $__template_vals = array();
	
	/**
	 * ���캯��
	 */
	public function __construct()
	{	
		if(TRUE == $GLOBALS['G_SP']['view']['enabled']){
			$this->v = spClass('spView');
		}
	}

    /**
     *
     * ��ת����
     *
     * Ӧ�ó���Ŀ���������Ը��Ǹú�����ʹ���Զ������ת����
     *
     * @param $url  ��Ҫǰ���ĵ�ַ
     * @param $delay   �ӳ�ʱ��
     */
    public function jump($url, $delay = 0){
		echo "<html><head><meta http-equiv='refresh' content='{$delay};url={$url}'></head><body></body></html>";
		exit;
    }

    /**
     *
     * ������ʾ����
     *
     * Ӧ�ó���Ŀ���������Ը��Ǹú�����ʹ���Զ���Ĵ�����ʾ
     *
     * @param $msg   ������ʾ��Ҫ�������Ϣ
     * @param $url   ��ת��ַ
     */
    public function error($msg, $url = ''){
		$url = empty($url) ? "window.history.back();" : "location.href=\"{$url}\";";
		echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=gb2312\"><script>function sptips(){alert(\"{$msg}\");{$url}}</script></head><body onload=\"sptips()\"></body></html>";
		exit;
    }

    /**
     *
     * �ɹ���ʾ����
     *
     * Ӧ�ó���Ŀ���������Ը��Ǹú�����ʹ���Զ���ĳɹ���ʾ
	 *
     * @param $msg   �ɹ���ʾ��Ҫ�������Ϣ
     * @param $url   ��ת��ַ
     */
    public function success($msg, $url = ''){
		$url = empty($url) ? "window.history.back();" : "location.href=\"{$url}\";";
		echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=gb2312\"><script>function sptips(){alert(\"{$msg}\");{$url}}</script></head><body onload=\"sptips()\"></body></html>";
		exit;
    }

	/**
	 * ħ����������ȡ��ֵ��Ϊģ���ڱ���
	 */
	public function __set($name, $value)
	{
		if(TRUE == $GLOBALS['G_SP']['view']['enabled'] && false !== $value){
			$this->v->engine->assign(array($name=>$value));
		}
		$this->__template_vals[$name] = $value;
	}
	

	/**
	 * ħ�������������Ѹ�ֵ�ı���ֵ
	 */
	public function __get($name)
	{
		return $this->__template_vals[$name];
	}
	
	/**
	 * ���ģ��
	 *
     * @param $tplname   ģ��·��������
     * @param $output   �Ƿ�ֱ����ʾģ�壬���ó�FALSE������HTML�������
	 */
	public function display($tplname, $output = TRUE)
	{
		@ob_start();
		if(TRUE == $GLOBALS['G_SP']['view']['enabled']){
			$this->v->display($tplname);
		}else{
			extract($this->__template_vals);
			require($tplname);
		}
		if( TRUE != $output )return ob_get_clean();
	}
	
	/**
	 * �Զ����ҳ��
	 * @param tplname ģ���ļ�·��
	 */
	public function auto_display($tplname)
	{
		if( TRUE != $this->v->displayed && FALSE != $GLOBALS['G_SP']['view']['auto_display']){
			if( method_exists($this->v->engine, 'templateExists') && TRUE == $this->v->engine->templateExists($tplname))$this->display($tplname);
		}
	}

	/**
	 * ħ��������ʵ�ֶԿ�������չ����Զ�����
	 */
	public function __call($name, $args)
	{
		if(in_array($name, $GLOBALS['G_SP']["auto_load_controller"])){
			return spClass($name)->__input($args);
		}elseif(!method_exists( $this, $name )){
			spError("���� {$name}δ���壡<br />�����Ƿ��������(".get_class($this).")������ģ����������");
		}
	}

	/**
	 * ��ȡģ������ʵ��
	 */
	public function getView()
	{
		$this->v->addfuncs();
		return $this->v->engine;
	}
	/**
	 * ���õ�ǰ�û�������
     * @param $lang   ���Ա�ʶ
	 */
	public function setLang($lang)
	{
		if( array_key_exists($lang, $GLOBALS['G_SP']["lang"]) ){
			@ob_start();
			$domain = ('www.' == substr($_SERVER["HTTP_HOST"],0,4)) ? substr($_SERVER["HTTP_HOST"],4) : $_SERVER["HTTP_HOST"];
			setcookie($GLOBALS['G_SP']['sp_app_id']."_SpLangCookies", $lang, time()+31536000, '/', $domain ); // һ�����
			$_SESSION[$GLOBALS['G_SP']['sp_app_id']."_SpLangSession"] = $lang;
			return TRUE;
		}
		return FALSE;
	}
	/**
	 * ��ȡ��ǰ�û�������
	 */
	public function getLang()
	{
		if( !isset($_COOKIE[$GLOBALS['G_SP']['sp_app_id']."_SpLangCookies"]) )return $_SESSION[$GLOBALS['G_SP']['sp_app_id']."_SpLangSession"];
		return $_COOKIE[$GLOBALS['G_SP']['sp_app_id']."_SpLangCookies"];
	}
}

/**
 * spArgs 
 * Ӧ�ó��������
 * spArgs�Ƿ�װ��$_GET/$_POST��$_COOKIE�ȣ��ṩһЩ���ķ��ʺ�ʹ����Щ
 * ȫ�ֱ����ķ�����
 */

class spArgs {
	/**
	 * ���ڴ��б���ı���
	 */
	private $args = null;

	/**
	 * ���캯��
	 *
	 */
	public function __construct(){
		$this->args = $_REQUEST;
	}
	
	/**
	 * ��ȡӦ�ó����������ֵ��ͬʱҲ����ָ����ȡ�ı���������
	 * 
	 * @param name    ��ȡ�ı������ƣ����Ϊ�գ��򷵻�ȫ�����������
	 * @param default    ��ǰ��ȡ�ı��������ڵ�ʱ�򣬽����ص�Ĭ��ֵ
	 * @param method    ��ȡλ�ã�ȡֵGET��POST��COOKIE
	 */
	public function get($name = null, $default = FALSE, $method = null)
	{
		if(null != $name){
			if( $this->has($name) ){
				if( null != $method ){
					switch (strtolower($method)) {
						case 'get':
							return $_GET[$name];
						case 'post':
							return $_POST[$name];
						case 'cookie':
							return $_COOKIE[$name];
					}
				}
				return $this->args[$name];
			}else{
				return (FALSE === $default) ? FALSE : $default;
			}
		}else{
			return $this->args;
		}
	}

	/**
	 * ���ã����ӣ���������ֵ�������ƽ�����ԭ���Ļ�����������
	 * 
	 * @param name    ������������
	 * @param value    ��������ֵ
	 */
	public function set($name, $value)
	{
		$this->args[$name] = $value;
	}

	/**
	 * ����Ƿ����ĳֵ
	 * 
	 * @param name    �����Ļ�����������
	 */
	public function has($name)
	{
		return isset($this->args[$name]);
	}

	/**
	 * �������뺯������׼�÷�
	 * @param args    �����������ƵĲ���
	 */
	public function __input($args = -1)
	{
		if( -1 == $args )return $this;
		@list( $name, $default, $method ) = $args;
		return $this->get($name, $default, $method);
	}
	
	/**
	 * ��ȡ�����ַ�
	 */
	public function request(){
		return $_SERVER["QUERY_STRING"];
	}
}

