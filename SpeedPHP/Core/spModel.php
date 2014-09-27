<?php
/////////////////////////////////////////////////////////////////
// SpeedPHP����PHP���, Copyright (C) 2008 - 2010 SpeedPHP.com //
/////////////////////////////////////////////////////////////////

/**
 * spModel ϵͳģ���࣬����ģ����ĸ��� Ӧ�ó����е�ÿ��ģ���඼Ӧ�̳���spModel��
 */
class spModel {
	/**
	 * ������ֵ�Ĺ����뷵����Ϣ
	 */
	public $verifier = null;
	
	/**
	 * ���ӵ��Զ�����֤����
	 */
	public $addrules = array();
	/**
	 * ������
	 */
	public $pk;
	/**
	 * ������
	 */
	public $table;

	/**
	 * ��������
	 */
	public $linker = null;
	
	/**
	 * ��ȫ��
	 */
	public $tbl_name = null;
	
	/**
	 * ������������
	 */
	public $_db;

	/**
	 * ���캯��
	 */
	public function __construct()
	{
		if( null == $this->tbl_name )$this->tbl_name = $GLOBALS['G_SP']['db']['prefix'] . $this->table;
		if( '' == $GLOBALS['G_SP']['db_driver_path'] ){
			$GLOBALS['G_SP']['db_driver_path'] = $GLOBALS['G_SP']['sp_drivers_path'].'/'.$GLOBALS['G_SP']['db']['driver'].'.php';
		}
		$this->_db = spClass('db_'.$GLOBALS['G_SP']['db']['driver'], array(0=>$GLOBALS['G_SP']['db']), $GLOBALS['G_SP']['db_driver_path']);
	}

	/**
	 * �����ݱ��в���һ����¼
	 *
	 * @param conditions    ��������������array("�ֶ���"=>"����ֵ")���ַ�����
	 * ��ע����ʹ���ַ���ʱ����Ҫ����������ʹ��escape��������ֵ���й���
	 * @param sort    ���򣬵�ͬ�ڡ�ORDER BY ��
	 * @param fields    ���ص��ֶη�Χ��Ĭ��Ϊ����ȫ���ֶε�ֵ
	 */
	public function find($conditions = null, $sort = null, $fields = null)
	{
		if( $record = $this->findAll($conditions, $sort, $fields, 1) ){
			return array_pop($record);
		}else{
			return FALSE;
		}
	}
	
	/**
	 * �����ݱ��в��Ҽ�¼
	 *
	 * @param conditions    ��������������array("�ֶ���"=>"����ֵ")���ַ�����
	 * ��ע����ʹ���ַ���ʱ����Ҫ����������ʹ��escape��������ֵ���й���
	 * @param sort    ���򣬵�ͬ�ڡ�ORDER BY ��
	 * @param fields    ���ص��ֶη�Χ��Ĭ��Ϊ����ȫ���ֶε�ֵ
	 * @param limit    ���صĽ���������ƣ���ͬ�ڡ�LIMIT ������$limit = " 3, 5"�����Ǵӵ�3����¼����0��ʼ���㣩��ʼ��ȡ������ȡ5����¼
	 *                 ���limitֵֻ��һ�����֣�����ָ����0����¼��ʼ��
	 */
	public function findAll($conditions = null, $sort = null, $fields = null, $limit = null)
	{
		$where = "";
		$fields = empty($fields) ? "*" : $fields;
		if(is_array($conditions)){
			$join = array();
			foreach( $conditions as $key => $condition ){
				$condition = $this->escape($condition);
				$join[] = "{$key} = {$condition}";
			}
			$where = "WHERE ".join(" AND ",$join);
		}else{
			if(null != $conditions)$where = "WHERE ".$conditions;
		}
		if(null != $sort){
			$sort = "ORDER BY {$sort}";
		}else{
			$sort = "ORDER BY {$this->pk}";
		}
		$sql = "SELECT {$fields} FROM {$this->tbl_name} {$where} {$sort}";
		if(null != $limit)$sql = $this->_db->setlimit($sql, $limit);
		return $this->_db->getArray($sql);
	}
	/**
	 * ����ת���ַ�
	 *
	 * @param value ��Ҫ���й��˵�ֵ
	 */
	public function escape($value)
	{
		return $this->_db->__val_escape($value);
	}
	// __val_escape��val�ı�������ǰ����
	public function __val_escape($value){return $this->escape($value);}
	
