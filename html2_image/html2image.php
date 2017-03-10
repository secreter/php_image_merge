<?php
require_once('html2_pdf_lib/html2pdf.class.php');
if(isset($_POST['snapshot'])){
 $html = stripslashes($_POST['url']); 
 ob_start();  ?>
 
<page style="font-size: 14px">
<?php
echo $html;
?>
</page>
<?php 
$content = ob_get_clean();
ob_clean ();
//end buffering

	  try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'en');
//      $html2pdf->setModeDebug();
$html2pdf->setDefaultFont('courier');
$html2pdf->writeHTML($content);
$file = $html2pdf->Output('temp.pdf','F');
//pdf creation

//now magic starts
$im = new imagick('temp.pdf');
$im->setImageFormat( "jpg" );
$img_name = time().'.jpg';
$im->setSize(300,200);
$im->writeImage($img_name);
$im->clear();
$im->destroy(); 
//remove temp pdf
unlink('temp.pdf');

    }
	catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>

<?php
	}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>HTML To Image PHP Script | Buffer Now</title>

<link href='http://fonts.googleapis.com/css?family=Cuprum&amp;subset=latin' rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="preview_before/css/styles.css" />
</head>
<body>
<style>
textarea {
width: 544px;
height: 195px;
overflow-y:scroll;
resize:none;
}
</style>
<div id="page">
<?php if(isset($img_name)) { ?>
<img src="<? echo $img_name ?>"/>
<?php } else { ?>
<center><a style="color:#000" href="http://buffernow.com/html-to-image-php-script/" rel="dofollow">Full Article HTML To Image Simple Php Script</a></center>
<h1><a href="http://buffernow.com/html-to-image-php-script/">HTML To Image Simple Php Script<a></h1>
<ul>
<li>Edit The Html in The box And Press Convert</li>
<li>You Can use your own html But Your html
<ul> <li>Must contain Full url of css and images.</li>
<li>Should not contain any html5 tag</li>
 </ul>
</li>
</ul>

<form action="" method="POST">
<textarea type="text" name="url">
<link href="css/disco.css" type="text/css" rel="stylesheet">
<div class="container">
<div class="wrapper">
<div class="header">
<div class="dancing_img">
<img src="images/Danc_girl_img.png" />
</div>
<div class="heading_text">
<img src="images/header.jpg" />
</div> 
<div class="steffen_text">Paul Phoenix</div>
<div class="steffen_achieved">Achieved the following in the</div>
<div class="steffen_forget">Dont Forget The Disco Lyrics Quiz</div>
<div class="dancing_box">99%
<span id="dancing">- </span>
<span id="dancing_a">A</span> 
<span id="dancing_d">Magical</span>
<span id="dancing">Musician</span>
<span id="dancing_diva">is </span>
<span id="dancing">Phoenix</span>
</div>
<div class="dancing_img_man">
<img src="images/danc_man_img.png" />
</div>
</div></div>
</div>
 </textarea>
 <div style="clear:both"></div>
 <center>
<input type="submit" value="Snapshot" name="snapshot"> 
</center>
</form>
<?php } ?>
</body>
</html>