<?php 
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
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:'.Rand_IP(), 'CLIENT-IP:'.Rand_IP()));
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

function getImg($url){
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
  curl_setopt($ch, CURLOPT_TIMEOUT, 120);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //不输出结果
  $result=curl_exec($ch);
  $info = curl_getinfo($ch);
  curl_close($ch);
  return $result;
}


//随机IP
function Rand_IP(){

    $ip2id= round(rand(600000, 2550000) / 10000); //第一种方法，直接生成
    $ip3id= round(rand(600000, 2550000) / 10000);
    $ip4id= round(rand(600000, 2550000) / 10000);
    //下面是第二种方法，在以下数据中随机抽取
    $arr_1 = array("218","218","66","66","218","218","60","60","202","204","66","66","66","59","61","60","222","221","66","59","60","60","66","218","218","62","63","64","66","66","122","211");
    $randarr= mt_rand(0,count($arr_1)-1);
    $ip1id = $arr_1[$randarr];
    return $ip1id.".".$ip2id.".".$ip3id.".".$ip4id;
}
print(getImg(getSealUrl()));

 ?>