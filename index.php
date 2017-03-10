<?php 
require_once './grafika/src/autoloader.php';
use Grafika\Grafika; // Import package
use Grafika\Color;
$editor = Grafika::createEditor(); // Create the best available editor
//处理背景图
$editor->open( $bg_img, 'image/src/3.jpg'); // open bg image

//处理要添加的图片
// $editor->open($img , 'image/src/2.jpg'); // 
//下载远程图片流
    $url = 'https://unsplash.it/200/300/?random';
  $fp=fopen('./girl.jpg', 'w');
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); 
    $out=curl_exec($ch);
    curl_close($ch);
fclose($fp);
    $editor->open($img , './girl.jpg'); // 

    // exit();
 


// 宽度为200 等比缩放
$editor->resizeExactWidth($img , 200);
//把打开的图片放在背景图上
$editor->blend ( $bg_img, $img , 'normal', 0.9, 'bottom-right',-100,-100);



//处理要添加的文字
$editor->text($bg_img ,"命运就是颠\r\n沛流离，别\n流泪伤心",30,200,100,new Color("#000000"),'./grafika/fonts/hanyikaiti.ttf',0);




//保存图片
$editor->save($bg_img , 'image/dist/1.jpg');





//输出图片
header('Content-type: image/png'); // Tell the browser we're sending a png image
$bg_img->blob('PNG'); 
 ?>