	/**
	 * �����ݱ�������һ������
	 *
	 * @param row ������ʽ������ļ������ݱ��е��ֶ���������Ӧ��ֵ����Ҫ���������ݡ�
	 */
	public function create($row)
	{
		if(!is_array($row))return FALSE;
		$row = $this->__prepera_format($row);
		if(empty($row))return FALSE;
		foreach($row as $key => $value){
			$cols[] = $key;
			$vals[] = $this->escape($value);
		}
		$col = join(',', $cols);
		$val = join(',', $vals);

		$sql = "INSERT INTO {$this->tbl_name} ({$col}) VALUES ({$val})";
		if( FALSE != $this->_db->exec($sql) ){ // ��ȡ��ǰ������ID
			if( $newinserid = $this->_db->newinsertid() ){
				return $newinserid;
			}else{
				return array_pop( $this->find($row, "{$this->pk} DESC",$this->pk) );
			}
		}
		return FALSE;
	}

	/**
	 * �����ݱ�������������¼
	 *
	 * @param rows ������ʽ��ÿ���Ϊcreate��$row��һ������
	 */
	public function createAll($rows)
	{
		foreach($rows as $row)$this->create($row);
	}

	/**
	 * ������ɾ����¼
	 *
	 * @param conditions ������ʽ�������������˲����ĸ�ʽ�÷���find/findAll�Ĳ���������������ͬ�ġ�
	 */
	public function delete($conditions)
	{
		$where = "";
		if(is_array($conditions)){
			$join = array();
			foreach( $conditions as $key => $condition ){
				$condition = $this->escape($condition);
				$join[] = "{$key} = {$condition}";
			}
			$where = "WHERE ( ".join(" AND ",$join). ")";
		}else{
			if(null != $conditions)$where = "WHERE ( ".$conditions. ")";
		}
		$sql = "DELETE FROM {$this->tbl_name} {$where}";
		return $this->_db->exec($sql);
	}

	/**
	 * ���ֶ�ֵ����һ����¼
	 *
	 * @param field �ַ�������Ӧ���ݱ��е��ֶ���
	 * @param value �ַ�������Ӧ��ֵ
	 */
	public function findBy($field, $value)
	{
		return $this->find(array($field=>$value));
	}

	/**
	 * ���ֶ�ֵ�޸�һ����¼
	 *
	 * @param conditions ������ʽ�������������˲����ĸ�ʽ�÷���find/findAll�Ĳ���������������ͬ�ġ�
	 * @param field �ַ�������Ӧ���ݱ��е���Ҫ�޸ĵ��ֶ���
	 * @param value �ַ�������ֵ
	 */
	public function updateField($conditions, $field, $value)
	{
		return $this->update($conditions, array($field=>$value));
	}

	/**
	 * ʹ��SQL�����в��Ҳ��������ڽ���find��findAll�Ȳ���
	 *
	 * @param sql �ַ�������Ҫ���в��ҵ�SQL���
	 */
	public function findSql($sql)
	{
		return $this->_db->getArray($sql);
	}

	/**
	 * ִ��SQL��䣬�����ִ���������޸ģ�ɾ���Ȳ�����
	 *
	 * @param sql �ַ�������Ҫִ�е�SQL���
	 */
	public function runSql($sql)
	{
		return $this->_db->exec($sql);
	}
	// query��runSql�ı�������ǰ����
	public function query($sql){return $this->runSql($sql);}

	/**
	 * �������ִ�е�SQL��乩����
	 */
	public function dumpSql()
	{
		return end( $this->_db->arrSql );
	}
	
	/**
	 * �����ϴ�ִ��update,create,delete,exec��Ӱ������
	 */
	public function affectedRows()
	{
		return $this->_db->affected_rows();
	}
	/**
	 * ������������ļ�¼����
	 *
	 * @param conditions ��������������array("�ֶ���"=>"����ֵ")���ַ�����
	 * ��ע����ʹ���ַ���ʱ����Ҫ����������ʹ��escape��������ֵ���й���
	 */
	public function findCount($conditions = null)
	{
		$where = "";
		if(is_array($conditions)){
			$join = array();
			foreach( $conditions as $key => $condition ){
				$condition = $this->escape($condition);
				$join[] = "{$key} = {$condition}";
			}
			$where = "WHERE ".join(" AND ",$join);
		}else{
			if(null != $conditions)$where = "WHERE ".$conditions;
		}
		$sql = "SELECT COUNT({$this->pk}) AS SP_COUNTER FROM {$this->tbl_name} {$where}";
		$result = $this->_db->getArray($sql);
		return $result[0]['SP_COUNTER'];
	}

