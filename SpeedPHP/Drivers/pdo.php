<?php
/////////////////////////////////////////////////////////////////
// SpeedPHP����PHP���, Copyright (C) 2008 - 2010 SpeedPHP.com //
/////////////////////////////////////////////////////////////////

/**
 * db_pdo_mysql PDO MySQL����������
 */
class db_pdo_mysql extends db_pdo {
	/**
	 * ��ȡ���ݱ�ṹ
	 *
	 * @param tbl_name  ������
	 */
	public function getTable($tbl_name){
		return $this->getArray("DESCRIBE {$tbl_name}");
	}
}
/**
 * db_pdo_sqlite PDO Sqlite����������
 */
class db_pdo_sqlite extends db_pdo {
	/**
	 * ��ȡ���ݱ�ṹ
	 *
	 * @param tbl_name  ������
	 */
	public function getTable($tbl_name){
		$tmptable = $this->getArray("SELECT * FROM SQLITE_MASTER WHERE name = '{$tbl_name}' AND type='table'");
		if (FALSE === strpos($tmptable[0]['sql'], '[')){
			$tmp = explode('"',$tmptable[0]['sql']);
			for( $i=1; $i < count ($tmp); $i+=2 ){
				$columns[]['Field'] = $tmp[$i];
			}
		}else{
			$tmp = explode('[',$tmptable[0]['sql']);
			foreach( $tmp as $value ){
				$towarr = explode(']', $value);
				if( isset($towarr[1]) )$columns[]['Field'] = $towarr[0];
			}
		}
		array_shift($columns);
		return $columns;
	}
}

/**
 * db_pdo PDO������ 
 */
class db_pdo {
	/**
	 * ���ݿ����Ӿ��
	 */
	public $conn;
	/**
	 * ִ�е�SQL����¼
	 */
	public $arrSql;
	/**
	 * execִ��Ӱ������
	 */
	private $num_rows;

	/**
	 * ��SQL����ȡ��¼�������������
	 * 
	 * @param sql  ִ�е�SQL���
	 */
	public function getArray($sql)
	{
		$this->arrSql[] = $sql;
		if( ! $rows = $this->conn->prepare($sql) ){
			$poderror = $this->conn->errorInfo();
			spError("{$sql}<br />ִ�д���: " .$poderror[2]);
		}
		$rows->execute();
		return $rows->fetchAll(PDO::FETCH_ASSOC);
	}
	
	/**
	 * ���ص�ǰ�����¼������ID
	 */
	public function newinsertid()
	{
		return $this->conn->lastInsertId();
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
		$result = $this->conn->exec($sql);
		if( FALSE !== $result ){
			$this->num_rows = $result;
			return $result;
		}else{
			$poderror = $this->conn->errorInfo();
			spError("{$sql}<br />ִ�д���: " .$poderror[2]);
		}
	}
	
	/**
	 * ����Ӱ������
	 */
	public function affected_rows()
	{
		return $this->num_rows;
	}

	/**
	 * ��ȡ���ݱ�ṹ
	 *
	 * @param tbl_name  ������
	 */
	public function getTable($tbl_name){}

	/**
	 * ���캯��
	 *
	 * @param dbConfig  ���ݿ�����
	 */
	public function __construct($dbConfig)
	{
		if(!class_exists("PDO"))spError('PHP����δ��װPDO�����⣡');
		try {
		    $this->conn = new PDO($dbConfig['host'], $dbConfig['login'], $dbConfig['password'],array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES GBK")); 
		} catch (PDOException $e) {
		    spError('���ݿ����Ӵ���/�޷��ҵ����ݿ� :  ' . $e->getMessage());
		}
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
		return $this->conn->quote($value);
		//$value = "'{$value}'";
	}

	/**
	 * ��������
	 */
	public function __destruct(){
		$this->conn = null;
	}
	
	/**
	 * getConn ȡ��PDO����
	 */
	public function getConn()
	{
		return $this->conn;
	}
}

