<?php
/////////////////////////////////////////////////////////////////
// SpeedPHP����PHP���, Copyright (C) 2008 - 2010 SpeedPHP.com //
/////////////////////////////////////////////////////////////////

/**
 * spUrlRewrite �࣬����չ��ʽ֧��SpeedPHP���URL_REWRITE����չ��
 *
 * ����չ��ʹ�ã�����Ҫȷ������������URL_REWRITE���ܣ�������.htaccess���Ѿ������µ�����
 *
 * .htaccess����Ե�ǰӦ�ó����
 *
 * <IfModule mod_rewrite.c>
 * RewriteEngine On
 * RewriteCond %{REQUEST_FILENAME} !-f
 * RewriteCond %{REQUEST_FILENAME} !-d
 * RewriteRule ^(.*)$ index.php?$1 [L]
 * </IfModule>
 *
 * ����չҪ��SpeedPHP���2.5�汾���ϣ���֧�ֶ�spUrl��������չ����
 *
 * Ӧ�ó�����������Ҫʹ�õ�·����չ���Լ�spUrl��չ��
 * 'launch' => array( 
 *	 	'router_prefilter' => array( 
 *			array('spUrlRewrite', 'setReWrite'), 
 *		),
 *  	'function_url' => array(
 *			array("spUrlRewrite", "getReWrite"),
 * 	    ),
 *),
 *
 * ��spUrlRewrite������
 *
 * 'ext' => array(
 * 		'spUrlRewrite' => array(
 *			'suffix' => '.html', // ���ɵ�ַ�Ľ�β������ַ��׺
 *			'sep' => '/', // ��ַ�����ָ����������ǡ�-_/��֮һ
 *			'map' => array( // ��ַӳ�䣬���� 'search' => 'main@search'��
 *							// ��ʹ�� http://www.example.com/search.html ת�������main/����serachִ��
 *							// ���� '@' => 'main@no' ���ӳ����@����ʹ�÷���������������ַת�� ������main/����noִ�У�
 *							// 1. ��map���޷��ҵ�����ӳ�䣬2. ��ַ��һ���������ǿ��������ơ�			
 *			),
 *			'args' => array( // ��ַӳ�丽�ӵ����ز�����������ĳ����ַӳ�����������ز�����������ַ�н�����ڲ���ֵ�����������Ʊ����ء�
 *						 	 // ���� 'search' => array('q','page'), ��ô���ɵ���ַ�����ǣ�
 *							 // http://www.example.com/search-thekey-2.html
 *							 // ���mapӳ��'search' => 'main@search'�������ַ����ִ�� ������main/����serach��
 *							 // ������q������thekey������page������2
 *			),
 *		),
 * ),
 *
 */
