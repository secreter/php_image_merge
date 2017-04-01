<?php 
require_once './grafika/src/autoloader.php';
require_once './txt2img.php';
require_once './imageMerge.php';
use Grafika\Grafika; // Import package
use Grafika\Color;
Date_default_timezone_set("PRC");

$error='';
$words=getWords();
$url=getImageUrl();


// $url='https://unsplash.it/750/1000/?random';
// echo $url;
$dataArr=array(
  'images' =>   array(
    array(
      'x' => -50, 
      'y' => -220, 
      'w' => 50, 
      // 'h' => 300, 
      'stretch' => false, 
      // 'path' => getSealUrl(),  
      'path' => './image/src/11.jpg',  
      // 'path' => "http://yinzhang.388g.com/maker/tmp/C_21.png",  
      ),
    array(
      'x' => 0, 
      'y' => 0, 
      'w' => 562, 
      // 'h' => 1100, 
      'stretch' => false, 
      // 'path' => './image/src/10.jpg',  
      'path' => $url,  
      ),
    array(
      'x' => -40, 
      'y' => -40, 
      'w' => 80, 
      'stretch' => false, 
      'path' => './image/src/9.png',  
      ),
    ), 
  'texts' =>   array(
    array(
      'x' => 30, 
      'y' => -30, 
      'w' => 400, 
      'color' => '#000000', 
      'fontsize' =>18,
      'stretch' => flase, 
      'lineheightRate' =>1.6,
      'wordWidthRate' =>1.32,
      'font' => './grafika/fonts/chengjishi.ttf', 
      'text' => $words
      ),

    
    ),
  'canvas' =>   array(
    'w' => 562, 
    'h' => 1000, 
    'stretch' => false, 
    ),
  );
  // $_POST['userword']="清新小文艺，出门老黄历。\nRedream上线新功能——朋友圈配图生成，可自定义文字生成文艺图片，若无文字，默认生成当天老黄历。\n回复“图片”或识别二维码体验立即体验哦";
  if($_POST['userword']){
    $customArr=array(
        array(
        'x' => 30, 
        'y' => 500, 
        'w' => 480, 
        'fontsize' => 22, 
        'lineheightRate' =>1.6,
        'color' => '#000000',
        'stretch' => false, 
        'font' => './grafika/fonts/chengjishi.ttf',
        'text' => $_POST['userword']
          ), 
      );
  }else{
    //日历
    $customArr=array(
    array(
      'x' => 30, 
      'y' => 450, 
      'w' => 480, 
      'fontsize' => 16, 
      'lineheightRate' =>1.6,
      'color' => '#000000',
      'stretch' => false, 
      'font' => './grafika/fonts/song.ttf',
      'text' => getLunarCalendar()
      ), 
    array(
      'x' => 30, 
      'y' => 500, 
      'w' => 480, 
      'fontsize' => 120, 
      'lineheightRate' =>1.6,
      'color' => '#000000',
      'stretch' => false, 
      'font' => './grafika/fonts/song.ttf',
      'text' => date('d')
      ), 
    array(
      'x' => 240, 
      'y' => 600, 
      'w' => 300, 
      'fontsize' => 20, 
      'lineheightRate' =>1.6,
      'color' => '#000000',
      'stretch' => false, 
      'font' => './grafika/fonts/song.ttf',
      'text' => date('Y/m')
      ), 
    array(
      'x' => 30, 
      'y' => 720, 
      'w' => 400, 
      'fontsize' => 18, 
      'lineheightRate' =>1.6,
      'color' => '#000000',
      'stretch' => false, 
      'font' => './grafika/fonts/song.ttf',
      'text' => getYiJI()
      ), 
    );
  }
  

  $dataArr['texts']=array_merge($dataArr['texts'],$customArr);
  $res=new ImageMerge($dataArr);
  // $res->saveImage('./image/dist');
  $res->showImage();
  // echo getYiJI();




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
  if (!preg_match('/<div class=\"one-cita\">([\s\S]*?)<\/div>/i',$result,$match)) {
    $error='正则匹配失败,请重试';
    // print_r($result);
    die($error);
  }
  curl_close($ch);
  //超过40个汉字就重新换
  if(mb_strlen($match[1])>80){
    $match[1]=getWords();
  }
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
  if (!preg_match('/<div class=\"one-imagen\">([\s\S]*?)<img src=\"([\s\S]*?)\"/i',$result,$match)) {
    $error='正则匹配失败';
    echo $url;
    die($error);
  }
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


//公历转农历api
//http://www.wnfangsong.com/api/date/api.php?d=20170313
//{
// status: 1,
// data: {
// year: "二零一七",
// month: "二月",
// day: "十六",
// era: "丁酉",
// week: "一",
// zodiac: "鸡",
// constellation: "双鱼座",
// note: ""
// },
// log: "公历转农历完毕。"
// }
// 随机生成今日谊、忌
function getLunarCalendar(){
  $url="http://www.wnfangsong.com/api/date/api.php?d=".date('Ymd');
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_FILE, $tempImage);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
  curl_setopt($ch, CURLOPT_TIMEOUT, 120);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //不输出结果
  $result=curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close($ch);
  $dataArr=json_decode($result,true);
  $dataArr=$dataArr['data'];
  // print_r($dataArr);
  return '农历  '.$dataArr['year'].'  '.$dataArr['month'].$dataArr['day'].'  '.$dataArr['era'].'  '.$dataArr['zodiac'].'  '.'双鱼座';
}


//获取谊忌
function getYiJI(){
  $data = array(
    '交朋友',
    '表白',
    '喝酒',
    '逛街',
    '购物',
    '做作业',
    '泡吧',
    '撩妹子',
    '大醉一场',
    '旅行',
    '出门',
    '宅',
    '看电影',
    '撸妆',
    '泡妞',
    '睡男神',
    '听歌',
    '睡觉',
    '吃大餐',
    '看书',
    '胡说八道',
    '打架',
    '看小黄书',
    '牵手',
    '洗澡',
    '裸睡',
    '打扫',
    '翻旧账',
    '花钱',
    '熬夜',
    '陪对象',
    '考试',
    '少女心',
    '开脑洞',
    '早睡觉',
    '求职',
    '求子',
    '健身',
    '相信第六感',
    '吃肉',
    '项目上线',
    '表白',
    '追女神',
    '啪啪啪',
    '剁手',
    '起床',
    '休假',
    '学习',
    '关爱单身狗',
    '二人世界',

    '少女心',
    '开脑洞',
    '早睡觉',
    '求职',
    '求子',
    '健身',
    '相信第六感',
    '吃肉',
    '项目上线',
    '表白',
    '追女神',
    '啪啪啪',
    '剁手',
    '起床',
    '休假',
    '学习',
    '关爱单身狗',
    '二人世界',

   );
  $day=date('d');
  $hour=date('G');
  $minute=intval(date('i')); //有前导0
  $weekday=date("w");
  $str="宜：".$data[$day].'、';
  array_splice($data,$day,1);
  $str.=$data[$hour].'、';
  array_splice($data,$hour,1);
  $str.=$data[$weekday].'、';
  array_splice($data,$weekday,1);
  $str.=$data[$minute]."\n\n";
  array_splice($data,$minute,1);

  $str.="忌：".$data[$day].'、';
  array_splice($data,$day,1);
  $str.=$data[$hour].'、';
  array_splice($data,$hour,1);
  $str.=$data[$minute];
  array_splice($data,$minute,1);

  // print_r($data);
  return $str;
}