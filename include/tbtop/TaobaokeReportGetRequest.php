<?php
/**
 * TOP API: taobao.taobaoke.report.get request
 * 
 * @author auto create
 * @since 1.0, 2013-03-07 16:37:24
 */
class TaobaokeReportGetRequest
{
	/** 
	 * ��Ҫ��ѯ��������ڣ���Ч������Ϊ���3�����ڵ�ĳһ�죬��ʽΪ:yyyyMMdd,��20090520.
	 **/
	private $date;
	
	/** 
	 * �践�ص��ֶ��б�.��ѡֵ:TaobaokeReportMember�Ա��ͱ����Ա�ṹ���е������ֶ�;�ֶ�֮����","�ָ�.
	 **/
	private $fields;
	
	/** 
	 * ��ǰҳ��.ֻ�ܻ�ȡ1-499ҳ����.
	 **/
	private $pageNo;
	
	/** 
	 * ÿҳ���ؽ����,Ĭ����40��.���ÿҳ100
	 **/
	private $pageSize;
	
	private $apiParas = array();
	
	public function setDate($date)
	{
		$this->date = $date;
		$this->apiParas["date"] = $date;
	}

	public function getDate()
	{
		return $this->date;
	}

	public function setFields($fields)
	{
		$this->fields = $fields;
		$this->apiParas["fields"] = $fields;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function setPageNo($pageNo)
	{
		$this->pageNo = $pageNo;
		$this->apiParas["page_no"] = $pageNo;
	}

	public function getPageNo()
	{
		return $this->pageNo;
	}

	public function setPageSize($pageSize)
	{
		$this->pageSize = $pageSize;
		$this->apiParas["page_size"] = $pageSize;
	}

	public function getPageSize()
	{
		return $this->pageSize;
	}

	public function getApiMethodName()
	{
		return "taobao.taobaoke.report.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->date,"date");
		RequestCheckUtil::checkNotNull($this->fields,"fields");
		RequestCheckUtil::checkMaxValue($this->pageNo,499,"pageNo");
		RequestCheckUtil::checkMinValue($this->pageNo,1,"pageNo");
		RequestCheckUtil::checkMaxValue($this->pageSize,100,"pageSize");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