	/**
	 * ħ��������ִ��ģ����չ����Զ����ؼ�ʹ��
	 */
	public function __call($name, $args)
	{
		if(in_array($name, $GLOBALS['G_SP']["auto_load_model"])){
			return spClass($name)->__input($this, $args);
		}elseif(!method_exists( $this, $name )){
			spError("���� {$name} δ����");
		}
	}

	/**
	 * �޸����ݣ��ú��������ݲ��������õ����������±�������
	 * 
	 * @param conditions    ������ʽ�������������˲����ĸ�ʽ�÷���find/findAll�Ĳ���������������ͬ�ġ�
	 * @param row    ������ʽ���޸ĵ����ݣ�
	 *  �˲����ĸ�ʽ�÷���create��$row����ͬ�ġ��ڷ��������ļ�¼�У�����$row���õ��ֶε����ݽ����޸ġ�
	 */
	public function update($conditions, $row)
	{
		$where = "";
		$row = $this->__prepera_format($row);
		if(empty($row))return FALSE;
		if(is_array($conditions)){
			$join = array();
			foreach( $conditions as $key => $condition ){
				$condition = $this->escape($condition);
				$join[] = "{$key} = {$condition}";
			}
			$where = "WHERE ".join(" AND ",$join);
		}else{
			if(null != $conditions)$where = "WHERE ".$conditions;
		}
		foreach($row as $key => $value){
			$value = $this->escape($value);
			$vals[] = "{$key} = {$value}";
		}
		$values = join(", ",$vals);
		$sql = "UPDATE {$this->tbl_name} SET {$values} {$where}";
		return $this->_db->exec($sql);
	}
	
	/**
	 * �滻���ݣ����������滻���ڵļ�¼�����¼�����ڣ����������滻������Ӳ�����һ����¼��
	 * 
	 * @param conditions    ������ʽ��������������ע�⣬����ʹ��������Ϊ��������
	 * @param row    ������ʽ���޸ĵ�����
	 */
	public function replace($conditions, $row)
	{
		if( $this->find($conditions) ){
			return $this->update($conditions, $row);
		}else{
			if( !is_array($conditions) )spError('replace���������������������ʽ��');
			$rows = spConfigReady($conditions, $row);
			return $this->create($rows);
		}
	}
	
	/**
	 * Ϊ�趨���ֶ�ֵ����
	 * @param conditions    ������ʽ�������������˲����ĸ�ʽ�÷���find/findAll�Ĳ���������������ͬ�ġ�
	 * @param field    �ַ�������Ҫ���ӵ��ֶ����ƣ����ֶ��������ֵ����
	 * @param optval    ���ӵ�ֵ
	 */
	public function incrField($conditions, $field, $optval = 1)
	{
		$where = "";
		if(is_array($conditions)){
			$join = array();
			foreach( $conditions as $key => $condition ){
				$condition = $this->escape($condition);
				$join[] = "{$key} = {$condition}";
			}
			$where = "WHERE ".join(" AND ",$join);
		}else{
			if(null != $conditions)$where = "WHERE ".$conditions;
		}
		$values = "{$field} = {$field} + {$optval}";
		$sql = "UPDATE {$this->tbl_name} SET {$values} {$where}";
		return $this->_db->exec($sql);
	}
	
	/**
	 * Ϊ�趨���ֶ�ֵ����
	 * @param conditions    ������ʽ�������������˲����ĸ�ʽ�÷���find/findAll�Ĳ���������������ͬ�ġ�
	 * @param field    �ַ�������Ҫ���ٵ��ֶ����ƣ����ֶ��������ֵ����
	 * @param optval    ���ٵ�ֵ
	 */
	public function decrField($conditions, $field, $optval = 1)
	{
		return $this->incrField($conditions, $field, - $optval);
	}

	/**
	 * �����������ݱ������ɾ����¼
	 *
	 * @param pk    �ַ��������֣����ݱ�������ֵ��
	 */
	public function deleteByPk($pk)
	{
		return $this->delete(array($this->pk=>$pk));
	}

