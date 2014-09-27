<?php
/**
 *
 * spUcenter
 *
 * ��ʢUCENTERϵͳ��http://www.comsenz.com/products/ucenter����API�ӿڳ���
 *
 * spUcenter ��װ�˶Կ�ʢUCENTERϵͳAPI�ӿڲ�������ΪӦ�ó����ṩ���¹��ܣ�
 *
 * �ṩͬ����¼���˳���ע�����ؽӿڣ�����ʵ���û�һ���˺ţ���һ����¼��ȫվͨ�С� 
 * �ṩ����Ϣ��ؽӿڣ�����ʵ���û��ڲ�ͬӦ���շ�����Ϣ�� 
 * �ṩ TAG ��ؽӿڣ�����ʵ��ͨ���ؼ��ʹ�����Ӧ�õ����ݣ������ӡ���Ʒ����Ƶ��ʹ���ݶ�Ԫ���� 
 * �ṩ Feed ��ؽӿڣ�����ʵ�ּ�¼�û��ڸ�Ӧ�õ���Ϊ�������� UCenter Home ��Ӧ����ʾ�� 
 * �ṩ������ؽӿڣ�����ʵ�ָ�Ӧ�ú��ѻ�ͨ�� 
 * �ṩ���ֲ��Թ�������ʵ�ָ�Ӧ���������Ļ��ֲ��ԡ� 
 * �ṩ����������ݹ�������ʵ�ָ�Ӧ�ù��� UCenter �Ĵ���������ݡ� 
 * �ṩ MySQL �� HTTP ���������û����ĵ�ģʽ��ʹ֮�ܽ� UCenter �͸�Ӧ�������𣬿����ǵ�̨��������Ҳ�����Ǿ��������������� 
 * ֪ͨʧ���ط����ƣ�Ϊ�û��������Ӧ�ý��������ṩ���õı��ϡ� 
 * ���桢ģ�塢��־�Ȼ��ƣ�����ϵͳ���ȶ����Ż�״̬�����С�
 *
 */
class spUcenter {
	/**
	 * UCENTER���ò���
	 */
	protected $params = array(
		
		'UC_CLIENT_DIR' => "", // uc_client�ļ��е�Ŀ¼
		
		'UC_CONNECT' => NULL, 	// ���� UCenter �ķ�ʽ: mysql/NULL, Ĭ��Ϊ��ʱΪ fscoketopen()
									// mysql ��ֱ�����ӵ����ݿ�, Ϊ��Ч��, ������� mysql
	
		//���ݿ���� (mysql ����ʱ, ����û������ UC_DBLINK ʱ, ��Ҫ�������±���)
		'UC_DBHOST' => 'localhost',	// UCenter ���ݿ�����
		'UC_DBUSER' => 'root',		// UCenter ���ݿ��û���
		'UC_DBPW'   => '',	// UCenter ���ݿ�����
		'UC_DBNAME' => 'ucenter',	// UCenter ���ݿ�����
		'UC_DBCHARSET' => 'utf8',			// UCenter ���ݿ��ַ���
		'UC_DBTABLEPRE' => 'ucenter.uc_',	// UCenter ���ݿ��ǰ׺�����ע�⣺����ڱ�ǰ׺ǰ���Ͽ���
	
		//ͨ�����
		'UC_KEY' => '123456789', 				// �� UCenter ��ͨ����Կ, Ҫ�� UCenter ����һ��
		'UC_API' => 'http://yourwebsite/uc_server', 	// UCenter �� URL ��ַ, �ڵ���ͷ��ʱ�����˳���
		'UC_CHARSET' => 'utf8', 	// UCenter ���ַ���
		'UC_IP'  => '',  // UCenter �� IP, �� UC_CONNECT Ϊ�� mysql ��ʽʱ, ���ҵ�ǰӦ�÷���������
		'UC_APPID' => 1 					// ��ǰӦ�õ� ID
	);

	/**
	 * ���캯����������UCENTER����Ȳ���
	 */
	public function __construct()
	{
		$params = ( false != spExt("spUcenter") ) ? array_merge($this->params, spExt("spUcenter")) : $this->params;
		$params['UC_CLIENT_DIR'] = ("" != $params['UC_CLIENT_DIR']) ? $params['UC_CLIENT_DIR'] : SP_PATH."/Extensions/uc_client";

		define('UC_CONNECT', $params['UC_CONNECT']);
		define('UC_DBHOST', $params['UC_DBHOST']);
		define('UC_DBUSER', $params['UC_DBUSER']);				
		define('UC_DBPW', $params['UC_DBPW']);					
		define('UC_DBNAME', $params['UC_DBNAME']);	
		define('UC_DBCHARSET', $params['UC_DBCHARSET']);
		define('UC_DBTABLEPRE', $params['UC_DBTABLEPRE']);		
		
		define('UC_KEY', $params['UC_KEY']);
		define('UC_API', $params['UC_API']);
		define('UC_CHARSET', $params['UC_CHARSET']);
		define('UC_IP', $params['UC_IP']);	
		define('UC_APPID', $params['UC_APPID']);
		
		import(rtrim($params['UC_CLIENT_DIR'], '/\\') . "/client.php");
	}

	/**
	 * 
	 * @param func    ���ú�����
	 * @param args    ����
	 */
	public function __call($func, $args)
	{
		if( 'uc_' == substr($func, 0, 3) &&  function_exists($func) ){
			return call_user_func_array ( $func, $args);
		}
		spError('�޷��ҵ��÷���������ı���չ��UCENTER������ֲ�');
	}
}
?>
