<?php
/////////////////////////////////////////////////////////////////
// SpeedPHP����PHP���, Copyright (C) 2008 - 2010 SpeedPHP.com //
/////////////////////////////////////////////////////////////////

$__speedy_compression_level = 9;

/**
 * speedy �򵥵�PHPģ�����棬��ͨ��PHP��������Ϊģ����﷨���ÿ����߳���Smarty��ģ������֮�⣬������һ�����ٲ��Ҽ򵥵�ģ�����淽����
 *
 * speedyӵ�еĹ��ܣ���ģ��·������ȡģ�岢��ʾ��ͨ��assign��ģ���ڱ������и�ֵ�����ģ���ļ��Ƿ���ڣ�GZipѹ���ȡ�
 */
class speedy{
	/**
	 * ģ��Ŀ¼
	 */
	public $template_dir = null;
	/**
	 * �Ƿ���GZipѹ��
	 */
	public $enable_gzip	= FALSE;
	/**
	 * GZipѹ������
	 */
	public $compression_level	=  9;
	/**
	 * ��������Ŀ¼
	 */
	public $no_compile_dir = true;
	/**
	 * ģ����ʹ�õı���ֵ
	 */
	private $_vars = array();
	
	/**
	 * ��ģ�帳ֵ
	 * @param key �������ƣ����������
	 * @param value ����ֵ
	 */
	public function assign($key, $value = null){
		if (is_array($key)){
			foreach($key as $var => $val)if($var != "")$this->_vars[$var] = $val;
		}else{
			if ($key != "")$this->_vars[$key] = $value;
		}
	}
	
	/**
	 * ���ģ���Ƿ����
	 * @param tplname ģ������
	 */	
	public function templateExists($tplname){
		if (is_readable(realpath($this->template_dir).'/'.$tplname))return TRUE;
		if (is_readable($tplname))return TRUE;
		return FALSE;
	}
	
	/**
	 * templateExists ����,���ģ���Ƿ����
	 * @param tplname ģ������
	 */	
	public function template_exists($tplname){return $this->templateExists($tplname);}
	/** ����Smarty3*/
	public function registerPlugin(){}
	
	/**
	 * ��ʾģ��
	 * @param tplname ģ������
	 */	
	public function display($tplname){
		if(is_readable(realpath($this->template_dir).'/'.$tplname)){
			$tplpath = realpath($this->template_dir).'/'.$tplname;
		}elseif(is_readable($tplname)){
			$tplpath = $tplname;
		}else{
			spError("speedy���棺�޷��ҵ�ģ�� ".$tplname);
		}
		extract($this->_vars);
		if( TRUE == $this->enable_gzip ){
			GLOBAL $__speedy_compression_level;
			$__speedy_compression_level = $this->compression_level;
			ob_start('speedy_ob_gzip');
		}
		include $tplpath;
	}
	
}

function speedy_ob_gzip($content){ 
	if( !headers_sent() && extension_loaded("zlib") && strstr($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip") ){
		GLOBAL $__speedy_compression_level;
		$content = gzencode($content,$__speedy_compression_level); 
		header("Content-Encoding: gzip"); 
		header("Vary: Accept-Encoding"); 
		header("Content-Length: ".strlen($content)); 
	} 
	return $content; 
} 