	/**
	 * �����ֶε����ʺϵ��ֶ�
	 * @param rows    ����ı��ֶ�
	 */
	private function __prepera_format($rows)
	{
		$columns = $this->_db->getTable($this->tbl_name);
		$newcol = array();
		foreach( $columns as $col ){
			$newcol[$col['Field']] = $col['Field'];
		}
		return array_intersect_key($rows,$newcol);
	}

}


/**
 * spPager
 * ���ݷ�ҳ����
 */
class spPager {
	/**
	 * ģ�Ͷ���
	 */
	private $model_obj = null;
	/**
	 * ҳ������
	 */
	private $pageData = null;
	/** 
	 * ����ʱ����Ĳ���
	 */
	private $input_args = null;
	/** 
	 * ����ʽʹ��ģ�͸���������뺯��
	 */
    public function __input(& $obj, $args){
		$this->model_obj = $obj;
		$this->input_args = $args;
		return $this;
	}
	/** 
	 * ħ��������֧�ֶ��غ���ʽʹ����ķ���
	 */
	public function __call($func_name, $func_args){
		if( ( 'findAll' == $func_name || 'findSql' == $func_name ) && 0 != $this->input_args[0]){
			return $this->runpager($func_name, $func_args);
		}elseif(method_exists($this,$func_name)){
			return call_user_func_array(array($this, $func_name), $func_args);
		}else{
			return call_user_func_array(array($this->model_obj, $func_name), $func_args);
		}
	}
	/** 
	 * ��ȡ��ҳ����
	 */
	public function getPager(){
		return $this->pageData;
	}
	
	/** 
	 * ���ɷ�ҳ����
	 */
	private function runpager($func_name, $func_args){
		$this->pageData = null;
		$page = $this->input_args[0];
		$pageSize = $this->input_args[1];
		@list($conditions, $sort, $fields ) = $func_args;
		if('findSql'==$func_name){
			$total_count = array_pop( array_pop( $this->model_obj->findSql("SELECT COUNT({$this->model_obj->pk}) as sp_counter FROM ($conditions) sp_tmp_table_pager1") ) );
		}else{
			$total_count = $this->model_obj->findCount($conditions);
		}
		if($total_count > $pageSize){
			$total_page = ceil( $total_count / $pageSize );
			$page = min(intval(max($page, 1)), $total_count); // ��ҳ����й淶����
			$this->pageData = array(
				"total_count" => $total_count,                                 // �ܼ�¼��
				"page_size"   => $pageSize,                                    // ��ҳ��С
				"total_page"  => $total_page,                                  // ��ҳ��
				"first_page"  => 1,                                            // ��һҳ
				"prev_page"   => ( ( 1 == $page ) ? 1 : ($page - 1) ),         // ��һҳ
				"next_page"   => ( ( $page == $total_page ) ? $total_page : ($page + 1)),     // ��һҳ
				"last_page"   => $total_page,                                  // ���һҳ
				"current_page"=> $page,                                        // ��ǰҳ
				"all_pages"   => array()	                                   // ȫ��ҳ��
			);
			for($i=1; $i <= $total_page; $i++)$this->pageData['all_pages'][] = $i;
			$limit = ($page - 1) * $pageSize . "," . $pageSize;
			if('findSql'==$func_name)$conditions = $this->model_obj->_db->setlimit($conditions, $limit);
		}
		if('findSql'==$func_name){
			return $this->model_obj->findSql($conditions);
		}else{
			return $this->model_obj->findAll($conditions, $sort, $fields, $limit);
		}
	}
}

/**
 * spVerifier
 * ������֤����
 */
class spVerifier {

	/** 
	 * ���ӵļ��������
	 */
	private $add_rules = null;
	
	/** 
	 * ��֤����
	 */
	private $verifier = null;
	
	/** 
	 * ��֤ʱ���ص���ʾ��Ϣ
	 */
	private $messages = null;
	
	/** 
	 * ����֤�ֶ�
	 */
	private $checkvalues = null;
	/** 
	 * ����ʽʹ��ģ�͸���������뺯��
	 */
    public function __input(& $obj, $args){
		$this->verifier = (null != $obj->verifier) ? $obj->verifier : array();
		if(isset($args[1]) && is_array($args[1])){
			$this->verifier["rules"] = $this->verifier["rules"] + $args[1]["rules"];
			$this->verifier["messages"] = isset($args[1]["messages"]) ? ( $this->verifier["messages"] + $args[1]["messages"] ) : $this->verifier["messages"];
		}
		if(is_array($obj->addrules) && !empty($obj->addrules) ){foreach($obj->addrules as $addrule => $addveri)$this->addrules($addrule, $addveri);}
		if(empty($this->verifier["rules"]))spError("�޶�Ӧ����֤����");
		return is_array($args[0]) ? $this->checkrules($args[0]) : TRUE; // TRUEΪ��ͨ����֤
	}
	
