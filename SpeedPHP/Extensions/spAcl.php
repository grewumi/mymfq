<?php
/////////////////////////////////////////////////////////////////
// SpeedPHP����PHP���, Copyright (C) 2008 - 2010 SpeedPHP.com //
/////////////////////////////////////////////////////////////////

define("SPANONYMOUS","SPANONYMOUS"); // ��Ȩ�����õĽ�ɫ����

/**
 * ��������û�Ȩ���жϻ���
 * Ҫʹ�ø�Ȩ�޿��Ƴ�����Ҫ��Ӧ�ó������������������ã�
 * ���޿��Ƶ��������������ʹ��	'launch' => array( 'router_prefilter' => array( array('spAcl','mincheck'), ), )
 * ǿ�ƿ��Ƶ��������������ʹ��	'launch' => array( 'router_prefilter' => array( array('spAcl','maxcheck'), ), )
 */
class spAcl
{
	/**
	 * Ĭ��Ȩ�޼��Ĵ���������ã������Ǻ������������飨array(����,����)����ʽ��
	 */
	public $checker = array('spAclModel','check');
	
	/**
	 * Ĭ����ʾ��Ȩ����ʾ�������Ǻ������������飨array(����,����)����ʽ��
	 */
	public $prompt = array('spAcl','def_prompt');
	/**
	 * ���캯��������Ȩ�޼���������ʾ����
	 */
	public function __construct()
	{	
		$params = spExt("spAcl");
		if( !empty($params["prompt"]) )$this->prompt = $params["prompt"];
		if( !empty($params["checker"]) )$this->checker = $params["checker"];
	}

	/**
	 * ��ȡ��ǰ�Ự���û���ʶ
	 */
	public function get()
	{
		return $_SESSION[$GLOBALS['G_SP']['sp_app_id']."_SpAclSession"];
	}

	/**
	 * ǿ�ƿ��Ƶļ����������ں�̨����Ȩ�޿��Ƶ�ҳ������ܽ���
	 */
	public function maxcheck()
	{
		$acl_handle = $this->check();
		if( 1 !== $acl_handle ){
			$this->prompt();
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * ���޵�Ȩ�޿��ƣ�������ǰ̨������Ȩ�ޱ�������ֹ��ҳ�������ã�����������ҳ����ɽ���
	 */
	public function mincheck()
	{
		$acl_handle = $this->check();
		if( 0 === $acl_handle ){
			$this->prompt();
			return FALSE;
		}
		return TRUE;
	}
	
	/**
	 * ʹ�ó�����������м��ȴ���
	 */
	private function check()
	{
		GLOBAL $__controller, $__action;
		$checker = $this->checker; $name = $this->get();

		if( is_array($checker) ){
			return spClass($checker[0])->{$checker[1]}($name, $__controller, $__action);
		}else{
			return call_user_func_array($checker, array($name, $__controller, $__action));
		}
	}
	/**
	 * ��Ȩ����ʾ��ת
	 */
	public function prompt()
	{
		$prompt = $this->prompt;
		if( is_array($prompt) ){
			return spClass($prompt[0])->{$prompt[1]}();
		}else{
			return call_user_func_array($prompt,array());
		}
	}
	
	/**
	 * Ĭ�ϵ���Ȩ����ʾ��ת
	 */
	public function def_prompt()
	{
		$url = spUrl(); // ��ת����ҳ����ǿ��Ȩ�޵�����£��뽫��ҳ�����óɿ��Խ��롣
		echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><script>function sptips(){alert(\"Access Failed!\");location.href=\"{$url}\";}</script></head><body onload=\"sptips()\"></body></html>";
		exit;
	}

	/**
	 * ���õ�ǰ�û����ڲ�ʹ��SESSION��¼
	 * 
	 * @param acl_name    �û���ʶ���������������û���
	 */
	public function set($acl_name)
	{
		$_SESSION[$GLOBALS['G_SP']['sp_app_id']."_SpAclSession"] = $acl_name;
	}
}

 /**
 * ACL�����࣬ͨ�����ݱ�ȷ���û�Ȩ��
 * ��ṹ��
 * CREATE TABLE acl
 * (
 * 	aclid int NOT NULL AUTO_INCREMENT,
 * 	name VARCHAR(200) NOT NULL,
 * 	controller VARCHAR(50) NOT NULL,
 * 	action VARCHAR(50) NOT NULL,
 * 	acl_name VARCHAR(50) NOT NULL,
 * 	PRIMARY KEY (aclid)
 * )  ENGINE=MyISAM DEFAULT CHARSET=gbk;
 */
class spAclModel extends spModel
{

	public $pk = 'aclid';
	/**
	 * ����
	 */
	public $table = 'acl';

	/**
	 * ����Ӧ��Ȩ��
	 *
	 * ����1��ͨ����飬0�ǲ���ͨ����飨���������������ڵ��û���ʶû�м�¼��
	 * ����-1���޸�Ȩ�޿��ƣ����ÿ�������������������Ȩ�ޱ��У�
	 * 
	 * @param acl_name    �û���ʶ�����������������û���
	 * @param controller    ����������
	 * @param action    ��������
	 */
	public function check($acl_name = SPANONYMOUS, $controller, $action)
	{
		$rows = array('controller' => $controller, 'action' => $action );
		if( $acl = $this->findAll($rows) ){
			foreach($acl as $v){
				if($v["acl_name"] == SPANONYMOUS || $v["acl_name"] == $acl_name)return 1;
			}
			return 0;
		}else{
			return -1;
		}
	}
}