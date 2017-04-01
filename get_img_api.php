<?php 
require_once './imageMerge.php';
$backData=array(
  'path'=>'',
  'error'=>0
  );
if (!isset($_GET['imgData'])) {
  $backData['error']='imgData is necessary!';
  echo json_encode($backData);
  return;
}
$dataArr=json_decode($_GET['imgData'],true);
// print_r($_GET['imgData']);
// print_r($dataArr);
$res=new ImageMerge($dataArr);
// $res->saveImage('./image/dist');
$res->showImage();






 ?>