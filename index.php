<?php
define("SP_PATH",dirname(__FILE__).'/SpeedPHP');
define("APP_PATH",dirname(__FILE__));
date_default_timezone_set('Asia/Shanghai');
$spConfig = array(
	// 数据库配置
	'db'=>array(
		'host' => 'localhost',  // 数据库地址，一般都可以是localhost
                //'host' => 'www.yimiaofengqiang.com',
		'login' => 'root', // 数据库用户名
		//'password' => 'N]j]78R>jPKEML7edAC(',  // 数据库密码
		'password' => '',
		//'database' => 'xiai' // 数据库的库名称
		'database' => 'net37372922' // 数据库的库名称					
	),
	// smarty配置
	'view' => array(
		'enabled' => TRUE, // 开启Smarty
		'config' =>array(
			'template_dir' => APP_PATH.'/tpl', // 模板存放的目录
			'compile_dir' => APP_PATH.'/tmp', // 编译的临时目录
			'cache_dir' => APP_PATH.'/tmp', // 缓存的临时目录
			'left_delimiter' => '{',  // smarty左限定符
			'right_delimiter' => '}', // smarty右限定符
		)
	),
	// 伪静态配置
	'launch' => array( // 加入挂靠点，以便开始使用Url_ReWrite的功能
		'router_prefilter' => array(
			array('spUrlRewrite', 'setReWrite'),  // 对路由进行挂靠，处理转向地址
		),
		'function_url' => array(
			array("spUrlRewrite", "getReWrite"),  // 对spUrl进行挂靠，让spUrl可以进行Url_ReWrite地址的生成
		)
	),
	// Url重写配置
	'ext' => array(
		'spUrlRewrite' => array(
			'suffix' => '.html', // 生成地址的结尾符，网址后缀，可自由设置，如果“.do”或“.myphp”，该参数可为空，默认是.html。
			'sep' => '/', // 网址参数分隔符，建议是“-_/”之一
			'map' => array(	// 网址映射
				//'view'=>'main@view',
				'view'=>'main@view',
				//'user'=>'main@user',
				'iteminfo'=>'admin@getiteminfo',
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
				'search' => 'main@search', // 将使得 http://www.example.com/search.html 转向控制器main/动作serach执行
				'@' => 'main@no' // 1.在map中无法找到其他映射，2. 网址第一个参数并非控制器名称。
			),
			'args' => array( // 网址映射附加的隐藏参数，如果针对某个网址映射设置了隐藏参数，则在网址中仅会存在参数值，而参数名称被隐藏。
				// 生成的网址将会是：http://www.example.com/search-thekey-2.html
				// 这个网址将会执行 控制器main/动作serach，而参数q将等于thekey，参数page将等于2
				'search' => array('q','page'), 
											   
			)
		),

		// 康盛UCenter的设置		
		'spUcenter' => array(
			'UC_CLIENT_DIR' => "", // uc_client文件夹的目录，无需设置		
			'UC_CONNECT' => 'mysql', // 连接 UCenter 的方式: mysql/NULL, 默认为空时为 fscoketopen()
			// mysql 是直接连接的数据库, 为了效率, 建议采用 mysql
	
			//数据库相关 (mysql 连接时, 并且没有设置 UC_DBLINK 时, 需要配置以下变量)
			'UC_DBHOST' => 'localhost', // UCenter 数据库主机
			'UC_DBUSER' => 'root', // UCenter 数据库用户名
			'UC_DBPW' => '', // UCenter 数据库密码
			'UC_DBNAME' => 'ucenter', // UCenter 数据库名称
			'UC_DBCHARSET' => 'gbk', // UCenter 数据库字符集
			'UC_DBTABLEPRE' => 'ucenter.uc_', // UCenter 数据库表前缀，务必注意：最好在表前缀前加上库名
	
			//通信相关
			'UC_KEY' => 'YgergE52d7yUJ1EEHRYHKCFAS4wUW28lw8GUcUp1wiyitclPr46XR7xtOlwnm754', // 与 UCenter 的通信密钥, 要与 UCenter 保持一致
			'UC_API' => 'http://ucenter.com', // UCenter 的 URL 地址, 在调用头像时依赖此常量
			'UC_CHARSET' => 'gbk', // UCenter 的字符集
			'UC_IP' => '127.0.0.1', // UCenter 的 IP, 当 UC_CONNECT 为非 mysql 方式时, 并且当前应用服务器解析
			'UC_APPID' => 1 // 当前应用的 ID
		)
	),
	// 用户程序扩展类载入路径
	'include_path' => array(
		APP_PATH.'/include'
	),
	'html' => array(  // HTML生成配置
		'enabled' => TRUE, // 开启HTML生成功能
		//'file_root_name' => 'articles'
	),
	'mode'=>'release'
);
require(SP_PATH."/SpeedPHP.php");
import('md5password.php');
spRun(); 