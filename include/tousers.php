<?php

/* 
 * 用户相关
 */
function switchtogrouppage($group){
	switch ($group){
		case '5':
			header("Location:/");//用户选择界面
			break;
		case '4'://商家
			header("Location:/?c=user&a=iinfo");
			//header("Location:#");
			break;
		default:
			header("Location:/?c=user&a=iinfo");
			break;
	}
}

?>
