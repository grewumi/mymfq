<?php
/**
 * TOP API: taobao.itemcats.get request
 * 
 * @author auto create
 * @since 1.0, 2013-04-24 16:44:35
 */
class ItemcatsGetRequest
{
	/** 
	 * ��Ʒ������ĿID�б��ð�Ƕ���(,)�ָ� ����:(18957,19562,) (cids��parent_cid���ٴ�һ��)
	 **/
	private $cids;
	
	/** 
	 * ��Ҫ���ص��ֶ��б���ItemCat��Ĭ�Ϸ��أ�cid,parent_cid,name,is_parent
	 **/
	private $fields;
	
	/** 
	 * ����Ʒ��Ŀ id��0��ʾ���ڵ�, ����ò���������������Ŀ�� (cids��parent_cid���ٴ�һ��)
	 **/
	private $parentCid;
	
	private $apiParas = array();
	
	public function setCids($cids)
	{
		$this->cids = $cids;
		$this->apiParas["cids"] = $cids;
	}

	public function getCids()
	{
		return $this->cids;
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

	public function setParentCid($parentCid)
	{
		$this->parentCid = $parentCid;
		$this->apiParas["parent_cid"] = $parentCid;
	}

	public function getParentCid()
	{
		return $this->parentCid;
	}

	public function getApiMethodName()
	{
		return "taobao.itemcats.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkMaxListSize($this->cids,1000,"cids");
		RequestCheckUtil::checkMaxValue($this->parentCid,9223372036854775807,"parentCid");
		RequestCheckUtil::checkMinValue($this->parentCid,0,"parentCid");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