	/** 
	 * ���븽�ӵ���֤����
	 * 
	 * @param rule_name    ��֤��������
	 * @param checker    ��֤������֤�����������ַ�ʽ��
	 * ��һ����  '��֤������'�����ǵ�������һ�������ĺ���ʱʹ��
	 * �ڶ����� array('����', '����������')�����ǵ�������һ�����ĳ����������ʱ��ʹ�á�
	 */
	public function addrules($rule_name, $checker){
		$this->add_rules[$rule_name] = $checker;
	}
	/** 
	 * ��������֤����
	 * 
	 * @param values    ��ֵ֤
	 */
	private function checkrules($values){ 
		$this->checkvalues = $values;
		foreach( $this->verifier["rules"] as $rkey => $rval ){
			$inputval = isset($values[$rkey]) ? $values[$rkey] : '';
			foreach( $rval as $rule => $rightval ){
				if(method_exists($this, $rule)){
					if(TRUE == $this->$rule($inputval, $rightval))continue;
				}elseif(null != $this->add_rules && isset($this->add_rules[$rule])){
					if( function_exists($this->add_rules[$rule]) ){
						if(TRUE == $this->add_rules[$rule]($inputval, $rightval, $values))continue;
					}elseif( is_array($this->add_rules[$rule]) ){
						if(TRUE == spClass($this->add_rules[$rule][0])->{$this->add_rules[$rule][1]}($inputval, $rightval, $values))continue;
					}
				}else{
					spError("δ֪����{$rule}");
				}
				$this->messages[$rkey][] = (isset($this->verifier["messages"][$rkey][$rule])) ? $this->verifier["messages"][$rkey][$rule] : "{$rule}";
			}
		}
		// ����FALSE��ͨ����֤������������δ��ͨ����֤�����ص�����ʾ��Ϣ��
		return (null == $this->messages) ? FALSE : $this->messages; 
	}
	/** 
	 * ������֤��������ַ����ǿ�
	 * @param val    ����֤�ַ���
	 * @param right    ��ȷֵ
	 */
	private function notnull($val, $right){return $right === ( strlen($val) > 0 );}
	/** 
	 * ������֤��������ַ����Ƿ�С��ָ������
	 * @param val    ����֤�ַ���
	 * @param right    ��ȷֵ
	 */
	private function minlength($val, $right){return $this->cn_strlen($val) >= $right;}
	/** 
	 * ������֤��������ַ����Ƿ����ָ������
	 * @param val    ����֤�ַ���
	 * @param right    ��ȷֵ
	 */
	private function maxlength($val, $right){return $this->cn_strlen($val) <= $right;}
	/** 
	 * ������֤��������ַ����Ƿ������һ����֤�ֶε�ֵ
	 * @param val    ����֤�ַ���
	 * @param right    ��ȷֵ
	 */
	private function equalto($val, $right){return $val == $this->checkvalues[$right];}
	/** 
	 * ������֤��������ַ����Ƿ���ȷ��ʱ���ʽ
	 * @param val    ����֤�ַ���
	 * @param right    ��ȷֵ
	 */
	private function istime($val, $right){$test = @strtotime($val);return $right == ( $test !== -1 && $test !== false );}
	/** 
	 * ������֤��������ַ����Ƿ���ȷ�ĵ����ʼ���ʽ
	 * @param val    ����֤�ַ���
	 * @param right    ��ȷֵ
	 */	
	private function email($val, $right){
		return $right == ( preg_match('/^[A-Za-z0-9]+([._\-\+]*[A-Za-z0-9]+)*@([A-Za-z0-9-]+\.)+[A-Za-z0-9]+$/', $val) != 0 );
	}
	/** 
	 * �����ַ������ȣ�֧�ְ����������ڵ��ַ���
	 * @param val    ��������ַ���
	 */
	public function cn_strlen($val){$i=0;$n=0;
		while($i<strlen($val)){$clen = ( strlen("����") == 4 ) ? 2 : 3;
			if(preg_match("/^[".chr(0xa1)."-".chr(0xff)."]+$/",$val[$i])){$i+=$clen;}else{$i+=1;}$n+=1;}
		return $n;
	}
}

