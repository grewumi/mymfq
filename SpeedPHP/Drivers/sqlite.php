<?php
/////////////////////////////////////////////////////////////////
// SpeedPHP����PHP���, Copyright (C) 2008 - 2010 SpeedPHP.com //
/////////////////////////////////////////////////////////////////

/**
 * db_sqlite Sqlite���ݿ������֧�� 
 */
class db_sqlite {
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
		$this->arrSql[] = $sql;
		return sqlite_array_query($this->conn, $sql, SQLITE_ASSOC);
	}
	
	/**
	 * ���ص�ǰ�����¼������ID
	 */
	public function newinsertid()
	{
		return sqlite_last_insert_rowid($this->conn);
	}
	
	/**
	 * ��ʽ����limit��SQL���
	 */
	public function setlimit($sql, $limit)
	{
		return $sql. " LIMIT {$limit}";
	}

	/**
	 * ִ��һ��SQL���
	 * 
	 * @param sql ��Ҫִ�е�SQL���
	 */
	public function exec($sql)
	{
		$this->arrSql[] = $sql;
		if( $result = sqlite_query($this->conn, $sql, SQLITE_ASSOC, $sqliteerror) ){
			return $result;
		}else{
			spError("{$sql}<br />ִ�д���: " . $sqliteerror);
		}
	}
	
	/**
	 * ����Ӱ������
	 */
	public function affected_rows()
	{
		return sqlite_changes($this->conn);
	}

	/**
	 * ��ȡ���ݱ�ṹ
	 *
	 * @param tbl_name  ������
	 */
	public function getTable($tbl_name)
	{
		$cols = sqlite_fetch_column_types($tbl_name, $this->conn, SQLITE_ASSOC);
		$columns = array();
		foreach ($cols as $column => $type) {
		    $columns[] = array('Field'=>$column);
		}
		return $columns;
	}

	/**
	 * ���캯��
	 *
	 * @param dbConfig  ���ݿ�����
	 */
	public function __construct($dbConfig)
	{
		if(!function_exists('sqlite_open'))spError('PHP����δ��װSqlite�����⣡');
		$linkfunction = ( TRUE == $dbConfig['persistent'] ) ? 'sqlite_popen' : 'sqlite_open';
		if (! $this->conn = $linkfunction($dbConfig['host'], 0666, $sqliteerror))spError('���ݿ����Ӵ���/�޷��ҵ����ݿ� : '. $sqliteerror);
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
		return '\''.sqlite_escape_string($value).'\'';
	}

	/**
	 * ��������
	 */
	public function __destruct()
	{
		if( TRUE != $GLOBALS['G_SP']['db']['persistent'] )@sqlite_close($this->conn);
	}
}

