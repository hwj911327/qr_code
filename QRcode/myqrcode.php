<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/19
 * Time: 16:35
 */
include 'phpqrcode.php';

class myqrcode {
    public $errorCorrectionLevel = 'H';
    public $matrixPointSize      = 9;


    /**
     * 获取二维码
     * @param string $value | 二维码内容
     * @param string $url | 二维码保存地址
     * @param string $logo | 二维码中间是否有logo
     */
    function get_qrcode($value = '', $url = '', $logo = '') {
        /*生成二维码图片*/
        QRcode::png($value, $url, $this->errorCorrectionLevel, $this->matrixPointSize, 1);
        /*添加中心logo头像*/
        if ($logo != FALSE) {
            //获取二维码
            $qr_code       = file_get_contents($url);
            $qr_code       = imagecreatefromstring($qr_code);
            $qr_code_width = imagesx($qr_code);
            //获得头像
            $logo       = $this->chamfering($logo, 50, 30);
            $logo_width = imagesx($logo);
            //计算圆角图片的宽高及相对于二维码的摆放位置,将圆角图片拷贝到二维码中央
            $corner_qr_height = $corner_qr_width = $qr_code_width / 4;
            $from_width       = ($qr_code_width - $corner_qr_width) / 2;
            imagecopyresampled($qr_code, $logo, $from_width, $from_width, 0, 0, $corner_qr_width, $corner_qr_height, $logo_width, $logo_width);
            imagepng($qr_code, $url);
        }

    }


    /**
     * 将原图变为圆角(0)
     * @param $url | 图片地址
     * @param $radius | 弧度大小
     * @param int $border | 边框厚度
     * @return resource
     */
    public function chamfering($url, $radius, $border = 10) {
        /*获得原图*/
        $img       = file_get_contents($url);
        $img       = imagecreatefromstring($img);
        $img_width = imagesx($img);

        /*原图倒角*/
        $corner = $this->_get_corner($radius);
        $gps    = $img_width - $radius;
        for ($i = 0; $i < 4; $i++) {
            $corner = $i > 0 ? imagerotate($corner, 90, 0) : $corner;
            imagecopymerge($img, $corner, $i > 1 ? $gps : 0, $i > 0 && $i < 3 ? $gps : 0, 0, 0, $radius, $radius, 100);
        }
        /*创建边框*/
        $img_border = imagecreatetruecolor($img_width + $border, $img_width + $border);
        $bg_color   = imagecolorallocate($img, 255, 255, 255);
        imagefill($img_border, 0, 0, $bg_color);
        /*与原图合并*/
        imagecopyresampled($img_border, $img, $border, $border, 0, 0, $img_width - $border, $img_width - $border, $img_width, $img_width);
        /*新图倒角*/
        $corner_border = $this->_get_corner($radius + $border, ['0', '255', '0']);
        $gps           = $img_width - $radius;
        for ($i = 0; $i < 4; $i++) {
            $corner_border = $i > 0 ? imagerotate($corner_border, 90, 0) : $corner_border;
            imagecopymerge($img_border, $corner_border, $i > 1 ? $gps : 0, $i > 0 && $i < 3 ? $gps : 0, 0, 0, $radius + $border, $radius + $border, 100);
        }
        /*倒角透明*/
        imagecolortransparent($img_border, imagecolorallocate($img_border, 0, 255, 0));

        return $img_border;
    }

    /**
     * 生成一个角的圆弧(圆弧内为透明)
     * @param $radius | 圆弧半径
     * @param array $clocr | 非透明处颜色
     * @return resource
     */
    private function _get_corner($radius, $clocr = ['255', '255', '255']) {
        $img      = imagecreatetruecolor($radius, $radius);  // 创建一个正方形的图像
        $bg_color = imagecolorallocate($img, $clocr[0], $clocr[1], $clocr[2]);   // 图像的背景
        $fg_color = imagecolorallocate($img, 0, 0, 0); //圆弧颜色
        imagefill($img, 0, 0, $bg_color);
        imagefilledarc($img, $radius, $radius, $radius * 2, $radius * 2, 180, 270, $fg_color, IMG_ARC_PIE);
        imagecolortransparent($img, $fg_color); //圆弧变为透明
        return $img;
    }
}

$logo='http://'.$_SERVER['HTTP_HOST'].'/test/QRcode/pic/5689.jpg';




$qrcode = new myqrcode();
$qrcode->get_qrcode('http://xgf.quduoduo.cc/', 'pic/qrcode.png',$logo);