/**
 * spCache
 * ���������ݻ���ʵ��
 */
class spCache {
	
	/**
	 * Ĭ�ϵ�����������
	 */
	public $life_time = 3600;
	
	/**
	 * ģ�Ͷ���
	 */
	private $model_obj = null;
	
	/** 
	 * ����ʱ����Ĳ���
	 */
	private $input_args = null;
	/** 
	 * ����ʽʹ��ģ�͸���������뺯��
	 */
    public function __input(& $obj, $args){
		$this->model_obj = $obj;
		$this->input_args = $args;
		return $this;
	}
	/** 
	 * ħ��������֧�ֶ��غ���ʽʹ����ķ���
	 */
	public function __call($func_name, $func_args){
		if( isset($this->input_args[0]) && -1 == $this->input_args[0] )return $this->clear($this->model_obj, $func_name, $func_args);
		$cache_id = get_class($this->model_obj) . md5($func_name);
		if( null != $func_args )$cache_id .= md5(json_encode($func_args));
		if( $cache_file = spAccess('r', "sp_cache_{$cache_id}") )return unserialize( $cache_file );
		return $this->cache_obj($cache_id, call_user_func_array(array($this->model_obj, $func_name), $func_args), $this->input_args[0]);
	}
	/** 
	 * ִ��spModel�������ķ��������Է��ؽ�����л��档
	 *
	 * @param obj    ���õ�spModel�������
	 * @param func_name    ��Ҫִ�еĺ�������
	 * @param func_args    �����Ĳ���
	 * @param life_time    ��������ʱ��
	 */
	public function cache_obj($cache_id, $run_result, $life_time = null ){
		if( null == $life_time )$life_time = $this->life_time;
		spAccess('w', "sp_cache_{$cache_id}", serialize($run_result), $life_time);
		if( $cache_list = spAccess('r', 'sp_cache_list') ){
			$cache_list = explode("\n",$cache_list);
			if( ! in_array( $cache_id, $cache_list ) )spAccess('w', 'sp_cache_list', join("\n", $cache_list) . $cache_id . "\n");
		}else{
			spAccess('w', 'sp_cache_list', $cache_id . "\n");
		}
		return $run_result;
	}
	/** 
	 * ��������������������
	 *
	 * @param obj    ���õ�spModel�������
	 * @param func_name    ��Ҫִ�еĺ�������
	 * @param func_args    �����Ĳ�������Ĭ�ϲ��������������£������ȫ���ú������ɵĻ��档
	 * ���func_args�����ã���ֻ������ò��������Ļ��档
	 */
	public function clear(& $obj, $func_name, $func_args = null){
		$cache_id = get_class($obj) . md5($func_name);
		if( null != $func_args )$cache_id .= md5(json_encode($func_args));
		if( $cache_list = spAccess('r', 'sp_cache_list') ){
			$cache_list = explode("\n",$cache_list);
			$new_list = '';
			foreach( $cache_list as $single_item ){
				if( $single_item == $cache_id || ( null == $func_args && substr($single_item,0,strlen($cache_id)) == $cache_id ) ){
					spAccess('c', "sp_cache_{$single_item}");
				}else{
					$new_list .= $single_item. "\n";
				}
			}
			spAccess('w', 'sp_cache_list', substr($new_list,0,-1));
		}
		return TRUE;
	}
	/** 
	 * ���ȫ���������������
	 *
	 */
	public function clear_all(){
		if( $cache_list = spAccess('r', 'sp_cache_list') ){
			$cache_list = explode("\n",$cache_list);
			foreach( $cache_list as $single_item )spAccess('c', "sp_cache_{$single_item}");
			spAccess('c', 'sp_cache_list');
		}
		return TRUE;
	}
}

/**
 * spLinker 
 * ���ݿ�ı���������
 */
class spLinker
{
	/**
	 * ģ�Ͷ���
	 */
	private $model_obj = null;
	
	/** 
	 * Ԥ׼���Ľ��
	 */
	private $prepare_result = null;
	
	/** 
	 * ���еĽ��
	 */
	private $run_result = null;
	
