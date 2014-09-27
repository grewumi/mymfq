<?php
/////////////////////////////////////////////////////////////////
// SpeedPHP����PHP���, Copyright (C) 2008 - 2010 SpeedPHP.com //
/////////////////////////////////////////////////////////////////

/**
 * spAccessCache �࣬����չ��ʽ֧��spAccess����ӵ�и���Ļ��淽ʽ����չ��
 *
 * ĿǰspAccessCache֧�ֵĻ��������������£�
 *
 * Xcache���������ƣ�xcache)
 * Memcache (memcache)
 * APC (apc)
 * eAccelerator (eaccelerator)
 * SAE��memcache (saememcache)
 * ʹ�����ݿ���Ϊ���� (db)
 *
 * ��ע�⣺Memcache��db ������������������ã���ο���ע�͡�
 *
 * Ӧ�ó�����������Ҫʹ�õ�·����չ���Լ�spAccess��չ��
 * 'launch' => array( 
 *  	'function_access' => array(
 *			array("spAccessCache", "xcache"), // �ڶ�������Ϊ�����������͵�����
 * 	    ),
 *),
 * 
 * ����չҪ��SpeedPHP���2.5�汾���ϣ���֧�ֶ�spAccess��������չ����
 */
if( SP_VERSION < 2.5 )spError('spAccessCache��չҪ��SpeedPHP��ܰ汾2.5���ϡ�');
class spAccessCache{
	/**
	 * ħ������  ͨ�������������ò�ͬ�Ļ���������
	 */
	public function __call($name, $args){
		$driverClass = 'access_driver_'.$name;
		if(!class_exists($driverClass))spError('spAccess�޷��ҵ���Ϊ{$name}����������������!');
		extract(array_pop($args));
		if('w' == $method){ // д����
			$life_time = ( -1 == $life_time ) ? '300000000' : $life_time;
			return spClass($driverClass)->set($name, serialize($value), $life_time);
		}elseif('c' == $method){ // �������
			return spClass($driverClass)->del($name);
		}else{ // ������
			return unserialize(spClass($driverClass)->get($name));
		}
	}
}

/**
 * access_driver_memcache  memcache����������
 *
 * memcache��������Ĭ�������� localhost:11211���������������֮����ͬ�������������ã�
 * 'ext' => array(
 * 		'spAccessCache' => array(
 *			'memcache_host' => '123.456.789.10', // memcache��������ַ
 * 			'memcache_port' => '1111', // memcache�������˿�
 *		),
 * ),
 */
class access_driver_memcache{
	public $mmc = null;
	public function __construct(){
		if(!function_exists('memcache_connect'))spError('PHP����δ��װMemcache�����⣡');
		$params = spExt('spAccessCache');
		$memcache_host = (isset($params['memcache_host'])) ? $params['memcache_host'] : 'localhost';
		$memcache_port = (isset($params['memcache_port'])) ? $params['memcache_port'] : '11211';
		$this->mmc = memcache_connect($memcache_host, $memcache_port);
	}
	public function get($name){return memcache_get($this->mmc, $name);}
	public function set($name, $value, $life_time){return memcache_set($this->mmc, $name, $value, 0, $life_time);}
	public function del($name){return memcache_delete($this->mmc, $name);}
}

/**
 * access_driver_saememcache  SAE��memcache����������
 */
class access_driver_saememcache{
	public $mmc = null;
	public function __construct(){if( ! $this->mmc = memcache_init() )spError("SAE��memcache��ʼ��ʧ�ܣ�");}
	public function get($name){return memcache_get($this->mmc, $name);}
	public function set($name, $value, $life_time){return memcache_set($this->mmc, $name, $value, 0, $life_time);}
	public function del($name){return memcache_delete($this->mmc, $name);}
}

/**
 * access_driver_apc  APC����������
 */
class access_driver_apc{
	public function __construct(){if(!function_exists('apc_store'))spError('PHP����δ��װAPC�����⣡');}
	public function get($name){return apc_fetch($name);}
	public function set($name, $value, $life_time){return apc_store($name, $value, $life_time);}
	public function del($name){return apc_delete($name);}
}

/**
 * access_driver_eaccelerator  eAccelerator����������
 */
class access_driver_eaccelerator{
	public function __construct(){if(!function_exists('eaccelerator_put'))spError('PHP����δ��װeAccelerator�����⣡');}
	public function get($name){return eaccelerator_get($name);}
	public function set($name, $value, $life_time){return eaccelerator_put($name, $value, $life_time);}
	public function del($name){return eaccelerator_rm($name);}
}

/**
 * access_driver_xcache  Xcache����������
 */
class access_driver_xcache{
	public function __construct(){if(!function_exists('xcache_set'))spError('PHP����δ��װXcache�����⣡');}
	public function get($name){return xcache_get($name);}
	public function set($name, $value, $life_time){return xcache_set($name, $value, $life_time);}
	public function del($name){return xcache_unset($name);}
}

/**
 * access_driver_db  ���ݿ⻺��������
 *
 * access_driver_db�����ÿ�����ʹ�����ݿⱾ����Ϊ����������
 *
 * ��ʹ�� access_driver_db ֮ǰ����ؽ�����Ӧ�� access_cache ���ݱ�
 *
 * ���ɱ���䣺
 * CREATE TABLE `access_cache` (
 *   `cacheid` bigint(20) NOT NULL AUTO_INCREMENT,
 *   `cachename` varchar(100) NOT NULL,
 *   `cachevalue` text,
 *   PRIMARY KEY (`cacheid`)
 * ) ENGINE=MyISAM DEFAULT CHARSET=gbk;
 *
 */
class access_driver_db extends spModel{
	public $pk = 'cacheid';
	public $table = 'access_cache';
	public function get($name){
		if(! $result = array_pop($this->find(array('cachename'=>$name),'cacheid DESC','cachevalue')) )return FALSE;
		if( substr($result, 0, 10) < time() ){$this->del($name);return FALSE;}
		return unserialize(substr($result, 10));
	}
	public function set($name, $value, $life_time){
		$value = ( time() + $life_time ).serialize($value);
		if( FALSE !== $this->find(array('cachename'=>$name),'cacheid DESC','cachevalue') ){
			return $this->updateField(array('cachename'=>$name), 'cachevalue', $value);
		}else{
			return $this->create(array('cachename'=>$name, 'cachevalue'=>$value));
		}
	}
	public function del($name){return $this->delete(array('cachename'=>$name));}
}