<?php
class ajaxdata extends spController{
	public function index(){
		$pros = spClass("m_pro");
		$baseSql = 'st<=curdate() and et>=curdate() and ischeck=1 and type!=87';
		$order = 'rank asc,postdt desc';
		$items = $pros->findAll($baseSql,$order,'',150);
		foreach($items as $k=>$v){
			echo 'update fstk_pro set rank="'.$v['rank'].'",postdt="'.$v['postdt'].'",pic="'.$v['pic'].'" where iid='.$v['iid'].';';
		}	
	}
        public function ajaxpic(){
            set_time_limit(0);
            // 采集开春哥
            ini_set('memory_limit', '64M'); // 内存超载
            ini_set('pcre.backtrack_limit', 999999999); // 回溯超载
            ini_set('pcre.recursion_limit', 99999); // 资源开大就行
            
            import('tbapi.php');
            import("function_login_taobao.php");
            $pros = spClass("m_pro");
            $baseSql = 'st<=curdate() and et>=curdate() and ischeck=1 and postdt>=curdate()';
            $items = $pros->findAll($baseSql);
            echo '今日图片更新开始！';
            foreach($items as $k=>$v){
                $item = getItemDetail($v['iid']);
//                var_dump($item);
                $updata['pic'] = $item['pic'];
//                echo $item['pic'].'22222';
                $pros->update(array('iid'=>$item['iid']),$updata);
                echo $item['iid'].'更新图片成功！！';
            }
        }

}