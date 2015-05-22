<?php

if(!isset($_GET['year']))
	header("Location: callGraph.php");

if(!isset($_GET['month']))
	header("Location: callGraph.php");

if(!isset($_GET['day']))
	header("Location: callGraph.php");

$m=$_GET['month'];
$d=$_GET['day'];
$y=$_GET['year'];


$sql=sprintf("select date_trunc('minute',calldate) AS start,date_trunc('minute',calldate+interval '1 minute'+(duration * interval '1 second')) AS stop,dst,channel,dstchannel,lastapp,lastdata FROM cdr WHERE date(calldate)='%s-%s-%s' AND dst!='s' ORDER BY calldate ASC",$m,$d,$y);
$sql=sprintf("select date_trunc('minute',calldate) AS start,calldate+interval '1 minute'+duration * interval '1 second' AS stop,dst,channel,dstchannel,lastapp,lastdata FROM cdr WHERE date(calldate)='%s-%s-%s' AND dst!='s' ORDER BY calldate ASC",$m,$d,$y);

require_once('MDB2.php');

$db=MDB2::singleton("pgsql://asterisk@asterisk.visionsystems.tv/asterisk");

if(PEAR::isError($db)) {
	print $db->getMessage();
	exit();
}

$res=$db->query($sql);
if(PEAR::isError($res)) {
	print $res->getMessage();
	exit();
}
while(($row=$res->fetchRow())==true) {
	// Convert Start and End times to minutes since midnight, will be important for th graph later
	$tmp=preg_split("/ /",$row[0]);
	$ttmp=preg_split("/:/",$tmp[1]);
	$row[0]=$ttmp[0]*60+$ttmp[1];
	$tmp=preg_split("/ /",$row[1]);
	$ttmp=preg_split("/:/",$tmp[1]);
	$row[1]=$ttmp[0]*60+$ttmp[1];
	$data_set[]=$row;
}
//print "<pre>"; var_dump($data_set); print "</pre>"; exit();


define("CHART_WIDTH","3000");
define("CHART_ELEMENT_HEIGHT","20");

$im = imagecreatetruecolor(CHART_WIDTH,(count($data_set)+1)*CHART_ELEMENT_HEIGHT);

$white=imagecolorallocate($im,255,255,255);
$black=imagecolorallocate($im,0,0,0);
$red=imagecolorallocate($im,255,0,0);
$green=imagecolorallocate($im,0,255,0);
$blue=imagecolorallocate($im,0,0,255);
imagefill($im,0,0,$white);
imagestring($im,1,5,5, "test string", $black);
$count=0;
foreach($data_set AS $row) {
	$count++;
	$start_x=$row[0];
	$end_x  =$row[1];
	$start_y=$count*CHART_ELEMENT_HEIGHT+2;
	$end_y  =$start_y+CHART_ELEMENT_HEIGHT-4;
	imagefilledrectangle($im,$start_x,$start_y,$end_x,$end_y,$black);
}
for($i=1; $i<24; $i++) {
	$x=$i*60;
	$y=CHART_ELEMENT_HEIGHT;
	$y2=(count($data_set)+1)*CHART_ELEMENT_HEIGHT;
	if($x == 360) {
		$color=$red;
	} elseif($x == 720) {
		$color=$red;
	} elseif($x == 1080) {
		$color=$red;
	} elseif($x == 480) {
		$color=$green;
	} elseif($x == 1020) {
		$color=$green;
	} else {
		$color=$blue;
	}
	imageline($im,$x,$y,$x,$y2,$color);
}



header("Content-type: image/png");
imagepng($im);
imagedestroy($im);