	/**
	 * ��֧�ֵĹ�������
	 */
	private $methods = array('find','findBy','findAll','run','create','delete','deleteByPk','update');
	/**
	 * �Ƿ�����ȫ������
	 */
	public $enabled = TRUE;
	/** 
	 * ����ʽʹ��ģ�͸���������뺯��
	 */
    public function __input(& $obj, $args = null){
		$this->model_obj = $obj;
		return $this;
	}
	
	/** 
	 * �����߿���ͨ��spLinker()->run($result)���Ѿ����ص����ݽ��й���findAll����
	 * @param result    ���ص�����
	 */
    public function run($result = FALSE){
    	if( FALSE == $result )return FALSE;
		$this->run_result = $result;
		return $this->__call('run', null);
	}
	
	/** 
	 * ħ��������֧�ֶ��غ���ʽʹ����ķ���
	 *
	 * ��spLinker���У�__callִ����spModel�̳������ز������Լ������������������˶Թ�������ģ����Ĳ�����
	 */
	public function __call($func_name, $func_args){
		if( in_array( $func_name, $this->methods ) && FALSE != $this->enabled ){
			if( 'delete' == $func_name || 'deleteByPk' == $func_name )$maprecords = $this->prepare_delete($func_name, $func_args);
			if( null != $this->run_result ){
				$run_result = $this->run_result;
			}elseif( !$run_result = call_user_func_array(array($this->model_obj, $func_name), $func_args) ){
				if( 'update' != $func_name )return FALSE;
			}
			if( null != $this->model_obj->linker && is_array($this->model_obj->linker) ){
				foreach( $this->model_obj->linker as $linkey => $thelinker ){
					if( !isset($thelinker['map']) )$thelinker['map'] = $linkey;
					if( FALSE == $thelinker['enabled'] )continue;
					$thelinker['type'] = strtolower($thelinker['type']);
					if( 'find' == $func_name || 'findBy' == $func_name ){
						$run_result[$thelinker['map']] = $this->do_select( $thelinker, $run_result );
					}elseif( 'findAll' == $func_name || 'run' == $func_name ){
						foreach( $run_result as $single_key => $single_result )
							$run_result[$single_key][$thelinker['map']] = $this->do_select( $thelinker, $single_result );
					}elseif( 'create' == $func_name ){
						$this->do_create( $thelinker, $run_result, $func_args );
					}elseif( 'update' == $func_name ){
						$this->do_update( $thelinker, $func_args );
					}elseif( 'delete' == $func_name || 'deleteByPk' == $func_name ){
						$this->do_delete( $thelinker, $maprecords );
					}
				}
			}
			return $run_result;
		}elseif(in_array($func_name, $GLOBALS['G_SP']["auto_load_model"])){
			return spClass($func_name)->__input($this, $func_args);
  		}else{
			return call_user_func_array(array($this->model_obj, $func_name), $func_args);
		}
	}

