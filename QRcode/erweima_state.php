<?php

include 'phpqrcode.php';
header('Content-type: image/png');

/**
 * 生成二维码
 * @param string $value
 * @param $url | 生成的原始二维码图地址
 * @param string $logo | 添加logo
 */
function get_qrcode($value = '', $url = '', $logo = '') {
    $errorCorrectionLevel = 'H';//容错级别
    $matrixPointSize      = 9;//生成图片大小
    //生成二维码图片
    QRcode::png($value, $url, $errorCorrectionLevel, $matrixPointSize, 1);

    if ($logo != FALSE) {
        //获取二维码
        $qr_code       = file_get_contents($url);
        $qr_code       = imagecreatefromstring($qr_code);
        $qr_code_width = imagesx($qr_code);
        //获得头像
        $logo = get_corner($logo, 50,30);
        $logo_width  = imagesx($logo);
        //将真彩色图像转换为调色板图像,解决jpeg合并颜色丢失显示为黑白的问题
        imagetruecolortopalette($qr_code, false, 255);
        imagetruecolortopalette($logo, false, 255);

        //计算圆角图片的宽高及相对于二维码的摆放位置,将圆角图片拷贝到二维码中央
        $corner_qr_height = $corner_qr_width = $qr_code_width / 4;
        $from_width       = ($qr_code_width - $corner_qr_width) / 2;
        imagecopyresampled($qr_code, $logo, $from_width, $from_width, 0, 0, $corner_qr_width, $corner_qr_height, $logo_width, $logo_width);
        imagepng($qr_code, $url);
    }
}





/**
 * 生成圆弧图片
 * @param $url  | 图片地址
 * @param $radius | 弧度大小
 * @param int $border | 边框厚度
 * @return resource
 */
function get_corner($url, $radius,$border=10) {
    $img         = file_get_contents($url);
    $img         = imagecreatefromstring($img);
    $img_width  = imagesx($img);

    $corner = get_lt_rounder_corner($radius);
    $gps    = $img_width - $radius;
    for ($i = 0; $i < 4; $i++) {
        $corner = $i>0?imagerotate($corner, 90, 0):$corner;
        imagecopymerge($img, $corner, $i>1?$gps:0, $i>0&&$i<3?$gps:0, 0, 0, $radius, $radius, 100);
    }
    // 创建背景
    $img_border    = imagecreatetruecolor($img_width+$border,$img_width+$border);
    $bg_color = imagecolorallocate($img, 255, 255, 255);
    imagefill($img_border , 0, 0, $bg_color);

    imagecopyresampled($img_border, $img, $border, $border, 0, 0, $img_width-$border, $img_width-$border, $img_width, $img_width);

    $corner_border = get_lt_rounder_corner($radius+$border,['0','255','0']);
    $gps    = $img_width - $radius;
    for ($i = 0; $i < 4; $i++) {
        $corner_border = $i>0?imagerotate($corner_border, 90, 0):$corner_border;
        imagecopymerge($img_border, $corner_border, $i>1?$gps:0, $i>0&&$i<3?$gps:0, 0, 0, $radius+$border, $radius+$border, 100);
    }

    $fg_color    = imagecolorallocate($img_border, 0, 255, 0);
    imagecolortransparent($img_border, $fg_color);

    return $img_border;
}


/*生成圆弧*/
function get_lt_rounder_corner($radius,$clocr=['255','255','255']) {
    $img      = imagecreatetruecolor ($radius, $radius);  // 创建一个正方形的图像
    $bg_color = imagecolorallocate($img, $clocr[0], $clocr[1], $clocr[2]);   // 图像的背景
    $fg_color = imagecolorallocate($img, 0, 0, 0);
    imagefill($img, 0, 0, $bg_color);
    imagefilledarc($img, $radius, $radius, $radius * 2, $radius * 2, 180, 270, $fg_color, IMG_ARC_PIE);
    imagecolortransparent($img, $fg_color);
    return $img;
}
get_qrcode('https://hao.360.cn/?wd=1000', 'pic/qrcode.png', '666.jpeg');