<?php 
require_once './grafika/src/autoloader.php';
require_once './txt2img.php';
require_once './imageMerge.php';
use Grafika\Grafika; // Import package
use Grafika\Color;

$error='';
$words=getWords();
$url=getImageUrl();
// $url='https://unsplash.it/750/1000/?random';
// echo $url;
$dataArr=array(
  'images' =>   array(
    // array(
    //   'x' => 0, 
    //   'y' => 0, 
    //   'w' => 600, 
    //   'h' => 300, 
    //   'stretch' => true, 
    //   'path' => './image/src/4.jpg', 
    //   ),
    array(
      'x' => -80, 
      'y' => -350, 
      'w' => 90, 
      // 'h' => 300, 
      'stretch' => false, 
      // 'path' => getSealUrl(),  
      'path' => './image/src/11.jpg',  
      // 'path' => "http://yinzhang.388g.com/maker/tmp/C_21.png",  
      ),
    
    array(
      'x' => 0, 
      'y' => 0, 
      'w' => 750, 
      // 'h' => 1100, 
      'stretch' => false, 
      // 'path' => './image/src/10.jpg',  
      'path' => $url,  
      ),
    array(
      'x' => -50, 
      'y' => -50, 
      'w' => 100, 
      'stretch' => false, 
      'path' => './image/src/9.jpg',  
      ),
    ), 
  'texts' =>   array(
    array(
      'x' => 50, 
      'y' => -60, 
      'w' => 520, 
      'color' => '#000000', 
      'fontsize' =>22,
      'stretch' => flase, 
      'lineheightRate' =>1.6,
      'wordWidthRate' =>1.32,
      'font' => './grafika/fonts/chengjishi.ttf', 
      'text' => $words
      ),
//     array(
//       'x' => 60, 
//       'y' => 630, 
//       'w' => 610, 
//       'fontsize' => 28, 
//       'lineheightRate' =>1.6,
//       'color' => '#000000',
//       'stretch' => false, 
//       'font' => './grafika/fonts/chengjishi.ttf',
//       'text' => '
// 其实，我觉得南开的妹子都好不容易，长得那么好看，还要努力学习。
// 不然就会被别的大学学生说：“看，南开的女生，除了长得漂亮，还是长得漂亮，要不我们转学吧！”

// 2017-3-7
// 南开妹纸女生节快乐'
//       ), 
    // array(
    //   'x' => -50, 
    //   'y' => -0.1, 
    //   'w' => 200, 
    //   'fontsize' => 14, 
    //   'font' => './grafika/fonts/chengjishi.ttf', 
    //   'color' => '#ffffff',
    //   'stretch' => false, 
    //   'text' => 'by 南开科技'
    //   ), 
    ),
  'canvas' =>   array(
    'w' => 750, 
    'h' => 1334, 
    'stretch' => false, 
    ),
  );
  $res=new ImageMerge($dataArr);
  // $res->saveImage('./image/dist');
  $res->showImage();



/**
 * 从“一个”网站随机获取一句鸡汤文
 * @return [str] [string]
 */
function getWords(){
  $url="http://wufazhuce.com/one/".rand(100,1600);
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_FILE, $tempImage);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
  curl_setopt($ch, CURLOPT_TIMEOUT, 120);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //不输出结果
  $result=curl_exec($ch);
  $info = curl_getinfo($ch);
  // print_r($info);
  if (!preg_match('/<div class=\"one-cita\">([\s\S]*?)<\/div>/i',$result,$match)) {
    $error='正则匹配失败';
    die($error);
  }
  // print_r($match);
  curl_close($ch);
  return $match[1];
}


/**
 * 从“一个”网站随机获取一幅图片的url
 * @return [string] [img url]
 */
