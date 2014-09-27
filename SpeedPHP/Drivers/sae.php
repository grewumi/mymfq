<?php
/////////////////////////////////////////////////////////////////
// SpeedPHP����PHP���, Copyright (C) 2008 - 2010 SpeedPHP.com //
/////////////////////////////////////////////////////////////////

/**
 * SAE��MySQL���ݿ������֧�� 
 *
 * SAE��Sina App Engine������Ӧ�����棩����д��SAE��һ���ֲ�ʽwebӦ�ÿ������еķ���ƽ̨��
 * �䲻������������������webӦ�õļ򵥽��������漰һ���״��ģ�ֲ�ʽ����Ľ��������
 *
 * db_sae ��װ��SAE�ṩ��SaeMysql�������������
 */
class db_sae {
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
		$result = $this->conn->getData($sql);
		if( $this->conn->errno() )spError("{$sql}<br />ִ�д���: " . $this->conn->error());
		return $result;
	}
	
	/**
	 * ���ص�ǰ�����¼������ID
	 */
	public function newinsertid()
	{
		return $this->conn->lastId();
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
		$result = $this->conn->runSql($sql);
		if( $this->conn->errno() )spError("{$sql}<br />ִ�д���: " . $this->conn->error());
		return $result;
	}
	
	/**
	 * ����Ӱ������
	 */
	public function affected_rows()
	{
		return FALSE; // SAE������ʱ�޷���ȡӰ������
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
		if(TRUE == SP_DEBUG)sae_set_display_errors(TRUE);
		$this->conn = new SaeMysql();
		if( $this->conn->errno() )spError("���ݿ����Ӵ��� : " . $this->conn->error()); 
		$this->conn->setCharset("GBK");
	}
	/**
	 * �������ַ����й���
	 *
	 * @param value  ֵ
	 */
	public function __val_escape($value, $quotes = FALSE) {
		if(is_null($value))return 'NULL';
		if(is_bool($value))return $value ? 1 : 0;
		if(is_int($value))return (int)$value;
		if(is_float($value))return (float)$value;
		if(@get_magic_quotes_gpc())$value = stripslashes($value);
		return '\''.$this->conn->escape($value).'\'';
	}

	/**
	 * ��������
	 */
	public function __destruct()
	{
		@$this->conn->closeDb();
	}
	
	/**
	 * getConn ȡ��Sae MySQL����
	 * Ϊ�˸��õ�ʹ��Sea�ṩMySQL�࣬getSeaDB����������Sae MySQL���󹩿�����ʹ��
	 */
	public function getConn()
	{
		return $this->conn;
	}
}

