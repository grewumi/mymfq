<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$spConfig = array(
	// ���ݿ�����
	'db'=>array(
		'host' => 'localhost',  // ���ݿ��ַ��һ�㶼������localhost
		'login' => 'root', // ���ݿ��û���
		'password' =>$dbpasswd ,  // ���ݿ�����
		'database' => 'yimiaofengqiang' // ���ݿ�Ŀ�����			
	),
        'url' => array(
                'url_path_info' => FALSE, // �Ƿ�ʹ��path_info��ʽ��URL
                'url_path_base' => '/index.php', // URL�ĸ�Ŀ¼���ʵ�ַ
        ),
	// smarty����
	'view' => array(
		'enabled' => TRUE, // ����Smarty
		'config' =>array(
			'template_dir' => APP_PATH.'/tpl', // ģ���ŵ�Ŀ¼
			'compile_dir' => APP_PATH.'/tmp', // �������ʱĿ¼
			'cache_dir' => APP_PATH.'/tmp', // �������ʱĿ¼
			'left_delimiter' => '{',  // smarty���޶���
			'right_delimiter' => '}', // smarty���޶���
		)
	),
	// α��̬����
	'launch' => array( // ����ҿ��㣬�Ա㿪ʼʹ��Url_ReWrite�Ĺ���
		'router_prefilter' => array(
			array('spUrlRewrite', 'setReWrite'),  // ��·�ɽ��йҿ�������ת���ַ
		),
		'function_url' => array(
			array("spUrlRewrite", "getReWrite"),  // ��spUrl���йҿ�����spUrl���Խ���Url_ReWrite��ַ������
		)
	),
	// Url��д����
	'ext' => array(
		'spUrlRewrite' => array(
			'suffix' => '.html', // ���ɵ�ַ�Ľ�β������ַ��׺�����������ã������.do����.myphp�����ò�����Ϊ�գ�Ĭ����.html��
			'sep' => '/', // ��ַ�����ָ����������ǡ�-_/��֮һ
			'map' => array(	// ��ַӳ��
				'view'=>'main@view',
//				'user'=>'main@user',
				'outitems'=>'main@outitems',
                                'search' => 'main@search', // ��ʹ�� http://www.example.com/search.html ת�������main/����serachִ��
				'@' => 'main@no', // 1.��map���޷��ҵ�����ӳ�䣬2. ��ַ��һ���������ǿ��������ơ�
                            
				'iteminfo'=>'admin@getiteminfo',
				'xuqi'=>'admin@xuqi',
				'postDataToUz'=>'admin@postDataToUz',
				'delpro'=>'admin@delpro',
				'delgq'=>'admin@delgq',
				'checkpro'=>'admin@checkpro',
				'mailindex'=>'main@mailindex',
				'admin' => 'admin@index',
				'login' => 'admin@login',
				'pro' => 'admin@pro',
				'addpro' => 'admin@addpro',
				'modpro' => 'admin@modpro',
				'proget' => 'admin@proget',
				'uzcaijiapi' => 'admin@uzcaijiapi',
				'uzcaiji' => 'admin@uzcaiji',
				'yjuzcaiji' => 'admin@yjuzcaiji',
				'yonghu' => 'admin@yonghu',
				'link' => 'admin@link',
				'ad' => 'admin@ad',
				'tkreport' => 'admin@tkreport',
				'dbselect' => 'admin@dbselect',
				'sqlout' => 'admin@sqlout',
				'updateyj' => 'admin@updateyj',
				'updateyjonce' => 'admin@updateyjonce',					
			),
			'args' => array( // ��ַӳ�丽�ӵ����ز�����������ĳ����ַӳ�����������ز�����������ַ�н�����ڲ���ֵ�����������Ʊ����ء�
				// ���ɵ���ַ�����ǣ�http://www.example.com/search-thekey-2.html
				// �����ַ����ִ�� ������main/����serach��������q������thekey������page������2
				'search' => array('q','page'), 
											   
			)
		),

		// ��ʢUCenter������		
		'spUcenter' => array(
			'UC_CLIENT_DIR' => "", // uc_client�ļ��е�Ŀ¼����������		
			'UC_CONNECT' => 'mysql', // ���� UCenter �ķ�ʽ: mysql/NULL, Ĭ��Ϊ��ʱΪ fscoketopen()
			// mysql ��ֱ�����ӵ����ݿ�, Ϊ��Ч��, ������� mysql
	
			//���ݿ���� (mysql ����ʱ, ����û������ UC_DBLINK ʱ, ��Ҫ�������±���)
			'UC_DBHOST' => 'localhost', // UCenter ���ݿ�����
			'UC_DBUSER' => 'root', // UCenter ���ݿ��û���
			'UC_DBPW' => $dbpasswd, // UCenter ���ݿ�����
			'UC_DBNAME' => 'ucenter', // UCenter ���ݿ�����
			'UC_DBCHARSET' => 'gbk', // UCenter ���ݿ��ַ���
			'UC_DBTABLEPRE' => 'ucenter.uc_', // UCenter ���ݿ��ǰ׺�����ע�⣺����ڱ�ǰ׺ǰ���Ͽ���
	
			//ͨ�����
			'UC_KEY' => 'YgergE52d7yUJ1EEHRYHKCFAS4wUW28lw8GUcUp1wiyitclPr46XR7xtOlwnm754', // �� UCenter ��ͨ����Կ, Ҫ�� UCenter ����һ��
			'UC_API' => $ucapi,
			'UC_CHARSET' => 'gbk', // UCenter ���ַ���
			'UC_IP' => '127.0.0.1', // UCenter �� IP, �� UC_CONNECT Ϊ�� mysql ��ʽʱ, ���ҵ�ǰӦ�÷���������
			'UC_APPID' => 1 // ��ǰӦ�õ� ID
		)
	),
	// �û�������չ������·��
	'include_path' => array(
		APP_PATH.'/include'
	),
	'html' => array(  // HTML��������
		'enabled' => TRUE, // ����HTML���ɹ���
	),
	'mode' => 'release',
	'dispatcher_error' => "import(APP_PATH.'/404.html');exit();",
	'supe_uid' => '',
	'SC' => array(
		'cookiepre' => 'ymfq_', //COOKIEǰ׺
		'cookiedomain' => '', //COOKIE������
		'cookiepath' => '/', //COOKIE����·��
	),
	'timestamp' => time(),
	'spVerifyCode' => array( //��֤����չ
		'width' => 60, //��֤����
		'height' => 20, //��֤��߶�
		'length' => 4, //��֤���ַ�����
		'bgcolor' => '#FFFFFF', //����ɫ
		'noisenum' => 50, //ͼ���������
		'fontsize' => 22, //�����С
		'fontfile' => 'font.ttf', //�����ļ�
		'format' => 'gif', //��֤�����ͼƬ��ʽ
	),
        'ajaxToUz' => array(
            'addpro'=>true,
            'modpro'=>true,
            'delpro'=>true
        ),
        'autocat'=>true
);

if(LOCALDEVELOP){
    $spConfig['ajaxToUz'] = null;
}

?>