	/** 
	 * ˽�к���������ɾ�����ݲ���
	 * @param func_name    ��Ҫִ�еĺ�������
	 * @param func_args    �����Ĳ���
	 */
	private function prepare_delete($func_name, $func_args)
	{
		if('deleteByPk'==$func_name){
			return $this->model_obj->findAll(array($this->model_obj->pk=>$func_args[0]));
		}else{
			return $this->model_obj->findAll($func_args[0]);
		}
	}
	/** 
	 * ˽�к��������й���ɾ�����ݲ���
	 * @param thelinker    ����������
	 * @param maprecords    ��Ӧ�ļ�¼
	 */
	private function do_delete( $thelinker, $maprecords ){
		if( FALSE == $maprecords )return FALSE;
		foreach( $maprecords as $singlerecord ){
			if(!empty($thelinker['condition'])){
				if( is_array($thelinker['condition']) ){
					$fcondition = array($thelinker['fkey']=>$singlerecord[$thelinker['mapkey']]) + $thelinker['condition'];
				}else{
					$fcondition = "{$thelinker['fkey']} = '{$singlerecord[$thelinker['mapkey']]}' AND {$thelinker['condition']}";
				}
			}else{
				$fcondition = array($thelinker['fkey']=>$singlerecord[$thelinker['mapkey']]);
			}
			$returns = spClass($thelinker['fclass'])->delete($fcondition);
		}
		return $returns;
	}
	/** 
	 * ˽�к��������й����������ݲ���
	 * @param thelinker    ����������
	 * @param func_args    ���в����Ĳ���
	 */
	private function do_update( $thelinker, $func_args ){
		if( !is_array($func_args[1][$thelinker['map']]) )return FALSE;
		if( !$maprecords = $this->model_obj->findAll($func_args[0]))return FALSE;
		foreach( $maprecords as $singlerecord ){
			if(!empty($thelinker['condition'])){
				if( is_array($thelinker['condition']) ){
					$fcondition = array($thelinker['fkey']=>$singlerecord[$thelinker['mapkey']]) + $thelinker['condition'];
				}else{
					$fcondition = "{$thelinker['fkey']} = '{$singlerecord[$thelinker['mapkey']]}' AND {$thelinker['condition']}";
				}
			}else{
				$fcondition = array($thelinker['fkey']=>$singlerecord[$thelinker['mapkey']]);
			}
			$returns = spClass($thelinker['fclass'])->update($fcondition, $func_args[1][$thelinker['map']]);
		}
		return $returns;
	}
	/** 
	 * ˽�к��������й����������ݲ���
	 * @param thelinker    ����������
	 * @param newid    ����������¼��Ĺ���ID
	 * @param func_args    ���в����Ĳ���
	 */
	private function do_create( $thelinker, $newid, $func_args ){
		if( !is_array($func_args[0][$thelinker['map']]) )return FALSE;
		if('hasone'==$thelinker['type']){
			$newrows = $func_args[0][$thelinker['map']];
			$newrows[$thelinker['fkey']] = $newid;
			return spClass($thelinker['fclass'])->create($newrows);
		}elseif('hasmany'==$thelinker['type']){
			if(array_key_exists(0,$func_args[0][$thelinker['map']])){ // �������
				foreach($func_args[0][$thelinker['map']] as $singlerows){
					$newrows = $singlerows;
					$newrows[$thelinker['fkey']] = $newid;
					$returns = spClass($thelinker['fclass'])->create($newrows);	
				}
				return $returns;
			}else{ // ��������
				$newrows = $func_args[0][$thelinker['map']];
				$newrows[$thelinker['fkey']] = $newid;
				return spClass($thelinker['fclass'])->create($newrows);
			}
		}
	}
	/** 
	 * ˽�к��������й����������ݲ���
	 * @param thelinker    ����������
	 * @param run_result    ����ִ�в��Һ󷵻صĽ��
	 */
	private function do_select( $thelinker, $run_result ){
		if(empty($thelinker['mapkey']))$thelinker['mapkey'] = $this->model_obj->pk;
		if( 'manytomany' == $thelinker['type'] ){
			$do_func = 'findAll';
			$midcondition = array($thelinker['mapkey']=>$run_result[$thelinker['mapkey']]);
			if( !$midresult = spClass($thelinker['midclass'])->findAll($midcondition,null,$thelinker['fkey']) )return FALSE;
			$tmpkeys = array();foreach( $midresult as $val )$tmpkeys[] = "'".$val[$thelinker['fkey']]."'";
			if(!empty($thelinker['condition'])){
				if( is_array($thelinker['condition']) ){
					$fcondition = "{$thelinker['fkey']} in (".join(',',$tmpkeys).")";
					foreach( $thelinker['condition'] as $tmpkey => $tmpvalue )$fcondition .= " AND {$tmpkey} = '{$tmpvalue}'";
				}else{
					$fcondition = "{$thelinker['fkey']} in (".join(',',$tmpkeys).") AND {$thelinker['condition']}";
				}
			}else{
				$fcondition = "{$thelinker['fkey']} in (".join(',',$tmpkeys).")";
			}
		}else{
			$do_func = ( 'hasone' == $thelinker['type'] ) ? 'find' : 'findAll';
			if(!empty($thelinker['condition'])){
				if( is_array($thelinker['condition']) ){
					$fcondition = array($thelinker['fkey']=>$run_result[$thelinker['mapkey']]) + $thelinker['condition'];
				}else{
					$fcondition = "{$thelinker['fkey']} = '{$run_result[$thelinker['mapkey']]}' AND {$thelinker['condition']}";
				}
			}else{
				$fcondition = array($thelinker['fkey']=>$run_result[$thelinker['mapkey']]);
			}
		}
		if(TRUE == $thelinker['countonly'])$do_func = "findCount";
		return spClass($thelinker['fclass'])->$do_func($fcondition, $thelinker['sort'], $thelinker['field'], $thelinker['limit'] );
	}
}
