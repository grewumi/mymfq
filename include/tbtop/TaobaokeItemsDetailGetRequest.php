<?php
/**
 * TOP API: taobao.taobaoke.items.detail.get request
 * 
 * @author auto create
 * @since 1.0, 2013-03-07 16:37:24
 */
class TaobaokeItemsDetailGetRequest
{
	/** 
	 * �践�ص��ֶ��б�.��ѡֵ:TaobaokeItemDetail�Ա�����Ʒ�ṹ���е������ֶ�;�ֶ�֮����","�ָ���item_detail��Ҫ���õ�Itemģ���µ��ֶ�,������:num_iid,detail_url��; ֻ����item_detail,�򲻷��ص�Item�µ�������Ϣ.ע��item�ṹ�е�skus��videos��props_name������
	 **/
	private $fields;
	
	/** 
	 * ��ʶһ��Ӧ���Ƿ��������߻����ֻ�Ӧ��,�����true���ʹ������������ܵ����.�������ֵ,��Ĭ����false.
	 **/
	private $isMobile;
	
	/** 
	 * �Ա��û��ǳƣ�ע��ָ�����Ա��Ļ�Ա��¼��.����ǳƴ���,��ô�ͻ����ղ���Ӷ��.ÿ���Ա��ǳƶ���Ӧ��һ��pid������������Ҫ����Ӷ����Ա��ǳƣ����ƹ����Ʒ�ɹ���Ӷ�������������Ա��ǳƵ��˻����������Ϣ���Ե��밢���������վ�鿴.
	 **/
	private $nick;
	
	/** 
	 * �Ա�����Ʒ����id��.�������10��.��ʽ��:"value1,value2,value3" ��" , "�ŷָ���Ʒid.
	 **/
	private $numIids;
	
	/** 
	 * �Զ������봮.��ʽ:Ӣ�ĺ��������;���Ȳ��ܴ���12���ַ�,���ֲ�ͬ���ƹ�����,��:bbs,��ʾbbsΪ�ƹ�����;blog,��ʾblogΪ�ƹ�����.
	 **/
	private $outerCode;
	
	/** 
	 * �û���pid,������mm_xxxx_0_0���ָ�ʽ�м��"xxxx". ע��nick��pid������Ҫ����һ��,���2��������,����pidΪ׼,��pid����󳤶���20����һ�ε��ýӿڵ��û����Ƽ�����β�Ҫ��д��ʹ��nick=���Ա��˺ţ��ķ�ʽȥ��ȡ���������
	 **/
	private $pid;
	
	/** 
	 * ��Ʒtrack_iid��������׷��Ч������Ʒid),�������10��,��num_iids������һ
	 **/
	private $trackIids;
	
	private $apiParas = array();
	
	public function setFields($fields)
	{
		$this->fields = $fields;
		$this->apiParas["fields"] = $fields;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function setIsMobile($isMobile)
	{
		$this->isMobile = $isMobile;
		$this->apiParas["is_mobile"] = $isMobile;
	}

	public function getIsMobile()
	{
		return $this->isMobile;
	}

	public function setNick($nick)
	{
		$this->nick = $nick;
		$this->apiParas["nick"] = $nick;
	}

	public function getNick()
	{
		return $this->nick;
	}

	public function setNumIids($numIids)
	{
		$this->numIids = $numIids;
		$this->apiParas["num_iids"] = $numIids;
	}

	public function getNumIids()
	{
		return $this->numIids;
	}

	public function setOuterCode($outerCode)
	{
		$this->outerCode = $outerCode;
		$this->apiParas["outer_code"] = $outerCode;
	}

	public function getOuterCode()
	{
		return $this->outerCode;
	}

	public function setPid($pid)
	{
		$this->pid = $pid;
		$this->apiParas["pid"] = $pid;
	}

	public function getPid()
	{
		return $this->pid;
	}

	public function setTrackIids($trackIids)
	{
		$this->trackIids = $trackIids;
		$this->apiParas["track_iids"] = $trackIids;
	}

	public function getTrackIids()
	{
		return $this->trackIids;
	}

	public function getApiMethodName()
	{
		return "taobao.taobaoke.items.detail.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->fields,"fields");
		RequestCheckUtil::checkMaxListSize($this->numIids,10,"numIids");
		RequestCheckUtil::checkMaxLength($this->outerCode,12,"outerCode");
		RequestCheckUtil::checkMaxListSize($this->trackIids,10,"trackIids");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
