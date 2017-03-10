<?php 
require_once './grafika/src/autoloader.php';
require_once './txt2img.php';
use Grafika\Grafika; // Import package
use Grafika\Color;

// $txt2img=new Txt2img("由于使用的vps是centos的，一直没注意这个问题，今天在自己机子上的windows调试php，发现取绝对路径怎么弄都出错，原来是windows和linux的”“和”/“的问题。查了半天发现可以用这个常量来替代绝对路径中的分隔符，这样无论服务器是windows还是linux就都适用了。",500,16);
// $txt2img->saveImg('./image/dist');
// $json={

// }
// $dataArr=json_decode($json,true);
// $dataArr=array(
//   'images' =>   array(
    // array(
    //   'x' => 0, 
    //   'y' => 0, 
    //   'w' => 600, 
    //   'h' => 300, 
    //   'stretch' => true, 
    //   'path' => './image/src/4.jpg', 
    //   ),
    // array(
    //   'x' => -0.001, 
    //   'y' => -0.001, 
    //   'w' => 600, 
    //   // 'h' => 300, 
    //   'stretch' => true, 
    //   'path' => './image/src/5.jpg',  
    //   ),
    
//     array(
//       'x' => 0, 
//       'y' => 0, 
//       'w' => 500, 
//       'stretch' => false, 
//       // 'path' => './image/src/10.jpg',  
//       'path' => 'https://unsplash.it/500/1000/?random',  
//       ),
//     array(
//       'x' => -50, 
//       'y' => -50, 
//       'w' => 100, 
//       'stretch' => false, 
//       'path' => './image/src/9.jpg',  
//       ),
//     ), 
//   'texts' =>   array(
//     array(
//       'x' => 50, 
//       'y' => 50, 
//       'w' => 400, 
//       'color' => '#123096', 
//       'stretch' => flase, 
//       'text' => '永远感激 你狂奔过操场 来到我眼前
// 阳光灿烂 烫红了你双颊 温暖你笑靥
// 那时节 黄澄澄的落叶 铺满整条街
// 下课钟声 荡过悠悠岁月
// 长大后 世界像一张网 网住我们的翅膀
// 回忆 沉甸甸在心上
// 偶尔 轻声独唱
// 是否能找回消失的力量
// 想起了初爱 想起最初的梦已不在
// 想起青春 曾无畏无惧 无所谓失败
// 当时看见彩虹就笑开 一无窒碍在胸怀
// 带你抛下课堂 翻过围墙 只为了往一片大海
// 告别了初爱 告别了制服上的名牌
// 告别天真 学着去拨开 雨天的阴霾
// 沮丧失落反复地重来 不能放弃勇敢去爱
// 是 你让我 还相信未来'
//       ),
//     array(
//       'x' => -50, 
//       'y' => -0.1, 
//       'w' => 200, 
//       'fontsize' => 14, 
//       'font' => './grafika/fonts/chengjishi.ttf', 
//       'color' => '#ffffff',
//       'stretch' => false, 
//       'text' => 'by 南开科技'
//       ), 
//     ),
//   'canvas' =>   array(
//     'w' => 500, 
//     'h' => 1000, 
//     'stretch' => false, 
//     ),
//   );
//   $res=new ImageMerge($dataArr);
//   // $res->saveImage('./image/dist');
//   $res->showImage();
/**
* 
*/
class ImageMerge
{
  private $canvasHeight;
  private $canvasWidth=800;
  private $dataArr;
  private $bgColor='#ffffff';
  private $canvas;
  function __construct($dataArr)
  {
    $this->dataArr=$dataArr;
    $this->canvasWidth=$dataArr['canvas']['w'];
    $this->init();
  }
  private function init()
  {
    $this->allTxt2img();    //文本转为透明图片，并追加结果到dataArr
    $this->imageResize();   //传入的图片全部调整大小，并追加结果到dataArr
    $this->getCanvasHeight(); //获取最终的canvas高度
    $this->mergeImage();    //所有图层合并
  }
  private function allTxt2img()
  {
    foreach ($this->dataArr['texts'] as $index => $textItem) {
      $tx2img=new Txt2img($textItem['text'],$textItem['w']);
      if (isset($textItem['color'])) {
        $tx2img->setColor($textItem['color']);
      }
      if (isset($textItem['alpha'])) {
        $tx2img->setAlpha($textItem['alpha']);
      }
      if (isset($textItem['font'])) {
        $tx2img->setFont($textItem['font']);
      }
      if (isset($textItem['fontsize'])) {
        $tx2img->setFontsize($textItem['fontsize']);
      }
      if (isset($textItem['enRate'])) {
        $tx2img->setEnRate($textItem['enRate']);
      }
      if (isset($textItem['lineheightRate'])) {
        $tx2img->setLineheightRate($textItem['lineheightRate']);
      }
      if (isset($textItem['wordWidthRate'])) {
        $tx2img->setWordWidthRate($textItem['wordWidthRate']);
      }
      $image=$tx2img->getImg();
      //把生成的image保存在对应数组里
      $this->dataArr['texts'][$index]['image']=$image;
      $this->dataArr['texts'][$index]['h']=$image->getHeight();
    }
    // print_r($this->dataArr);
  }
  public function getCanvasHeight()
  {
    $canvas=$this->dataArr['canvas'];
    // print_r($canvas);
    //如果指定不可延伸并有高度就用指定的高度
    if ($canvas['stretch']==false && isset($canvas['h'])) {
      $this->canvasHeight=$canvas['h'];
      return $this->canvasHeight;
    }
    $h=0;
    foreach ($this->dataArr['texts'] as $textItem) {
      //可延伸的文字图片才计算高度，层叠上去的不计算
      if ($textItem['stretch']==true) {
        $h+=$textItem['h'];
      }
    }
    foreach ($this->dataArr['images'] as $imageItem) {
      //可延伸的图片才计算高度，层叠上去的不计算
      if ($imageItem['stretch']==true) {
        $h+=$imageItem['h'];
      }
    }
    $this->canvasHeight=$h;
    return $this->canvasHeight;
  }
  /**
   * 根据获取的宽高先创建画布
   * @return [type] [description]
   */
  public function createCanvas()
  {
    $editor = Grafika::createEditor();
    //创建空白画布
    $image = Grafika::createBlankImage($this->canvasWidth,$this->canvasHeight);
    $transparent=new Color($this->bgColor);
    $transparent->setAlpha(1);      //填充透明颜色
    $editor->fill ( $image, $transparent, 0, 0 );
    $this->canvas=$image;
    return $this->canvas;
  }
  /**
   * 合并图层，dataArr里先出现的在底层
   * @return [type] [description]
   */
  private function mergeImage()
  {
    $editor = Grafika::createEditor();
    $canvas=$this->createCanvas();
    
    foreach ($this->dataArr['images'] as $imageItem) {
      $origin=$this->getOrigin($imageItem);
      $editor->blend ( $canvas, $imageItem['image'] , 'normal', 1, $origin,$imageItem['x'],$imageItem['y']);
    }
    //文字放在较高的图层上
    foreach ($this->dataArr['texts'] as $textItem) {
      $origin=$this->getOrigin($textItem);
      $editor->blend ( $canvas, $textItem['image'] , 'normal', 1, $origin,$textItem['x'],$textItem['y']);
    }
    $this->canvas=$canvas;
    return $this->canvas;
  }
  /**
   * 根据x，y的正负号来确定坐标系原点位置，正为左上，负为右下
   * @param  [type] $item [description]
   * @return [type]       [description]
   */
  private function getOrigin($item)
  {
    $origin='';
    if ($item['x']>=0 && $item['y']>=0) {
      $origin='top-left';
    }elseif ($item['x']>=0 && $item['y']<=0) {
      $origin='bottom-left';
    }elseif ($item['x']<=0 && $item['y']<=0) {
      $origin='bottom-right';
    }else{
      $origin='top-right';
    }
    return $origin;
  }
  private function imageResize()
  {
    $editor = Grafika::createEditor();
    foreach ($this->dataArr['images'] as $index => $item) {

      // $editor->open($image, $item['path']);
      $image=$this->openImage($editor,$item['path']);
      //w ，h都有就按给定比例缩放，会变形
      if (isset($item['w']) && isset($item['h'])) {
        $editor->resizeExact($image , $item['w'] , $item['h']);
      }elseif(isset($item['w'])){
        $editor->resizeExactWidth($image , $item['w'] );
        //补全h
        $this->dataArr['images'][$index]['h'] =$image->getHeight();
      }elseif(isset($item['h'])){
        $editor->resizeExactHeight($image , $item['h'] );
        //补全w
        $this->dataArr['images'][$index]['h'] =$image->getWidth();
      }else{
        die('缩放宽高至少有一个！');
      }
      //将打开的图片资源保存在dataArr里
      $this->dataArr['images'][$index]['image'] =$image;
    }
    // print_r($this->dataArr);
    return $this->dataArr;
  }
  private function openImage($editor,$path)
  {
    $editor = Grafika::createEditor();
    //远程文件先下载。最好直接用数据流
    if (preg_match("/^http/",$path)) {
      $url = $path;
      // $referer="http://yinzhang.388g.com/?page=18&pagesize=15";
      // $UserAgent="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36";
      //rand是防止多个人同时访问读写了一个文件，其实这里应该用流直接流入的，但是没找到有这个方法，暂时先这样。另外，后缀其实要匹配的，不一定是jpg
      //这里可以通过curl_getinfo拿到content-type，来确定图片格式，但发现这个open函数挺智能，会直接判断，不管后缀
      $imageName='./image/temp/temp_img_'.time().rand(0,20).'.jpg';
      // echo $path;
      $tempImage=fopen($imageName, 'w');
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_FILE, $tempImage);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
      curl_setopt($ch, CURLOPT_TIMEOUT, 120);
      //伪造来源referer
      curl_setopt ($ch,CURLOPT_REFERER,$referer);

      // $header[0]="Host: yinzhang.388g.com";
      // $header[]="Connection: keep-alive";
      // $header[]="Pragma: no-cache";
      // $header[]="Cache-Control: no-cache";
      // $header[]="Upgrade-Insecure-Requests: 1";
      // $header[]="User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36";
      // $header[]="Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8";
      // $header[]="Accept-Encoding: gzip, deflate, sdch";
      // $header[]="Accept-Language: zh-CN,zh;q=0.8,en;q=0.6";
      // $header[]="Cookie: bdshare_firstime=1488695809675; city=101030100; pgv_pvi=273187840; pgv_si=s4786613248; Hm_lvt_1c93b08a236fd3a29a5bc35d9eea56ca=1487413606,1488695809,1488805406; Hm_lpvt_1c93b08a236fd3a29a5bc35d9eea56ca=1488805569";
      // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      curl_setopt($ch, CURLOPT_USERAGENT, $UserAgent);
      // 为啥加上这句反倒出错了
      // curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
      $out=curl_exec($ch);
      $info = curl_getinfo($ch);
      // print_r($info);
      // print_r($out);
      
      curl_close($ch);
      fclose($tempImage);
      // exit();
      $editor->open($image , $imageName);
      // unlink($imageName);
      return $image;
    }else{
      //直接打开
      $editor->open($image , $path);
      return $image;
    }
    
  }
  public function getImage()
  {
    return $this->canvas;
  }
  public function showImage()
  {
    header('Content-type: image/png'); // Tell the browser we're sending a png image
    $this->canvas->blob('PNG'); 
  }
  public function saveImage($path)
  {
    // echo $this->canvasHeight;
    // echo $this->canvasWidth;
    $image=$this->canvas;
    $filename='pic_'.time().rand(1111,9999).'.png';
    $fullpath=$this->path($path,$filename);
    $editor = Grafika::createEditor();
    $editor->save($image,$fullpath);
    return $fullpath;
  }
  public function path($headPath, $tailPath)
  {
    $realHeadPath = realpath($headPath);
    $concatnatedPath = $realHeadPath.DIRECTORY_SEPARATOR.$tailPath;
    return $concatnatedPath;
  }
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
 ?>