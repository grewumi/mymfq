<?php
/**
 * Session��չ��
 *
 * @author starlight36
 */
class spSession {
	private $session;

	function  __construct() {
		$this->session = &$_SESSION[$GLOBALS['G_SP']['sp_app_id']]['session'];
	}

	/**
	 * ֧����path��ʽ����Sessionֵ
	 * @param string $key
	 * @return mixed
	 */
	function get($key = NULL) {
		return $this->path_array($this->session, $key);
	}

	/**
	 * �����ݴ���SESSION, ֧��path��ʽ����
	 * @param string $key
	 * @param mixed $value
	 * @return bool
	 */
	function put($key, $value) {
		$array =& $this->path_array($this->session, $key);
		$array = $value;
		return TRUE;
	}

	/**
	 * Path��ʽ��������
	 * @param minxed &$array
	 * @param string $path
	 * @return mixed
	 */
	private function &path_array(&$array, $path = NULL) {
		if(empty($path) || !is_array($array)) {
			return $array;
		}else{
			$arr_path = explode('/', $path);
			$path = NULL;
			foreach($arr_path as $v){
				$path .= '[\''.addslashes($v).'\']';
			}
			eval('$value =& $array'.$path.';');
			return $value;
		}
	}
}
/* End of this file */
