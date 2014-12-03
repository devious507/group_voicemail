<?php

if(!isset($_GET['year']))
	header("Location: callLog.php");

if(!isset($_GET['month']))
	header("Location: callLog.php");

if(!isset($_GET['day']))
	header("Location: callLog.php");

$m=$_GET['month'];
$d=$_GET['day'];
$y=$_GET['year'];


$sql=sprintf("select calldate,src,dst,duration,channel,dstchannel,lastapp,lastdata FROM cdr WHERE date(calldate)='%02d-%02d-%d' AND dst!='s' ORDER BY calldate ASC",$m,$d,$y);

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
$tbl='';
$color="dedede";
while(($row=$res->fetchRow()) == true) {
	$tbl.="<tr>";
	foreach($row as $val) {
		$tbl.="<td bgcolor=\"{$color}\">{$val}</td>";
	}
	$tbl.="</tr>\n";
	if($color == 'ffffff') {
		$color='dedede';
	} else {
		$color='ffffff';
	}
}
$db->disconnect();
?>
<!doctype html>
<html>
<head>
<title>Call Logs</title>
</head>
<body>
<table cellpadding="5" cellspacing="0" border="1">
<tr><td>CallDate</td><td>Src</td><td>Dst</td><td>Duration</td><td>Channel</td><td>DstChannel</td><td>LastApp</td><td>LastData</td></tr>
<?php echo $tbl; ?>
</table>
<a href="callLog.php">Back</a><br>
</body>
</html>
