<?php
//判断当前用户登录状态
function checkauth() {
	if($_COOKIE[$GLOBALS['G_SP']['SC']['cookiepre'].'auth']) {
		@list($password, $uid) = explode('\t', authcode($_COOKIE[$GLOBALS['G_SP']['SC']['cookiepre'].'auth'], 'DECODE'));
		$GLOBALS['G_SP']['supe_uid'] = intval($uid);
		/* if($password && $_SGLOBAL['supe_uid']) {
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('session')." WHERE uid='$_SGLOBAL[supe_uid]'");
			if($member = $_SGLOBAL['db']->fetch_array($query)) {
				if($member['password'] == $password) {
					$_SGLOBAL['supe_username'] = addslashes($member['username']);
					$_SGLOBAL['session'] = $member;
				} else {
					$_SGLOBAL['supe_uid'] = 0;
				}
			} else {
				$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('member')." WHERE uid='$_SGLOBAL[supe_uid]'");
				if($member = $_SGLOBAL['db']->fetch_array($query)) {
					if($member['password'] == $password) {
						$_SGLOBAL['supe_username'] = addslashes($member['username']);
						$session = array('uid' => $_SGLOBAL['supe_uid'], 'username' => $_SGLOBAL['supe_username'], 'password' => $password);
						include_once(S_ROOT.'./source/function_space.php');
						insertsession($session);//登录
					} else {
						$_SGLOBAL['supe_uid'] = 0;
					}
				} else {
					$_SGLOBAL['supe_uid'] = 0;
				}
			}
		}*/
	} 
	/* if(empty($GLOBALS['G_SP']['supe_uid'])) {
		clearcookie();
	} else {
		;//$_SGLOBAL['username'] = $member['username'];
	}  */
	
}
?>