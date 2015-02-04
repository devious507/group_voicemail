<?php

require_once('cal.php');

$myCal=new paulCalendar();
$myCal->setBaseName('callLog.php');
$myCal->setTargetUrl('showCallLogs.php');
$myCal->setCellPadding(5);
$myCal->setCellSpacing(0);
$myCal->setBorder(1);
if(isset($_GET['year'])) {
	$myCal->setYear($_GET['year']);
}
if(isset($_GET['month'])) {
	$myCal->setMonth($_GET['month']);
}
//$myCal->dump();
$ctrl=$myCal->output();
?>
<!doctype html>
<html>
<head>
<title>Date Selector</title>
</head>
<body>
<?php echo $ctrl; ?>
<br><a href="index.php">Voicemail Monitoring</a><br>
</body>
</html>
