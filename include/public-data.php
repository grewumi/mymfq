<?php
global $caijiusers,$website;
define('COMISSIONRATESORT',0);
define('SETAJAXTOUZ',0);
define('SETFILETOUZ',1);
$website = array(
	'jiukuaiyou'=>array('actType'=>4,'name'=>'�ſ���','rank'=>19), // �ſ���
	'zhe800'=>array('actType'=>5,'name'=>'��800','rank'=>18), // ��800
	'juanpi'=>array('actType'=>11,'name'=>'��Ƥ�ۿ�','rank'=>17), // ��Ƥ�ۿ�
	'mizheuz'=>array('actType'=>18,'name'=>'����Uվ','rank'=>16), // ����Uվ
	'ifengqiang'=>array('actType'=>26,'name'=>'������','rank'=>15), // ������
//	'shiyonglianmeng'=>array('actType'=>24,'name'=>'��������','rank'=>12), // ��������
	'legou'=>array('actType'=>12,'name'=>'�ֹ�','rank'=>14), // �ֹ�
//	'taofen8'=>array('actType'=>8,'name'=>'�Է۰�','rank'=>13), // ��վ  �Է۰�
	'tejiayitian'=>array('actType'=>14,'name'=>'�ؼ�һ��','rank'=>12), // �ؼ�һ��
//	'yuansu'=>array('actType'=>21,'name'=>'������','rank'=>11), // ������
	'zhekouba'=>array('actType'=>28,'name'=>'�ۿ۰�','rank'=>11), //�ۿ۰�
//	'mmrizhi'=>array('actType'=>27,'name'=>'������','rank'=>10), // ������
	'mao'=>array('actType'=>17,'name'=>'�ؼ�è','rank'=>9), // �ؼ�è
	'vipzxhd'=>array('actType'=>13,'name'=>'vipר��','rank'=>8), // vipר��
	'zhuanbao'=>array('actType'=>15,'name'=>'����׬��','rank'=>7), //����׬��
	'aitaoba'=>array('actType'=>29,'name'=>'���԰�','rank'=>6), //���԰�
//	'bujie'=>array('actType'=>30,'name'=>'������','rank'=>6), // ������
	'yimiaofengqiang'=>array('actType'=>20,'name'=>'һ�����','rank'=>5), // һ�����
        'yimiaofengqiangdujia'=>array('actType'=>1,'name'=>'һ���������','rank'=>5), // һ���������
	'mytehui'=>array('actType'=>6,'name'=>'VIP�ػ�','rank'=>5), // VIP�ػ�
	'jiejie'=>array('actType'=>25,'name'=>'����','rank'=>4), // ����
	'tealife'=>array('actType'=>7,'name'=>'��ţƷ','rank'=>3), // ��ţƷ
	'huiyuangou'=>array('actType'=>2,'name'=>'��Ա��','rank'=>2), // ��Ա��
	'vipgouyouhui'=>array('actType'=>9,'name'=>'VIP���Ż�','rank'=>1), // VIP���Ż�
	//
	//'qiang'=>array('actType'=>3,'name'=>'��ţƷ','tcounts'=>count($pros->findAll('act_from=3 and '.$where))), // ��ţƷ
	//'10mst'=>array('actType'=>10,'name'=>'��ɱͨ'), // ��ɱͨ
	//'tejiafengqiang'=>array('actType'=>16,'name'=>'�ؼ۷���'), // �ؼ۷���
	//'ztbest'=>array('actType'=>19,'name'=>'��ͨ��ѡ'), // ��ͨ��ѡ
	//'fengtao'=>array('actType'=>22,'name'=>'���շ���'), // ���շ���
	//'youpinba'=>array('actType'=>23,'name'=>'����Ʒ','rank'=>14), // ����Ʒ
	'none'=>null
);
$caijiusers = array(
	'xinxin'=>array('username'=>'xinxin','password'=>'xin123456'),
	//'xx0123'=>array('username'=>'xx0123','password'=>'xx0123','unick'=>'ҹ0019w5k/sdL2PXJXimFTsiluFi1udq9'),
	'cong'=>array('username'=>'cong','password'=>'cong123456','nick'=>'�ۿ۰�'),
	'lijie'=>array('username'=>'lijie','password'=>'lijie1234','nick'=>''),
	'x0123'=>array('username'=>'x0123','password'=>'x0123','nick'=>'����','unick'=>'x0019wtl+cRC3vM7JYTUBIAXwQsISHmC'),
	'9kuaigou'=>array('username'=>'9kuaigou','password'=>'9kuaigou','nick'=>'�ſ鹺'),
	'sqsmb'=>array('username'=>'sqsmb','password'=>'sqsmb','nick'=>'U��'),
	'yuansu'=>array('username'=>'yuansu','password'=>'yuansu','nick'=>'������'),
	'zhe800w'=>array('username'=>'zhe800w','password'=>'zhe800w','nick'=>'vip�����Ż�'),
	'ifengqiang'=>array('username'=>'ifengqiang','password'=>'713211','nick'=>'������'),
	'shiyonglianmeng'=>array('username'=>'shiyonglianmeng','password'=>'shiyonglianmeng'),
	'jumei'=>array('username'=>'jumei','password'=>'jumei','nick'=>'������Ʒ'),
	'126789'=>array('username'=>'126789','password'=>'126789','nick'=>'126789'),
	'loveshe'=>array('username'=>'loveshe','password'=>'loveshe','nick'=>'���ֹ�'),
	'tiangou'=>array('username'=>'tiangou','password'=>'tiangou','nick'=>'�칺'),
	'youpinba'=>array('username'=>'youpinba','password'=>'youpinba','nick'=>'����Ʒ'),
	'okbuy'=>array('username'=>'okbuy','password'=>'okbuy','nick'=>'������'),
	'mplife'=>array('username'=>'mplife','password'=>'mplife','nick'=>'��ѡ��Ʒ'),
    'haowo'=>array('username'=>'haowo','password'=>'haowo','nick'=>'����ۿ�'),
	'1zhew'=>array('username'=>'1zhew','password'=>'1zhew','nick'=>'1����'),
	'chuang'=>array('username'=>'chuang','password'=>'chuang','nick'=>'����Ʒ'),
	'360tuan'=>array('username'=>'360tuan','password'=>'360tuan','nick'=>'360��'),
	'tongqu'=>array('username'=>'tongqu','password'=>'tongqu','nick'=>'vip����'),
	'tbcsh'=>array('username'=>'tbcsh','password'=>'tbcsh','nick'=>'���Ż�'),
	'55128'=>array('username'=>'55128','password'=>'55128','nick'=>'���ϼ�'),
	'viphuiyuan'=>array('username'=>'viphuiyuan','password'=>'viphuiyuan','nick'=>'vip��Ա��'),
	'tblgj'=>array('username'=>'tblgj','password'=>'tblgj','nick'=>'�ֹ���'),
	'tbypt'=>array('username'=>'tbypt','password'=>'tbypt','nick'=>'��Ʒ��'),
	'22888'=>array('username'=>'22888','password'=>'22888','nick'=>'��Ʒ�۹�'),
	'282828'=>array('username'=>'282828','password'=>'282828','nick'=>'�ؼ��ܻ�')
);	
?>