if( SP_VERSION < 2.5 )spError('spUrlRewrite��չҪ��SpeedPHP��ܰ汾2.5���ϡ�');
class spUrlRewrite
{
	var $params = array(
		// 'hide_default' => true, // ����Ĭ�ϵ�main/index���ƣ�����Ч
		// 'args_path_info' => false, // ��ַ�����Ƿ�ʹ��path_infoģʽ������Ч��ȫΪ��path_info��ģʽ
		'suffix' => '.html',
		'sep' => '-',
		'map' => array(
		),
		'args' => array(
		),
	);
	/**
	 * ���캯������������
	 */
	public function __construct()
	{
		$params = spExt('spUrlRewrite');
		if(is_array($params))$this->params = array_merge($this->params, $params);
	}	
	/**
	 * �ڿ�����/����ִ��ǰ����·�ɽ��и�װ��ʹ����Խ���URL_WRITE�ĵ�ַ
	 */
	public function setReWrite()
	{
		GLOBAL $__controller, $__action;
		if(isset($_SERVER['HTTP_X_REWRITE_URL']))$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
		// $request = ltrim(strtolower(substr($_SERVER["REQUEST_URI"], strlen(dirname($GLOBALS['G_SP']['url']['url_path_base'])))),"\/\\");
		$request = ltrim(substr($_SERVER["REQUEST_URI"], strlen(dirname($GLOBALS['G_SP']['url']['url_path_base']))),"\/\\");
		if( '?' == substr($request, 0, 1) or 'index.php?' == substr($request, 0, 10) )return ;
		if( empty($request) or 'index.php' == $request ){
			$__controller = $GLOBALS['G_SP']['default_controller'];
			$__action = $GLOBALS['G_SP']['default_action'];
			return ;
		}
		$request = explode((( '' == $this->params['suffix'] )?'?':$this->params['suffix']), $request, 2);
		$uri = array('first' => array_shift($request),'last' => ltrim(implode($request),'?'));
		$request = explode($this->params['sep'], $uri['first']);
		$uri['first'] = array('pattern' => array_shift($request),'args'  => $request);
		
		if( array_key_exists($uri['first']['pattern'], $this->params['map']) ){
			@list($__controller, $__action) = explode('@',$this->params['map'][$uri['first']['pattern']]);
			if( !empty($this->params['args'][$uri['first']['pattern']]) )foreach( $this->params['args'][$uri['first']['pattern']] as $v )spClass("spArgs")->set($v, array_shift($uri['first']['args']));
		}elseif( isset($this->params['map']['@']) && !in_array($uri['first']['pattern'].'.php', array_map('strtolower',scandir($GLOBALS['G_SP']['controller_path']))) ){
			@list($__controller, $__action) = explode('@',$this->params['map']['@']);
			if( !empty($this->params['args']['@']) ){
				$uri['first']['args'] = array_merge(array($uri['first']['pattern']), $uri['first']['args']);
				foreach( $this->params['args']['@'] as $v )spClass("spArgs")->set($v, array_shift($uri['first']['args']));
			}
		}else{
			$__controller = $uri['first']['pattern'];$__action = array_shift($uri['first']['args']);
			if( empty($__action) )$__action = $GLOBALS['G_SP']['default_action'];
		}
		if(!empty($uri['first']['args']))for($u = 0; $u < count($uri['first']['args']); $u++){
			spClass("spArgs")->set($uri['first']['args'][$u], isset($uri['first']['args'][$u+1])?$uri['first']['args'][$u+1]:"");
			$u+=1;}
		if(!empty($uri['last'])){
			$uri['last'] = explode('&',$uri['last']);
			foreach( $uri['last'] as $val ){
				@list($k, $v) = explode('=',$val);if(!empty($k))spClass("spArgs")->set($k,isset($v)?$v:"");}}
	}


	/**
	 * �ڹ���spUrl��ַʱ���Ե�ַ����URL_WRITE�ĸ�д
	 *
	 * @param urlargs    spUrl�Ĳ���
	 */
	public function getReWrite($urlargs = array())
	{
		$uri = trim(dirname($GLOBALS['G_SP']['url']["url_path_base"]),"\/\\");
		if( empty($uri) ){$uri = '/';}else{$uri = '/'.$uri.'/';}
		if( $GLOBALS['G_SP']["default_controller"] == $urlargs['controller'] && $GLOBALS['G_SP']["default_action"] == $urlargs['action'] && empty($urlargs['args']) ){
			return $uri.((null != $urlargs['anchor']) ? "#{$anchor}" : '');
		}elseif( $k = array_search(strtolower($urlargs['controller'].'@'.$urlargs['action']), array_map('strtolower',$this->params['map']))){
			$uri .= ('@'==$k)?'':$k;$isfirstmark = ('@'==$k);
			if( !empty( $this->params['args'][$k] ) && !empty($urlargs['args']) ){
				foreach( $this->params['args'][$k] as $defarg ){
					if( $isfirstmark ){
						$uri .= isset($urlargs['args'][$defarg]) ? $urlargs['args'][$defarg] : '';$isfirstmark = 0;
					}else{
						$uri .= isset($urlargs['args'][$defarg]) ? $this->params['sep'].$urlargs['args'][$defarg] : $this->params['sep'];
					}
					unset($urlargs['args'][$defarg]);
				}
			}
		}else{
			$uri .= $urlargs['controller'];
			if( !empty($urlargs['args']) || (!empty($urlargs['action']) && $urlargs['action'] != $GLOBALS['G_SP']["default_action"]) )$uri .= $this->params['sep'].$urlargs['action'];
		}
		if( !empty($urlargs['args']) ){
			foreach($urlargs['args'] as $k => $v)$uri.= $this->params['sep'].$k.$this->params['sep'].$v;
		}else{
			$uri = rtrim($uri, $this->params['sep']);
		}
		return $uri.$this->params['suffix'] .((null != $urlargs['anchor']) ? "#{$anchor}" : '');
	}
}