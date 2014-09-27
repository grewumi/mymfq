<?php
/////////////////////////////////////////////////////////////////
// SpeedPHP����PHP���, Copyright (C) 2008 - 2010 SpeedPHP.com //
/////////////////////////////////////////////////////////////////

/**
 * db_mysqli MySQL���ݿ�ĸĽ��汾MySQLi������֧�� 
 */
class db_mysqli {
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
		if( ! mysqli_num_rows($result) )return array();
		$rows = array();
		while($rows[] = mysqli_fetch_array($result, MYSQLI_ASSOC)){}
		mysqli_free_result($result);
		array_pop($rows);
		return $rows;
	}
	
	/**
	 * ���ص�ǰ�����¼������ID
	 */
	public function newinsertid()
	{
		return mysqli_insert_id($this->conn);
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
		if( $result = mysqli_query($this->conn, $sql) ){
			return $result;
		}else{
			spError("{$sql}<br />ִ�д���: " . mysqli_error($this->conn));
		}
	}
	
	/**
	 * ����Ӱ������
	 */
	public function affected_rows()
	{
		return mysqli_affected_rows($this->conn);
	}

	/**
	 * ��ȡ���ݱ�ṹ
	 *
	 * @param tbl_name  ������
	 */
	public function getTable($tbl_name)
	{
		return $this->getArray("DESCRIBE {$tbl_name}");
	}

	/**
	 * ���캯��
	 *
	 * @param dbConfig  ���ݿ�����
	 */
	public function __construct($dbConfig)
	{
		if(!function_exists('mysqli_connect'))spError('PHP����δ��װMySQLi�����⣡');
		$linkfunction = ( TRUE == $dbConfig['persistent'] ) ? 'mysqli_pconnect' : 'mysqli_connect';
		$this->conn = $linkfunction($dbConfig['host'], $dbConfig['login'], $dbConfig['password'], $dbConfig['database'], $dbConfig['port']);
		if(mysqli_connect_errno())spError('���ݿ����Ӵ���/�޷��ҵ����ݿ� : '. mysqli_connect_error());
		$this->exec("SET NAMES GBK");
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
		return '\''.mysqli_real_escape_string($this->conn, $value).'\'';// ע��mysqli_real_escape_string�Ĳ���λ��
	}

	/**
	 * ��������
	 */
	public function __destruct()
	{
		if( TRUE != $GLOBALS['G_SP']['db']['persistent'] )@mysqli_close($this->conn);
	}
}

