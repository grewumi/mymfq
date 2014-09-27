<?php
/////////////////////////////////////////////////////////////////
// SpeedPHP����PHP���, Copyright (C) 2008 - 2010 SpeedPHP.com //
/////////////////////////////////////////////////////////////////

/**
 * db_mysql MySQL���ݿ������֧�� 
 */
class db_mysql {
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
		if( ! mysql_num_rows($result) )return array();
		$rows = array();
		while($rows[] = mysql_fetch_array($result,MYSQL_ASSOC)){}
		mysql_free_result($result);
		array_pop($rows);
		return $rows;
	}
	
	/**
	 * ���ص�ǰ�����¼������ID
	 */
	public function newinsertid()
	{
		return mysql_insert_id($this->conn);
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
		if( $result = mysql_query($sql, $this->conn) ){
			return $result;
		}else{
			spError("{$sql}<br />ִ�д���: " . mysql_error());
		}
	}
	
	/**
	 * ����Ӱ������
	 */
	public function affected_rows()
	{
		return mysql_affected_rows($this->conn);
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
		$linkfunction = ( TRUE == $dbConfig['persistent'] ) ? 'mysql_pconnect' : 'mysql_connect';
		$this->conn = $linkfunction($dbConfig['host'].":".$dbConfig['port'], $dbConfig['login'], $dbConfig['password']) or spError("���ݿ����Ӵ��� : " . mysql_error()); 
		mysql_select_db($dbConfig['database'], $this->conn) or spError("�޷��ҵ����ݿ⣬��ȷ�����ݿ�������ȷ��");
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
		return '\''.mysql_real_escape_string($value, $this->conn).'\'';
	}

	/**
	 * ��������
	 */
	public function __destruct()
	{
		if( TRUE != $GLOBALS['G_SP']['db']['persistent'] )@mysql_close($this->conn);
	}
}

