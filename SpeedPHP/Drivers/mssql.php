<?php
/////////////////////////////////////////////////////////////////
// SpeedPHP����PHP���, Copyright (C) 2008 - 2010 SpeedPHP.com //
/////////////////////////////////////////////////////////////////

/**
 * db_mssql MsSQL���ݿ������֧��
 */
class db_mssql {
	/**
	 * ���ݿ����Ӿ��
	 */
	public $conn;
	/**
	 * ִ�е�SQL����¼
	 */
	public $arrSql;

	/**
	 * ��SQL����ȡ��¼�������������
	 * 
	 * @param sql  ִ�е�SQL���
	 */
	public function getArray($sql)
	{
		if( ! $result = $this->exec($sql) )return array();
		if( ! mssql_num_rows($result) )return array();
		$rows = array();
		while($rows[] = mssql_fetch_array($result,MSSQL_ASSOC)){}
		mssql_free_result($result);
		array_pop($rows);
		return $rows;
	}
	
	/**
	 * ���ص�ǰ�����¼������ID
	 */
	public function newinsertid()
	{
		$result = $this->getArray("select @@IDENTITY as sptmp_newinsert_id");
		return $result[0]['sptmp_newinsert_id'];
	}
	
	/**
	 * ��ʽ����limit��SQL���
	 */
	public function setlimit($sql, $limit)
	{
		if(!eregi(",", $limit))$limit = '0,'.$limit;
		$sql .= " LIMIT {$limit}";
		return $this->translimit($sql);
	}

	/**
	 * ִ��һ��SQL���
	 * 
	 * @param sql ��Ҫִ�е�SQL���
	 */
	public function exec($sql)
	{
		$this->arrSql[] = $sql;
		if( $result = mssql_query($sql, $this->conn) ){
			return $result;
		}else{
			spError("{$sql}<br />ִ�д���: " . mssql_get_last_message());
		}
	}
	
	/**
	 * ����Ӱ������
	 */
	public function affected_rows()
	{
		return mssql_rows_affected($this->conn);
	}

	/**
	 * ��ȡ���ݱ�ṹ
	 *
	 * @param tbl_name  ������
	 */
	public function getTable($tbl_name)
	{
		$result = $this->getArray("SELECT syscolumns.name FROM syscolumns, systypes WHERE syscolumns.xusertype = systypes.xusertype AND syscolumns.id = object_id('{$tbl_name}')");
		$columns = array();
		foreach( $result as $column )$columns[] = array('Field'=>$column['name']);
		return $columns;
	}

	/**
	 * ���캯��
	 *
	 * @param dbConfig  ���ݿ�����
	 */
	public function __construct($dbConfig)
	{
		if(!function_exists('mssql_connect'))spError('PHP����δ��װMSSQL�����⣡');
		$linkfunction = ( TRUE == $dbConfig['persistent'] ) ? 'mssql_pconnect' : 'mssql_connect';
		$this->conn = $linkfunction($dbConfig['host'], $dbConfig['login'], $dbConfig['password']) or spError("���ݿ����Ӵ��� : " . mssql_get_last_message()); 
		mssql_select_db($dbConfig['database'], $this->conn) or spError("�޷��ҵ����ݿ⣬��ȷ�����ݿ�������ȷ��");
	}
	/**
	 * �������ַ����й���
	 *
	 * @param value  ֵ
	 */
	public function __val_escape($value) {
		if(is_null($value))return 'NULL';
		if(is_bool($value))return $value ? 1 : 0;
		if(is_int($value))return (int)$value;
		if(is_float($value))return (float)$value;
		if(@get_magic_quotes_gpc())$value = stripslashes($value);
		$search=array("\\","\0","\n","\r","\x1a","'",'"');
        $replace=array("\\\\","[NULL]","\\n","\\r","\Z","''",'\"');
        return '\''.str_replace($search,$replace,$value).'\'';
	}

	/**
	 * ��������
	 */
	public function __destruct()
	{
		if( TRUE != $GLOBALS['G_SP']['db']['persistent'] )@mssql_close($this->conn);
	}

	/**
	 * ת��MSSQL��LIMIT����ת������
	 */
	function translimit($sql){       
		if(preg_match('/ limit /i', $sql)){
			//ȥ�����ո� 
			while(preg_match("/  /", $sql))$sql = str_replace("  "," ",$sql);
			$sql_array = explode(" ",$sql);
			//ȡ�ò�����Ҫ���������� 
			$i = 0;
			while(isset($sql_array[$i]) && $sql_array[$i]){ 
				if(strtolower($sql_array[$i])=="from")$from_id = $i;  
				if(strtolower($sql_array[$i])=="limit")$limit_id = $i; 
				if(strtolower($sql_array[$i])=="order")$order_id = $i;
				$i++;
			} 
			$last_id = $i-1; 
			$two_num = explode(",",$sql_array[$limit_id+1]);
			$totle_num = $two_num[0]+$two_num[1];

			$sql_return = "SELECT ";
			for($i=1;$i<=$from_id;$i++){ 
				$sql_return .= $sql_array[$i]; 
				$sql_return .= " "; 
			}
			$sql_return .= " ( SELECT TOP {$two_num[1]} ";
			for($i=1;$i<=$from_id;$i++){
				$sql_return .= $sql_array[$i]; 
				$sql_return .= " "; 
			}
			$sql_return .=" ( SELECT TOP {$totle_num} ";
			for($i=1;$i<$limit_id;$i++){
				$sql_return .= $sql_array[$i] ; 
				$sql_return .= " "; 
			} 
			$sql_return .= " ) AS SPTMP_MSSQL_TOTLERESULT ";
			if(preg_match("/ desc /i", $sql)){
				for($i=$from_id+2;$i<$limit_id;$i++){
					if(strtolower($sql_array[$i]) == "desc")continue;
					$sql_return .= $sql_array[$i];
					$sql_return .= " ";
				}
			}else{
				for($i=$from_id+2;$i<$limit_id;$i++){
					$sql_return .= $sql_array[$i];
					$sql_return .= " ";
					if($i == $order_id+2)$sql_return .= " DESC ";
				} 
			}
			$sql_return .= " ) AS SPTMP_MSSQL_ALLRESULT ";
			for($i=$from_id+2;$i<$limit_id;$i++){$sql_return .= $sql_array[$i] ." ";}
			return $sql_return;
		}else{
			return $sql;
		}
	}
}
