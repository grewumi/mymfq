<?php
/////////////////////////////////////////////////////////////////
// SpeedPHP����PHP���, Copyright (C) 2008 - 2010 SpeedPHP.com //
/////////////////////////////////////////////////////////////////

/**
 * spRun  ִ���û�����
 */
function spRun(){
	GLOBAL $__controller, $__action;
	// ��·�ɽ����Զ�ִ����ز���
	spLaunch("router_prefilter");
	// �Խ�Ҫ���ʵĿ����������ʵ����
	$handle_controller = spClass($__controller, null, $GLOBALS['G_SP']["controller_path"].'/'.$__controller.".php");
	// ���ÿ�������������·�ɴ�������
	if(!is_object($handle_controller) || !method_exists($handle_controller, $__action)){
		eval($GLOBALS['G_SP']["dispatcher_error"]);
		exit;
	}
	// ·�ɲ�ִ���û�����
	$handle_controller->$__action();
	// ����������������ϣ�����ģ����Զ����
	if(FALSE != $GLOBALS['G_SP']['view']['auto_display']){
		$__tplname = $__controller.$GLOBALS['G_SP']['view']['auto_display_sep'].
				$__action.$GLOBALS['G_SP']['view']['auto_display_suffix']; // ƴװģ��·��
		$handle_controller->auto_display($__tplname);
	}
	// ��·�ɽ��к�����ز���
	spLaunch("router_postfilter");
}

/**
 * dump  ��ʽ�������������
 * 
 * @param vars    ����
 * @param output    �Ƿ��������
 * @param show_trace    �Ƿ�ʹ��spError�Ա�������׷�����
 */
function dump($vars, $output = TRUE, $show_trace = FALSE){
	// ����ģʽ��ͬʱ������鿴������Ϣ�������ֱ���˳���
	if(TRUE != SP_DEBUG && TRUE != $GLOBALS['G_SP']['allow_trace_onrelease'])return;
	if( TRUE == $show_trace ){ // ��ʾ��������·��
		$content = spError(htmlspecialchars(print_r($vars, true)), TRUE, FALSE);
	}else{
		$content = "<div align=left><pre>\n" . htmlspecialchars(print_r($vars, true)) . "\n</pre></div>\n";
	}
    if(TRUE != $output) { return $content; } // ֱ�ӷ��أ�������� 
       echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=gb2312\"></head><body>{$content}</body></html>"; 
	   return;
}

/**
 * import  ��������ļ�
 * 
 * @param filename    ��Ҫ������ļ��������ļ�·��
 * @param auto_search    �����ļ��Ҳ���ʱ�Ƿ�����ϵͳ·�����ļ�������·����˳��Ϊ��Ӧ�ó������Ŀ¼ -> Ӧ�ó���ModelĿ¼ -> sp��ܰ����ļ�Ŀ¼
 * @param auto_error    �Զ���ʾ��չ�����������Ϣ
 */
function import($sfilename, $auto_search = TRUE, $auto_error = FALSE){
	if(isset($GLOBALS['G_SP']["import_file"][md5($sfilename)]))return TRUE; // �Ѱ������룬����
	// ���$sfilename�Ƿ�ֱ�ӿɶ�
	if( TRUE == @is_readable($sfilename) ){
		require($sfilename); // �����ļ�
		$GLOBALS['G_SP']['import_file'][md5($sfilename)] = TRUE; // �Ը��ļ����б�ʶΪ������
		return TRUE;
	}else{
		if(TRUE == $auto_search){ // ��Ҫ�����ļ�
			// ����Ӧ�ó������Ŀ¼ -> Ӧ�ó���ModelĿ¼ -> sp��ܰ����ļ�Ŀ¼����˳�������ļ�
			foreach(array_merge( $GLOBALS['G_SP']['include_path'], array($GLOBALS['G_SP']['model_path']), $GLOBALS['G_SP']['sp_include_path'] ) as $sp_include_path){
				// ��鵱ǰ����·���У����ļ��Ƿ��Ѿ�����
				if(isset($GLOBALS['G_SP']["import_file"][md5($sp_include_path.'/'.$sfilename)]))return TRUE;
				if( is_readable( $sp_include_path.'/'.$sfilename ) ){
					require($sp_include_path.'/'.$sfilename);// �����ļ�
					$GLOBALS['G_SP']['import_file'][md5($sp_include_path.'/'.$sfilename)] = TRUE;// �Ը��ļ����б�ʶΪ������
					return TRUE;
				}
			}
		}
	}
	if( TRUE == $auto_error )spError("δ���ҵ���Ϊ��{$sfilename}���ļ�");
	return FALSE;
}

