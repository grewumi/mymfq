<?php
global $caijiusers,$website;
define('COMISSIONRATESORT',0);
define('SETAJAXTOUZ',0);
define('SETFILETOUZ',1);
$website = array(
	'jiukuaiyou'=>array('actType'=>4,'name'=>'九块邮','rank'=>19), // 九块邮
	'zhe800'=>array('actType'=>5,'name'=>'折800','rank'=>18), // 折800
	'juanpi'=>array('actType'=>11,'name'=>'卷皮折扣','rank'=>17), // 卷皮折扣
	'mizheuz'=>array('actType'=>18,'name'=>'米折U站','rank'=>16), // 米折U站
	'ifengqiang'=>array('actType'=>26,'name'=>'爱疯抢','rank'=>15), // 爱疯抢
//	'shiyonglianmeng'=>array('actType'=>24,'name'=>'试用联盟','rank'=>12), // 试用联盟
	'legou'=>array('actType'=>12,'name'=>'乐购','rank'=>14), // 乐购
//	'taofen8'=>array('actType'=>8,'name'=>'淘粉吧','rank'=>13), // 外站  淘粉吧
	'tejiayitian'=>array('actType'=>14,'name'=>'特价一天','rank'=>12), // 特价一天
//	'yuansu'=>array('actType'=>21,'name'=>'爆划算','rank'=>11), // 爆划算
	'zhekouba'=>array('actType'=>28,'name'=>'折扣吧','rank'=>11), //折扣吧
//	'mmrizhi'=>array('actType'=>27,'name'=>'美美购','rank'=>10), // 美美购
	'mao'=>array('actType'=>17,'name'=>'特价猫','rank'=>9), // 特价猫
	'vipzxhd'=>array('actType'=>13,'name'=>'vip专享活动','rank'=>8), // vip专享活动
	'zhuanbao'=>array('actType'=>15,'name'=>'开心赚宝','rank'=>7), //开心赚宝
	'aitaoba'=>array('actType'=>29,'name'=>'爱淘吧','rank'=>6), //爱淘吧
//	'bujie'=>array('actType'=>30,'name'=>'步街网','rank'=>6), // 步街网
	'yimiaofengqiang'=>array('actType'=>20,'name'=>'一秒疯抢','rank'=>5), // 一秒疯抢
        'yimiaofengqiangdujia'=>array('actType'=>1,'name'=>'一秒疯抢独家','rank'=>5), // 一秒疯抢独家
	'mytehui'=>array('actType'=>6,'name'=>'VIP特惠','rank'=>5), // VIP特惠
	'jiejie'=>array('actType'=>25,'name'=>'姐逛街','rank'=>4), // 姐逛街
	'tealife'=>array('actType'=>7,'name'=>'淘牛品','rank'=>3), // 淘牛品
	'huiyuangou'=>array('actType'=>2,'name'=>'会员购','rank'=>2), // 会员购
	'vipgouyouhui'=>array('actType'=>9,'name'=>'VIP购优惠','rank'=>1), // VIP购优惠
	//
	//'qiang'=>array('actType'=>3,'name'=>'抢牛品','tcounts'=>count($pros->findAll('act_from=3 and '.$where))), // 抢牛品
	//'10mst'=>array('actType'=>10,'name'=>'秒杀通'), // 秒杀通
	//'tejiafengqiang'=>array('actType'=>16,'name'=>'特价疯抢'), // 特价疯抢
	//'ztbest'=>array('actType'=>19,'name'=>'中通优选'), // 中通优选
	//'fengtao'=>array('actType'=>22,'name'=>'今日疯淘'), // 今日疯淘
	//'youpinba'=>array('actType'=>23,'name'=>'抢优品','rank'=>14), // 抢优品
	'none'=>null
);
$caijiusers = array(
	'xinxin'=>array('username'=>'xinxin','password'=>'xin123456'),
	//'xx0123'=>array('username'=>'xx0123','password'=>'xx0123','unick'=>'夜0019w5k/sdL2PXJXimFTsiluFi1udq9'),
	'cong'=>array('username'=>'cong','password'=>'cong123456','nick'=>'折扣吧'),
	'lijie'=>array('username'=>'lijie','password'=>'lijie1234','nick'=>''),
	'x0123'=>array('username'=>'x0123','password'=>'x0123','nick'=>'消灵','unick'=>'x0019wtl+cRC3vM7JYTUBIAXwQsISHmC'),
	'9kuaigou'=>array('username'=>'9kuaigou','password'=>'9kuaigou','nick'=>'九块购'),
	'sqsmb'=>array('username'=>'sqsmb','password'=>'sqsmb','nick'=>'U购'),
	'yuansu'=>array('username'=>'yuansu','password'=>'yuansu','nick'=>'爆划算'),
	'zhe800w'=>array('username'=>'zhe800w','password'=>'zhe800w','nick'=>'vip独家优惠'),
	'ifengqiang'=>array('username'=>'ifengqiang','password'=>'713211','nick'=>'爱疯抢'),
	'shiyonglianmeng'=>array('username'=>'shiyonglianmeng','password'=>'shiyonglianmeng'),
	'jumei'=>array('username'=>'jumei','password'=>'jumei','nick'=>'聚美优品'),
	'126789'=>array('username'=>'126789','password'=>'126789','nick'=>'126789'),
	'loveshe'=>array('username'=>'loveshe','password'=>'loveshe','nick'=>'大乐购'),
	'tiangou'=>array('username'=>'tiangou','password'=>'tiangou','nick'=>'天购'),
	'youpinba'=>array('username'=>'youpinba','password'=>'youpinba','nick'=>'抢优品'),
	'okbuy'=>array('username'=>'okbuy','password'=>'okbuy','nick'=>'好乐买'),
	'mplife'=>array('username'=>'mplife','password'=>'mplife','nick'=>'优选铭品'),
    'haowo'=>array('username'=>'haowo','password'=>'haowo','nick'=>'好喔折扣'),
	'1zhew'=>array('username'=>'1zhew','password'=>'1zhew','nick'=>'1折网'),
	'chuang'=>array('username'=>'chuang','password'=>'chuang','nick'=>'创优品'),
	'360tuan'=>array('username'=>'360tuan','password'=>'360tuan','nick'=>'360团'),
	'tongqu'=>array('username'=>'tongqu','password'=>'tongqu','nick'=>'vip优折'),
	'tbcsh'=>array('username'=>'tbcsh','password'=>'tbcsh','nick'=>'超优惠'),
	'55128'=>array('username'=>'55128','password'=>'55128','nick'=>'爱赶集'),
	'viphuiyuan'=>array('username'=>'viphuiyuan','password'=>'viphuiyuan','nick'=>'vip会员购'),
	'tblgj'=>array('username'=>'tblgj','password'=>'tblgj','nick'=>'乐购街'),
	'tbypt'=>array('username'=>'tbypt','password'=>'tbypt','nick'=>'优品淘'),
	'22888'=>array('username'=>'22888','password'=>'22888','nick'=>'优品折购'),
	'282828'=>array('username'=>'282828','password'=>'282828','nick'=>'特价总汇')
);	
?>