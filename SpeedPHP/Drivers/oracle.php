<?php
/////////////////////////////////////////////////////////////////
// SpeedPHP����PHP���, Copyright (C) 2008 - 2010 SpeedPHP.com //
/////////////////////////////////////////////////////////////////

/**
 * db_oracle Oracle���ݿ������֧��
 */
class db_oracle {
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
		$result = $this->exec($sql);
		oci_fetch_all($result, $res, null, null, OCI_FETCHSTATEMENT_BY_ROW);
		oci_free_statement($result);
		return $res;
	}
	
	/**
	 * ���ص�ǰ�����¼������ID
	 */
	public function newinsertid()
	{
		return FALSE; // ʹ��spModel��create�����в���������ID
	}
	
	/**
	 * ��ʽ����limit��SQL���
	 */
	public function setlimit($sql, $limit)
	{
		$limitarr = explode(',',str_replace(' ','',$limit));
		$total = (isset($limitarr[1])) ? ($limitarr[1] + $limitarr[0]) : $limitarr[0];
		$start = (isset($limitarr[1])) ? $limitarr[0] : 0;
		return "SELECT * FROM ( SELECT SPTMP_LIMIT_TBLNAME.*, ROWNUM SPTMP_LIMIT_ROWNUM FROM ({$sql}) SPTMP_LIMIT_TBLNAME WHERE ROWNUM <= {$total} )WHERE SPTMP_LIMIT_ROWNUM > {$start}";
	}

	/**
	 * ִ��һ��SQL���
	 * 
	 * @param sql ��Ҫִ�е�SQL���
	 */
	public function exec($sql)
	{
		$this->arrSql[] = $sql;
		$result = oci_parse($this->conn, $sql);
		if( !oci_execute($result) ){$e = oci_error($result);spError("{$sql}<br />ִ�д���: " . strip_tags($e['message']));}
		$this->num_rows = oci_num_rows($result);
		return $result;
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
	public function getTable($tbl_name)
	{
		$tbl_name = strtoupper($tbl_name);
		$upcaseres = $this->getArray("SELECT COLUMN_NAME AS FIELD FROM USER_TAB_COLUMNS WHERE TABLE_NAME = '{$tbl_name}'");
		foreach( $upcaseres as $k => $v )$upcaseres[$k] = array('Field'=>$v['FIELD']);
		return $upcaseres;
	}

	/**
	 * ���캯��
	 *
	 * @param dbConfig  ���ݿ�����
	 */
	public function __construct($dbConfig)
	{
		if(!function_exists('oci_connect'))spError('PHP����δ��װORACLE�����⣡');
		$linkfunction = ( TRUE == $dbConfig['persistent'] ) ? 'oci_pconnect' : 'oci_connect';
		if( ! $this->conn = $linkfunction($dbConfig['login'], $dbConfig['password'], $dbConfig['host']) ){
			$e = oci_error();spError('���ݿ����Ӵ��� : ' . strip_tags($e['message']));
		}
		$this->exec('ALTER SESSION SET NLS_DATE_FORMAT = \'yyyy-mm-dd hh24:mi:ss\'');
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
		$search=array("\\","\0","\n","\r","\x1a","'",'"');
        $replace=array("\\\\","\\0","\\n","\\r","\Z","\'",'\"');
        return '\''.str_replace($search,$replace,$value).'\'';
	}

	/**
	 * ��������
	 */
	public function __destruct()
	{
		if( TRUE != $GLOBALS['G_SP']['db']['persistent'] )@oci_close($this->conn);
	}
}
