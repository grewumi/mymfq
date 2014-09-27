<?php
/**
 *
 * spUcenter
 *
 * 康盛UCENTER系统（http://www.comsenz.com/products/ucenter）的API接口程序
 *
 * spUcenter 封装了对康盛UCENTER系统API接口操作，可为应用程序提供以下功能：
 *
 * 提供同步登录、退出、注册等相关接口，可以实现用户一个账号，在一处登录，全站通行。 
 * 提供短消息相关接口，可以实现用户在不同应用收发短消息。 
 * 提供 TAG 相关接口，可以实现通过关键词关联各应用的数据，如帖子、商品、视频，使数据多元化。 
 * 提供 Feed 相关接口，可以实现记录用户在各应用的行为，并且在 UCenter Home 等应用显示。 
 * 提供好友相关接口，可以实现各应用好友互通。 
 * 提供积分策略共享，可以实现各应用设置灵活的积分策略。 
 * 提供词语过滤数据共享，可以实现各应用共享 UCenter 的词语过滤数据。 
 * 提供 MySQL 和 HTTP 两种连接用户中心的模式，使之能将 UCenter 和各应用能灵活部署，可以是单台服务器，也可以是局域网、广域网。 
 * 通知失败重发机制，为用户中心与各应用交换数据提供更好的保障。 
 * 缓存、模板、日志等机制，保障系统在稳定，优化状态下运行。
 *
 */
class spUcenter {
	/**
	 * UCENTER设置参数
	 */
	protected $params = array(
		
		'UC_CLIENT_DIR' => "", // uc_client文件夹的目录
		
		'UC_CONNECT' => NULL, 	// 连接 UCenter 的方式: mysql/NULL, 默认为空时为 fscoketopen()
									// mysql 是直接连接的数据库, 为了效率, 建议采用 mysql
	
		//数据库相关 (mysql 连接时, 并且没有设置 UC_DBLINK 时, 需要配置以下变量)
		'UC_DBHOST' => 'localhost',	// UCenter 数据库主机
		'UC_DBUSER' => 'root',		// UCenter 数据库用户名
		'UC_DBPW'   => '',	// UCenter 数据库密码
		'UC_DBNAME' => 'ucenter',	// UCenter 数据库名称
		'UC_DBCHARSET' => 'utf8',			// UCenter 数据库字符集
		'UC_DBTABLEPRE' => 'ucenter.uc_',	// UCenter 数据库表前缀，务必注意：最好在表前缀前加上库名
	
		//通信相关
		'UC_KEY' => '123456789', 				// 与 UCenter 的通信密钥, 要与 UCenter 保持一致
		'UC_API' => 'http://yourwebsite/uc_server', 	// UCenter 的 URL 地址, 在调用头像时依赖此常量
		'UC_CHARSET' => 'utf8', 	// UCenter 的字符集
		'UC_IP'  => '',  // UCenter 的 IP, 当 UC_CONNECT 为非 mysql 方式时, 并且当前应用服务器解析
		'UC_APPID' => 1 					// 当前应用的 ID
	);

	/**
	 * 构造函数，将处理UCENTER载入等操作
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
	 * @param func    调用函数名
	 * @param args    参数
	 */
	public function __call($func, $args)
	{
		if( 'uc_' == substr($func, 0, 3) &&  function_exists($func) ){
			return call_user_func_array ( $func, $args);
		}
		spError('无法找到该方法，请查阅本扩展及UCENTER的相关手册');
	}
}
?>