/**
 * spAccess ���ݻ��漰��ȡ����
 * 
 * @param method    ���ݴ�ȡģʽ��ȡֵ"w"Ϊ�������ݣ�ȡֵ"r"��ȡ���ݣ�ȡֵ"c"Ϊɾ������
 * @param name    ��ʶ���ݵ�����
 * @param value    �����ֵ���ڶ�ȡ���ݺ�ɾ�����ݵ�ģʽ�¾�ΪNULL
 * @param life_time    ����������ʱ�䣬Ĭ��Ϊ���ñ���
 */
function spAccess($method, $name, $value = NULL, $life_time = -1){
	// ʹ��function_access��չ��
	if( $launch = spLaunch("function_access", array('method'=>$method, 'name'=>$name, 'value'=>$value, 'life_time'=>$life_time), TRUE) )return $launch;
	// ׼������Ŀ¼�ͻ����ļ����ƣ������ļ�����Ϊ$name��MD5ֵ���ļ���׺Ϊphp
	if(!is_dir($GLOBALS['G_SP']['sp_cache']))__mkdirs($GLOBALS['G_SP']['sp_cache']);
	$sfile = $GLOBALS['G_SP']['sp_cache'].'/'.$GLOBALS['G_SP']['sp_app_id'].md5($name).".php";
	// ��$method�����жϣ��ֱ���ж�дɾ�Ĳ���
	if('w' == $method){ 
		// д���ݣ���$life_timeΪ-1��ʱ�򣬽�����$life_timeֵ����$life_time������
		$life_time = ( -1 == $life_time ) ? '300000000' : $life_time;
		// ׼�����뻺���ļ������ݣ������ļ�ʹ��PHP��die();�����Ա㱣֤���ݰ�ȫ��
		$value = '<?php die();?>'.( time() + $life_time ).serialize($value); // ���ݱ����л��󱣴�
		return file_put_contents($sfile, $value);
	}elseif('c' == $method){
		// ������ݣ�ֱ���Ƴ��Ļ����ļ�
		return @unlink($sfile);
	}else{
		// �����ݣ�����ļ��Ƿ�ɶ���ͬʱ��ȥ����������ǰ���������Է���
		if( !is_readable($sfile) )return FALSE;
		$arg_data = file_get_contents($sfile);
		// ��ȡ�ļ������$life_time����黺���Ƿ����
		if( substr($arg_data, 14, 10) < time() ){
			@unlink($sfile); // �������Ƴ������ļ�������FALSE
			return FALSE;
		}
		return unserialize(substr($arg_data, 24)); // ���ݷ����л��󷵻�
	}
}

/**
 * spClass  ��ʵ��������  �Զ������ඨ���ļ���ʵ���������ض�����
 * 
 * @param class_name    ������
 * @param args   ���ʼ��ʱʹ�õĲ�����������ʽ
 * @param sdir �����ඨ���ļ���·����������Ŀ¼+�ļ����ķ�ʽ��Ҳ���Ե�����Ŀ¼��sdir��ֵ������import()��������
 * @param force_inst �Ƿ�ǿ������ʵ��������
 */
function spClass($class_name, $args = null, $sdir = null, $force_inst = FALSE){
	// ����������Ƿ���ȷ���Ա�֤�ඨ���ļ�����İ�ȫ��
	if(preg_match('/[^a-z0-9\-_.]/i', $class_name))spError($class_name."�����ƴ������顣");
	// ����Ƿ�����Ѿ�ʵ������ֱ�ӷ�����ʵ�����󣬱����ٴ�ʵ����
	if(TRUE != $force_inst)if(isset($GLOBALS['G_SP']["inst_class"][$class_name]))return $GLOBALS['G_SP']["inst_class"][$class_name];
	// ���$sdir���ܶ�ȡ��������Ƿ��·��
	if(null != $sdir && !import($sdir) && !import($sdir.'/'.$class_name.'.php'))return FALSE;
	
	$has_define = FALSE;
	// ����ඨ���Ƿ����
	if(class_exists($class_name, false) || interface_exists($class_name, false)){
		$has_define = TRUE;
	}else{
		if( TRUE == import($class_name.'.php')){
			$has_define = TRUE;
		}
	}
	if(FALSE != $has_define){
		$argString = '';$comma = ''; 
		if(null != $args)for ($i = 0; $i < count($args); $i ++) { $argString .= $comma . "\$args[$i]"; $comma = ', '; } 
		eval("\$GLOBALS['G_SP']['inst_class'][\$class_name]= new \$class_name($argString);"); 
		return $GLOBALS['G_SP']["inst_class"][$class_name];
	}
	spError($class_name."�ඨ�岻���ڣ����顣");
}

