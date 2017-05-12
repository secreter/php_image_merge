<?php 
require_once './grafika/src/autoloader.php';
$path="D:\study\pythonitem\qrcodetest\\";
use Grafika\Grafika;
define(ROOT,dirname(__FILE__));
class ArtQrcode{
  private $bgImagePath;    //背景图文件地址
  private $offsetX;        //二维码在x轴偏移量
  private $offsetY;        //二维码在y轴偏移量
  private $w;              //二维码在宽度,默认正方形
  private $origin;         //二维码定位时的参照点
  private $content;        //二维码内容
  private $cropImagePath;  //裁剪出来的图片位置
  private $qrImagePath;    //生成的二维码位置
  private $artImagePath;   //生成的art二维码位置
  private $imageRootDir=ROOT.'/image/dist/';   //存放图片的文件夹根目录
  private $errors=array(
      '二维码生成出错！'
    );

  function __construct($bgImagePath,$content,$offsetX,$offsetY,$w=200){
    $time=time();
    $this->bgImagePath=$bgImagePath;
    $this->content=$content;
    $this->w=$w;
    $this->origin=$this->getOrigin($offsetX,$offsetY);
    $this->offsetX=$offsetX;
    $this->offsetY=$offsetY;
    $this->cropImagePath=$this->imageRootDir.'crop/'.$time.'.jpg';
    $this->qrImagePath=$this->imageRootDir.'qr/'.$time.'.png';
    $this->artImagePath=$this->imageRootDir.'art/art_'.$time.'.png';
  }
  /**
   * 根据x，y的正负号来确定坐标系原点位置，正为左上，负为右下
   * @param  [type] $item [description]
   * @return [type]       [description]
   */
  private function getOrigin($x,$y)
  {
    $origin='';
    if ($x>=0 && $y>=0) {
      $origin='top-left';
    }elseif ($x>=0 && $y<=0) {
      $origin='bottom-left';
    }elseif ($x<=0 && $y<=0) {
      $origin='bottom-right';
    }else{
      $origin='top-right';
    }
    echo $origin;
    return $origin;
  }
  /**
   * 将背景图中指定位置指定大小的图片抠出来
   * @return [type] [description]
   */
  function crop(){
    $editor = Grafika::createEditor();
    $editor->open( $image, $this->bgImagePath );
    $editor->crop( $image, $this->w, $this->w, $this->origin,$this->offsetX,$this->offsetY);
    $editor->save( $image, $this->cropImagePath );
  }
  /**
   * 对于剪切的图片进行二维码生成
   * @return [type] [description]
   */
  function generateQrcode(){
    $output_name=time().'.png';
    echo "myqr $this->content -n $output_name -d $output_dir -p $this->cropImagePath ";
    //执行py脚本生成二维码
    // exec("python D:\study\pythonitem\qrcode\myqr.py $this->content -n $output_name -d $output_dir  -c -con 1 -bri 1",$array,$ret);
    exec("pyqart -v 5 -y -r 3 -p 1 $this->content $this->cropImagePath -o $this->qrImagePath",$array,$ret);
    if ($ret!=0) {
      die($this->errors[0]);
    }
    print_r($array);
    echo("ret is $ret");
  }

  /**
   * 对于生成的二维码进行放大
   * @return [type] [description]
   */
  function resizeQrcode(){
    echo "python D:\study\pythonitem\qrcodetest\insert.py $this->qrImagePath $this->qrImagePath";
    //执行py脚本生成二维码
    // exec("python D:\study\pythonitem\qrcode\myqr.py $this->content -n $output_name -d $output_dir  -c -con 1 -bri 1",$array,$ret);
    exec("python D:\study\pythonitem\qrcodetest\insert.py $this->qrImagePath $this->qrImagePath",$array,$ret);
    if ($ret!=0) {
      die($this->errors[0]);
    }
    print_r($array);
    echo("ret is $ret");
  }
  /**
   * 删除二维码的白边
   * @return [type] [description]
   */
  function cutMargin(){
    //执行py脚本
    exec("python D:\study\pythonitem\qrcodetest\getMarginPos.py $this->qrImagePath",$array,$ret);
    if ($ret!=0) {
      die($this->errors[0]);
    }
    $margin=$array[1]+4;
    print_r($array);
    $editor = Grafika::createEditor();
    // //全部縮放到500px
    // $editor->open($image , $this->qrImagePath); // 打开yanying.jpg并且存放到$image

    // $editor->resizeExactWidth($image , $width);
    // $editor->save($image , $this->qrImagePath);

    $editor->open( $image, $this->qrImagePath );
    $width=$image->getWidth();
    $editor->crop( $image, $width-$margin*2, $width-$margin*2, $this->origin,$margin,$margin);
    $editor->save( $image, $this->qrImagePath );
  }

  function merge(){
    $editor = Grafika::createEditor();

    $editor->open($image1 , $this->qrImagePath); // 打开yanying.jpg并且存放到$image1
    $editor->resizeExactWidth($image1 , $this->w);
    $editor->save($image1 , $this->qrImagePath);

    $editor->open($image1 , $this->bgImagePath);
    $editor->open($image2 , $this->qrImagePath);
    $editor->blend ( $image1, $image2 , 'normal', 0.9, $this->origin,$this->offsetX,$this->offsetY);
    $editor->save($image1,$this->artImagePath);
  }


  /**
   * 析构函数，处理后事，把生成的不要的中间图片全删除了
   * [__destruct description]
   */
  function __destruct() {
    // 删除唱功返回true，错误返回false
    // if (!unlink($this->cropImagePath)){
    // echo ("Error deleting $this->cropImagePath;");
    // }else{
    // echo ("Deleted $this->cropImagePath;");
    // }
    // if (!unlink($this->qrImagePath)){
    // echo ("Error deleting $this->qrImagePath");
    // }else{
    // echo ("Deleted $this->qrImagePath");
    // }
  }

}


// $art=new ArtQrcode('D:\study\AppServ\www\phpstudy\imgmake\image\src\7.jpg','http://inankai.cn',500,500);
$art=new ArtQrcode('D:\study\AppServ\www\phpstudy\imgmake\image\src\7.jpg','http://inankai.cn',-300,100,117);
$art->crop();
$art->generateQrcode();
$art->resizeQrcode();
$art->merge();
// exec("python D:\study\pythonitem\qrcodetest\index.py",$array,$ret);
// exec("myqr $cropImagePath",$array,$ret);
// print_r($array);
// echo("ret is $ret");

 ?>