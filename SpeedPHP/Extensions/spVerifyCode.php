<?php
/**
 * SpeedPHP��֤��������
 */
class spVerifyCode {
	public $width = 60; //���
	public $height = 20; //�߶�
	public $len = 4; //�ַ�����
	public $backcolor = '#FFFFFF'; //����ɫ
	public $bordercolor = null; //�߿�ɫ
	public $noisenum = NULL ; //�ӵ�����

	public $textsize = 22; //�����С
	public $font = "font.ttf"; //�Զ�������
	public $format = 'png'; //���ͼƬ��ʽ
	public $imagename;
	protected $image;
	protected $backcolorRGB;
	protected $bordercolorRGB = NULL;
	protected $size;
	protected $sizestr2str;
	public $vcode = NULL; //��֤������(����)

	private $vc_session = NULL;

	public function  __construct() {
		$this->font = str_replace('\\', '/', dirname(__FILE__)).'/font.ttf';
		$this->vc_session = &$_SESSION[$GLOBALS['G_SP']['sp_app_id']]['verifycode'];

		$params = spExt('spVerifyCode');
		if( !empty($params['width']) )$this->width = $params['width'];
		if( !empty($params['height']) )$this->height = $params['height'];
		if( !empty($params['length']) )$this->len = $params['length'];
		if( !empty($params['bgcolor']) )$this->backcolor = $params['bgcolor'];
		if( !empty($params['noisenum']) )$this->noisenum = $params['noisenum'];
		if( !empty($params['fontsize']) )$this->textsize = $params['fontsize'];
		if( !empty($params['fontfile']) )$this->font = str_replace('\\', '/', dirname(__FILE__)).'/'.$params['fontfile'];
		if( !empty($params['format']) )$this->format = strtolower($params['format']);
	}

	public function display() {
		$this->make_img();
		$this->vc_session = $this->vcode;
		$this->show_img();
		exit();
	}

	public function verify($var, $is_clear = TRUE) {
		$result = FALSE;
		if($var == $this->vc_session) {
			$result = TRUE;
		}
		if($is_clear) $this->vc_session = '';
		return $result;
	}

	public function show_img() {
		@header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		@header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		@header("Cache-Control: no-store, no-cache, must-revalidate");
		@header("Pragma: no-cache");
		if($this->format == 'png') {
			@header("Content-type: image/png");
			imagepng($this->image);
		}elseif($this->format == 'jpg') {
			@header("Content-type: image/jpeg");
			imagejpeg($this->image);
		}else{
			@header("Content-type: image/gif");
			imagegif($this->image);
		}
		imagedestroy($this->image);
	}

	public function make_img() {
		$this->image = imageCreate($this->width, $this->height); //����ͼƬ
		$this->backcolorRGB = $this->getcolor($this->backcolor);   //��#ffffff��ʽ�ı���ɫת����RGB��ʽ
		imageFilledRectangle($this->image, 0, 0, $this->width, $this->height, $this->backcolorRGB); //��һ���� �����
		$this->size = $this->width/$this->len; //��ȳ����ַ��� = ÿ���ַ���Ҫ�Ŀ��
		if($this->size>$this->height) $this->size=$this->height; //��� ÿ���ַ���Ҫ�Ŀ�� ����ͼƬ�߶� �� ���ַ����=�߶�(������)
		$this->sizestr2str = $this->size/10 ; //��ÿ���ַ���1/10���Ϊ �ַ����
		$left = ($this->width-$this->len*($this->size+$this->sizestr2str))/$this->size;   // (��֤��ͼƬ��� - ʵ����Ҫ�Ŀ��)/ÿ���ַ��Ŀ�� = ������ߵĿ��
		for($i = 0; $i < 3; $i++) {  //���ɸ�����
			$linecolor = imagecolorallocate($this->image, rand(0,255), rand(0,255), rand(0,255));
			imageline($this->image, rand(0,30), rand(0,30), rand(30,80), rand(0,30), $linecolor);
		}
		for($i=0; $i<$this->len; $i++) {
			$randtext = rand(0, 9);  //��֤������ 0-9�����
			$this->vcode .= $randtext; //д��session������
			$textColor = imageColorAllocate($this->image, rand(50, 155), rand(50, 155), rand(50, 155)); //ͼƬ������ɫ
			if (!isset($this->textsize) ) $this->textsize = rand(($this->size-$this->size/10), ($this->size + $this->size/10)); //���δ���������С ��ȡ�����С
			$location = $left + ($i*$this->size+$this->size/10);
			imagettftext($this->image, $this->textsize, rand(-18,18), $location, rand($this->size-$this->size/10, $this->size+$this->size/10), $textColor, $this->font, $randtext); //���ɵ�������ͼ��
		}
		if(isset($this->noisenum)) $this->setnoise(); //�ӵ㴦��

		if(isset($this->bordercolor)){
			$this->bordercolorRGB = $this->getcolor($this->bordercolor);
			imageRectangle($this->image, 0, 0, $this->width-1, $this->height-1, $this->bordercolorRGB);
		}
	}

	protected function getcolor($color) {
		$color = eregi_replace ("^#","",$color);
		$r = $color[0].$color[1];
		$r = hexdec ($r);
		$b = $color[2].$color[3];
		$b = hexdec ($b);
		$g = $color[4].$color[5];
		$g = hexdec ($g);
		$color = imagecolorallocate ($this->image, $r, $b, $g);
		return $color;
	}

	protected function setnoise() {
		for ($i=0; $i<$this->noisenum; $i++) {
			$randColor = imageColorAllocate($this->image, rand(0, 255), rand(0, 255), rand(0, 255));
			imageSetPixel($this->image, rand(0, $this->width), rand(0, $this->height), $randColor);
		}
	}
}
/* End of this file */