/**
 * spError ��ܶ����ϵͳ��������ʾ
 * 
 * @param msg    ������Ϣ
 * @param output    �Ƿ����
 * @param stop    �Ƿ�ֹͣ����
 */
function spError($msg, $output = TRUE, $stop = TRUE){
	if($GLOBALS['G_SP']['sp_error_throw_exception'])throw new Exception($msg);
	if(TRUE != SP_DEBUG){error_log($msg);if(TRUE == $stop)exit;}
	$traces = debug_backtrace();
	$bufferabove = ob_get_clean();
	require_once($GLOBALS['G_SP']['sp_notice_php']);
	if(TRUE == $stop)exit;
}

/**
 * spLaunch  ִ����չ����
 * 
 * @param configname    ��չ�������õ�����
 * @param launchargs    ��չ����
 * @param return    �Ƿ���ڷ������ݣ�����Ҫ���أ������չ�������һ����չ����
 */
function spLaunch($configname, $launchargs = null, $returns = FALSE ){
	if( isset($GLOBALS['G_SP']['launch'][$configname]) && is_array($GLOBALS['G_SP']['launch'][$configname]) ){
		foreach( $GLOBALS['G_SP']['launch'][$configname] as $launch ){
			if( is_array($launch) ){
				$reval = spClass($launch[0])->{$launch[1]}($launchargs);
			}else{
				$reval = call_user_func_array($launch, $launchargs);
			}
			if( TRUE == $returns )return $reval;
		}
	}
	return false;
}
/**
 *
 * T
 *
 * ������ʵ�֣����뺯��
 *
 * @param w    Ĭ�����ԵĴ���
 *
 */
function T($w) {
	$method = $GLOBALS['G_SP']["lang"][spController::getLang()];
	if(!isset($method) || 'default' == $method){
		return $w;
	}elseif( function_exists($method) ){
		return ( $tmp = call_user_func($method, $w) ) ? $tmp : $w;
	}elseif( is_array($method) ){
		return ( $tmp = spClass($method[0])->{$method[1]}($w) ) ? $tmp : $w;
	}elseif( file_exists($method) ){
		$dict = require($method);
		return isset($dict[$w]) ? $dict[$w] : $w;
	}else{
		return $w;
	}
}

/**
 *
 * spUrl
 *
 * URLģʽ�Ĺ�������
 *
 * @param controller    ���������ƣ�Ĭ��Ϊ����'default_controller'
 * @param action    �������ƣ�Ĭ��Ϊ����'default_action' 
 * @param args    ���ݵĲ�����������ʽ
 * @param anchor    ��תê��
 * @param no_sphtml    �Ƿ�Ӧ��spHtml���ã���FALSEʱЧ���벻����spHtml��ͬ��
 */
function spUrl($controller = null, $action = null, $args = null, $anchor = null, $no_sphtml = FALSE) {
	if(TRUE == $GLOBALS['G_SP']['html']["enabled"] && TRUE != $no_sphtml){
		// ������HTML����ʱ��������HTML�б��ȡ��̬�ļ����ơ�
		$realhtml = spHtml::getUrl($controller, $action, $args, $anchor);if(isset($realhtml[0]))return $realhtml[0];
	}
	$controller = ( null != $controller ) ? $controller : $GLOBALS['G_SP']["default_controller"];
	$action = ( null != $action ) ? $action : $GLOBALS['G_SP']["default_action"];
	// ʹ����չ��
	if( $launch = spLaunch("function_url", array('controller'=>$controller, 'action'=>$action, 'args'=>$args, 'anchor'=>$anchor, 'no_sphtml'=>$no_sphtml), TRUE ))return $launch;
	if( TRUE == $GLOBALS['G_SP']['url']["url_path_info"] ){ // ʹ��path_info��ʽ
		$url = $GLOBALS['G_SP']['url']["url_path_base"]."/{$controller}/{$action}";
		if(null != $args)foreach($args as $key => $arg) $url .= "/{$key}/{$arg}";
	}else{
		$url = $GLOBALS['G_SP']['url']["url_path_base"]."?". $GLOBALS['G_SP']["url_controller"]. "={$controller}&";
		$url .= $GLOBALS['G_SP']["url_action"]. "={$action}";
		if(null != $args)foreach($args as $key => $arg) $url .= "&{$key}={$arg}";
	}
	if(null != $anchor) $url .= "#".$anchor;
	return $url;
}