function getImageUrl(){
  $url="http://wufazhuce.com/one/".rand(100,1600);
  // echo $url;
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_FILE, $tempImage);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
  curl_setopt($ch, CURLOPT_TIMEOUT, 120);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //不输出结果
  $result=curl_exec($ch);
  $info = curl_getinfo($ch);
  // print_r($info);
  // 
  if (!preg_match('/<div class=\"one-imagen\">([\s\S]*?)<img src=\"([\s\S]*?)\"/i',$result,$match)) {
    $error='正则匹配失败';
    echo $url;
    die($error);
  }
  // print_r($match);
  curl_close($ch);
  return $match[2];
}

/**
 * 从一个印章生成网站获取一个印章的url
 * api参数：
 * get请求，text是传入的文本，id是印章的种类，signsize大概是图片宽度，rnd暂时不知道是什么，好像不影响
 * @param  [string] $text [印章上的字，2-4个汉字]
 * @return [string]       [印章图片的url]
 */
function getSealUrl($text='重拾旧梦',$id=351,$signsize=90){
  $url="http://yinzhang.388g.com/aosbegin.php?text={$text}&id={$id}&signsize={$signsize}";
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
  curl_setopt($ch, CURLOPT_TIMEOUT, 120);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //不输出结果
  $header[0]="Host: yinzhang.388g.com";
  $header[]="Connection: keep-alive";
  $header[]="Pragma: no-cache";
  $header[]="Cache-Control: no-cache";
  $header[]="Upgrade-Insecure-Requests: 1";
  $header[]="User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36";
  $header[]="Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8";
  $header[]="Accept-Encoding: gzip, deflate, sdch";
  $header[]="Accept-Language: zh-CN,zh;q=0.8,en;q=0.6";
  $header[]='X-FORWARDED-FOR:'.Rand_IP();
  $header[]='CLIENT-IP:'.Rand_IP();
  $header[]="Cookie: bdshare_firstime=1488695809675; city=101030100; pgv_pvi=273187840; pgv_si=s4786613248; Hm_lvt_1c93b08a236fd3a29a5bc35d9eea56ca=1487413606,1488695809,1488805406; Hm_lpvt_1c93b08a236fd3a29a5bc35d9eea56ca=1488805569";
  curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
  $result=curl_exec($ch);
  $info = curl_getinfo($ch);
  // print_r($result);
  if (!preg_match('/\.(.+)\.\./',$result,$match)) {
    $error='正则匹配失败';
    echo $url;
    die($error);
  }
  // print_r($match);
  curl_close($ch);
  return "http://yinzhang.388g.com".$match[1];
}


// function getSealUrl($text='重拾旧梦',$id=351,$signsize=90){
//   $url="http://www.redream.cn/getseal.php";
//   $ch = curl_init($url);
//   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
//   curl_setopt($ch, CURLOPT_TIMEOUT, 120);
//   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //不输出结果
//   $result=curl_exec($ch);
//   $info = curl_getinfo($ch);
//   curl_close($ch);
//   print_r($result);

//   // $url=$result;
//   // $ch = curl_init($url);
//   // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
//   // curl_setopt($ch, CURLOPT_TIMEOUT, 120);
//   // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //不输出结果
//   // $result=curl_exec($ch);
//   // $info = curl_getinfo($ch);
//   // curl_close($ch);
//   // print_r($result);
//   // sleep(23);
//   return $result;
// }


// //随机IP
// function Rand_IP(){

//     $ip2id= round(rand(600000, 2550000) / 10000); //第一种方法，直接生成
//     $ip3id= round(rand(600000, 2550000) / 10000);
//     $ip4id= round(rand(600000, 2550000) / 10000);
//     //下面是第二种方法，在以下数据中随机抽取
//     $arr_1 = array("218","218","66","66","218","218","60","60","202","204","66","66","66","59","61","60","222","221","66","59","60","60","66","218","218","62","63","64","66","66","122","211");
//     $randarr= mt_rand(0,count($arr_1)-1);
//     $ip1id = $arr_1[$randarr];
//     return $ip1id.".".$ip2id.".".$ip3id.".".$ip4id;
// }