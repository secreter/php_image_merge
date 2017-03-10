<?php 
/*
txt转换为一张透明的图片，文字上下左右边距为0
 */
require_once './grafika/src/autoloader.php';
use Grafika\Grafika; // Import package
use Grafika\Color;





// $txt2img=new Txt2img("看下效果。这里说明下，如果文字为中文，需要找一个支持中文的字体。默认字体不支持中文，所以你写中文，就是都是小方框。\n看下效果。这里说明下，如果文字为中文，需要找一个支持中文的字体。默认字体不支持中文，所以你写中文，就是都是小方框。google sdsa wea sdasd google sdsa wea sdasdgoogle sdsa wea sdasd",500,16);
// $txt2img->saveImg('D:\study\AppServ\www\phpstudy\text2img\text2pic\src\Publics\uploads');


/**
* txt to img
*/
class Txt2img
{
  private $str;     //字符串
  private $strArr;  //按行分好的字符串数组
  private $wordNum;  //每行的字数，英文算0.5
  private $lineNum;  //一共有多少行
  private $lineheight;  
  private $canvasWidth; //画布宽度
  private $canvasHeight; //画布高度，根据文字计算出
  private $fontsize;
  private $color="#000000";
  private $wordWidthRate=1.3;  //文字加空隙宽度是实际px的倍率,不同字体倍率不同要测试
  private $lineheightRate=1.5;  //行高是实际px的倍率
  private $enRate=0.5;  //英文宽度字符是中文倍率
  private $newStr;     //添加了换行的string
  private $alpha=1;
  private $font='./grafika/fonts/hanyikaiti.ttf';
  function __construct($str = '没有文字···',$canvasWidth=500,$fontsize=16)
  {
    $this->str=$str;
    $this->canvasWidth=$canvasWidth;
    $this->fontsize=$fontsize;

    
  }
  /**
   * 初始化
   */
  private function init(){
    $this->getWordNum();
    $this->addBreak();
    $this->getImgHeight();
  }
  /**
   * 获取每行能容纳的中文字数，英文字符看做0.5个
   * @return [int]               [description]
   */
  private function getWordNum(){
    //汉字加上间距测量显示差不多有1.2倍fontsize,左右padding10
    $this->wordNum=floor(($this->canvasWidth-20) / ($this->wordWidthRate*$this->fontsize));
    return $this->wordNum;
  }
  private function getLineNum(){
    // echo $this->newStr;
    // echo $this->wordNum;
      $times=preg_match_all("/\\n/is",$this->newStr,$matchArr);
      
      if($times!=false){
        $this->lineNum=$times;
      }else{
        die('正则匹配失败');
      }
      return $this->lineNum;
  }
  function getImgHeight(){
    $this->lineheight=$this->lineheightRate*$this->fontsize;
    $this->canvasHeight=$this->getLineNum()*$this->lineheight;
    return $this->canvasHeight;
  }

  // 添加换行
  private function addBreak()
  {
    $strArr = array();
    //传进来的str里可能也包含换行
    $strTempArr=explode("\n", trim($this->str));
    foreach ($strTempArr as $strTemp) {
      $len=strlen($strTemp);
      $num=0;
      $index=0;
      while($index < $len){
        // 中文字符
        if(ord($strTemp[$index]) > 128){
          $num += 1;
          $index+=3;
        }
        else{
          $num += $this->enRate;
          $index+=1;
        }
        if ($num >= $this->wordNum ) {
          $strArr[] = substr($strTemp, 0, $index)."\n";  
          $strTemp = substr($strTemp, $index);
          $len -= $index;
          $num=0;
          $index=0;
        }
        
      }
      //用单引号就匹配不到，就是一个坑！！！单引号貌似就不把他当换行了
      $strArr[] = $strTemp."\n";
      
    }
    $this->strArr[]=implode($strArr);
    $this->newStr=implode($this->strArr);
    return  $this->newStr;
  }

  /**
   * 返回image对象，可直接使用
   * @return [image] [description]
   */
  public function getImg()
  {
    $this->init();
    $editor = Grafika::createEditor();
    //创建空白画布
    $image = Grafika::createBlankImage($this->canvasWidth,$this->canvasHeight);
    $transparent=new Color("#ffffff");
    $transparent->setAlpha(0);      //填充透明颜色
    $editor->fill ( $image, $transparent, 0, 0 );
    $textColor=new Color($this->color);
    $textColor->setAlpha($this->alpha);
    $editor->text($image ,$this->newStr,$this->fontsize,10,0,$textColor,$this->font,0);
    // 如果直接输出了，透明度还是无效
    // $editor->open( $bg_img, 'image/src/3.jpg'); // open bg image
    // $editor->blend ( $bg_img, $image , 'normal', 0.9, 'top-left',100,100);
    // $editor->save($bg_img,'temp.png');
    return $image;
  }
  /**
   * 输出图片到浏览器
   * @return [type] [description]
   */
  public function showImg()
  {
    $this->init();
    $image=$this->getImg();
    header('Content-type: image/png'); // Tell the browser we're sending a png image
    $image->blob('PNG'); 
  }

  //保存图片，如果直接输出了，透明度还是无效
  /**
   * 保存图片并返回路径
   * @param  [type] $path [description]
   * @return [type]       [description]
   */
  public function saveImg($path)
  {
    $this->init();
    $image=$this->getImg();
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
  public function setTextAlpha($alpha)
  {
    $this->alpha=$alpha;
  }
  public function setLineheightRate($rate){
    $this->lineheightRate=$rate;
  }
  public function setEnrate($rate)
  {
    $this->enRate=$rate;
  }
  public function setWordWidthRate($rate)
  {
    $this->wordWidthRate=$rate;
  }
  public function setColor($color)
  {
    $this->color=$color;
  }
  public function setFont($path)
  {
    $this->font=$path;
  }
  public function setFontsize($fontsize)
  {
    $this->fontsize=$fontsize;
  }
}




 ?>