<?php
/////////////////////////////////////////////////////////////////
// SpeedPHP����PHP���, Copyright (C) 2008 - 2010 SpeedPHP.com //
/////////////////////////////////////////////////////////////////

/**
 * spConfig
 *
 * SpeedPHPӦ�ÿ�ܵ�ϵͳĬ������
 */

return array(
	'mode' => 'debug', // Ӧ�ó���ģʽ��Ĭ��Ϊ����ģʽ
	'sp_core_path' => SP_PATH.'/Core', // ���MVC����Ŀ¼
	'sp_drivers_path' => SP_PATH.'/Drivers', // ��ܸ��������ļ�Ŀ¼
	'sp_include_path' => array( SP_PATH.'/Extensions' ), // �����չ��������·��
	'launch' => array(), // �Զ�ִ�е�ĸ��ڵ�
	
	'auto_load_controller' => array('spArgs'), // �������Զ����ص���չ����
	'auto_load_model' => array('spPager','spVerifier','spCache','spLinker'), // ģ���Զ����ص���չ����
	
	'sp_error_show_source' => 5, // spError��ʾ���������
	'sp_error_throw_exception' => FALSE, // �Ƿ��׳��쳣
	'allow_trace_onrelease' => FALSE, // �Ƿ������ڲ���ģʽ�����������Ϣ
	'sp_notice_php' => SP_PATH."/Misc/notice.php", // ���Ĭ�ϵĴ�����ʾ����
	
	'inst_class' => array(), // ��ʵ������������
	'import_file' => array(), // �Ѿ�������ļ�
	'sp_access_store' => array(), // ʹ��spAccess���浽�ڴ�ı���
	'view_registered_functions' => array(), // ��ͼ��ע��ĺ�����¼

	'default_controller' => 'main', // Ĭ�ϵĿ���������
	'default_action' => 'index',  // Ĭ�ϵĶ�������
	'url_controller' => 'c',  // ����ʱʹ�õĿ�����������ʶ
	'url_action' => 'a',  // ����ʱʹ�õĶ���������ʶ

	'auto_session' => TRUE, // �Ƿ��Զ�����SESSION֧��
	'dispatcher_error' => "spError('·�ɴ������������Ŀ¼���Ƿ���ڸÿ�����/������');", // ���崦��·�ɴ���ĺ���
	'auto_sp_run' => FALSE, // �Ƿ��Զ�ִ��spRun����
	
	'sp_cache' => APP_PATH.'/tmp', // �����ʱ�ļ���Ŀ¼
	'sp_app_id' => '',  // ���ʶ��ID
	'controller_path' => APP_PATH.'/controller', // �û������������·������
	'model_path' => APP_PATH.'/model', // �û�ģ�ͳ����·������


	'url' => array( // URL����
		'url_path_info' => FALSE, // �Ƿ�ʹ��path_info��ʽ��URL
		'url_path_base' => '', // URL�ĸ�Ŀ¼���ʵ�ַ��Ĭ��Ϊ����������ļ�index.php
	),
	
	'db' => array(  // ���ݿ���������
		'driver' => 'mysql',   // ��������
		'host' => 'localhost', // ���ݿ��ַ
		'port' => 3306,        // �˿�
		'login' => 'root',     // �û���
		'password' => '',      // ����
		'database' => '',      // ������
		'prefix' => '',           // ��ǰ׺
		'persistent' => FALSE,    // �Ƿ�ʹ�ó�����
	),
	'db_driver_path' => '', // �Զ������ݿ������ļ���ַ
	'db_spdb_full_tblname' => TRUE, // spDB�Ƿ�ʹ�ñ�ȫ��
	
	'view' => array( // ��ͼ����
		'enabled' => TRUE, // ������ͼ
		'config' =>array(
			'template_dir' => APP_PATH.'/tpl', // ģ��Ŀ¼
			'compile_dir' => APP_PATH.'/tmp', // ����Ŀ¼
			'cache_dir' => APP_PATH.'/tmp', // ����Ŀ¼
			'left_delimiter' => '{',  // smarty���޶���
			'right_delimiter' => '}', // smarty���޶���
			'auto_literal' => TRUE, // Smarty3������
		),
		'debugging' => FALSE, // �Ƿ�����ͼ���Թ��ܣ��ڲ���ģʽ���޷�������ͼ���Թ���
		'engine_name' => 'Smarty', // ģ������������ƣ�Ĭ��ΪSmarty
		'engine_path' => SP_PATH.'/Drivers/Smarty/Smarty.class.php', // ģ����������·��
		'auto_ob_start' => TRUE, // �Ƿ��Զ����������������
		'auto_display' => FALSE, // �Ƿ�ʹ���Զ����ģ�幦��
		'auto_display_sep' => '/', // �Զ����ģ���ƴװģʽ��/Ϊ��Ŀ¼��ʽƴװ��_Ϊ���»��߷�ʽ���Դ�����
		'auto_display_suffix' => '.html', // �Զ����ģ��ĺ�׺��
	),
		
	'html' => array( 
		'enabled' => FALSE, // �Ƿ�����ʵ��̬HTML�ļ�������
		'file_root_name' => 'topic', // ��̬�ļ����ɵĸ�Ŀ¼���ƣ�����Ϊ������ֱ��������ļ���ͬ��Ŀ¼����
		'safe_check_file_exists' => FALSE, // ��ȡURLʱ���������HTML�ļ��Ƿ���ڣ����ļ������ڣ��򷵻ذ�ȫ�Ķ�̬��ַ
	),
	
	'lang' => array(), // ���������ã�����ÿ�����Ե����ƣ���ֵ������default��Ĭ�����ԣ��������ļ���ַ�����Ƿ��뺯��
					// ͬʱ��ע�⣬��ʹ�������ļ������ļ��д������ĵ�ʱ���뽫�ļ����ó�UTF8����
	'ext' => array(), // ��չʹ�õ����ø�Ŀ¼
		
	'include_path' => array(
		APP_PATH.'/include',
	), // �û�������չ������·��
);