/**
 * __mkdirs
 *
 * ѭ������Ŀ¼�ĸ�������
 *
 * @param dir    Ŀ¼·��
 * @param mode    �ļ�Ȩ��
 */
function __mkdirs($dir, $mode = 0777)
{
	if (!is_dir($dir)) {
		__mkdirs(dirname($dir), $mode);
		return @mkdir($dir, $mode);
	}
	return true;
}

/**
 * spExt
 *
 * ��չ���ȡ��չ���õĺ���
 *
 * @param ext_node_name    ��չ������
 */
function spExt($ext_node_name)
{
	return (empty($GLOBALS['G_SP']['ext'][$ext_node_name])) ? FALSE : $GLOBALS['G_SP']['ext'][$ext_node_name];
}

/**
 * spAddViewFunction
 *
 * ������ע�ᵽģ����ʹ�ã��ú��������Ƕ���ķ�������ķ������Ǻ�����
 *
 * @param alias    ������ģ���ڵı���
 * @param callback_function    �ص��ĺ����򷽷�
 */
function spAddViewFunction($alias, $callback_function)
{
	return $GLOBALS['G_SP']["view_registered_functions"][$alias] = $callback_function;
}

/**
 * spDB ������ȫ�ƣ�SpeedPHP DataBase���������ݿ�����ĺ�����
 *
 * spDB���Դﵽ��ʹ��spModel����Ŀ�ݷ�ʽ����û��spModel���ඨ�������£�ֱ�ӶԸñ�(spModelӵ�е�)������
 * spDB���ṩspModel����ļ��ʹ�÷�ʽ������Ҫǿ���ḻ��spModel���๦�ܣ�����Ȼ��������ж��岢ʹ�ø����ࡣ
 *
 * �����߿��Է���أ�
 * 1. ��ʼ��һ��spModel�����࣬��ʹ�������Ķ��岻����
 * 2. ���øö���ļ̳�spModel������ȫ������
 *
 * @param tbl_name    ��ȫ�� �� �����ƣ������߿��������е�db_spdb_full_tblname���÷����Լ�ʹ��ϰ�ߵķ�ʽ��
 *                    ��ȫ����Ĭ��ֵ��db_spdb_full_tblname = true��tbl_nameֵ���ǣ���ǰ׺ + �����ƣ�
 *                    �����ƣ�db_spdb_full_tblname = false����ʱ���ܽ�ʹ��db�����еı�ǰ׺prefix��
 * @param pk    ��������ѡ��������������ʱ�򣬽���ȡ���һ���ֶ���Ϊ������ͨ�����ǣ�
 */
function spDB($tbl_name, $pk = null){
	$modelObj = spClass("spModel");
	$modelObj->tbl_name = (TRUE == $GLOBALS['G_SP']["db_spdb_full_tblname"]) ? $tbl_name :	$GLOBALS['G_SP']['db']['prefix'] . $tbl_name;
	if( !$pk ){ // ����ͨ�����ݿ�����getTable����ȡ
		@list($pk) = $modelObj->_db->getTable($modelObj->tbl_name);$pk = $pk['Field'];
	}
	$modelObj->pk = $pk;
	return $modelObj;
}

/**
 * json_decode/json_encode
 *
 * ������δ����JSON��չ�������ʹ��Services_JSON��
 *
 */
if ( !function_exists('json_decode') ){
	function json_decode($content, $assoc=false){
		if ( $assoc ){
			return spClass("Services_JSON", array(16))->decode($content);
		} else {
			return spClass("Services_JSON")->decode($content);
		}
	}
}
if ( !function_exists('json_encode') ){
    function json_encode($content){return spClass("Services_JSON")->encode($content);}
}

/**
 * spConfigReady   ���ٽ��û����ø��ǵ����Ĭ������
 * 
 * @param preconfig    Ĭ������
 * @param useconfig    �û�����
 */
function spConfigReady( $preconfig, $useconfig = null){
	$nowconfig = $preconfig;
	if (is_array($useconfig)){
		foreach ($useconfig as $key => $val){
			if (is_array($useconfig[$key])){
				@$nowconfig[$key] = is_array($nowconfig[$key]) ? spConfigReady($nowconfig[$key], $useconfig[$key]) : $useconfig[$key];
			}else{
				@$nowconfig[$key] = $val;
			}
		}
	}
	return $nowconfig;
}