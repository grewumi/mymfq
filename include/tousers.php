<?php

/* 
 * �û����
 */
function switchtogrouppage($group){
	switch ($group){
		case '5':
			header("Location:/");//�û�ѡ�����
			break;
		case '4'://�̼�
			header("Location:/?c=user&a=iinfo");
			//header("Location:#");
			break;
		default:
			header("Location:/?c=user&a=iinfo");
			break;
	}
}

?>
