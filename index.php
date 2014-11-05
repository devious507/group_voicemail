<?php

require_once("db.php");
$refresh_interval=120;

phpAuth();

$td='';
$td2='';
$db=connect();
$sql="SELECT max(message_id) FROM messages";
$res=$db->query($sql);
$row=$res->fetchRow();
$max=$row[0];
if(!isset($_GET['lastmess'])) {
	header("Location: index.php?lastmess={$max}");
	exit();
}
$sql="SELECT message_id,message_create from messages WHERE status_id=0 ORDER BY status_id ASC,message_create ASC";
$res=$db->query($sql);
while(($row=$res->fetchRow())==true) {
	$url="<a href=\"workTicket.php?message_id={$row[0]}\">{$row[0]}</a>";
	$td.="<tr><td>{$url}</td><td>{$row[1]}</td></tr>\n";
}

$sql="SELECT a.message_id,a.message_create,a.status,c.call_type FROM (SELECT m.call_type_id,m.message_id,m.message_create,s.status FROM messages AS m LEFT OUTER JOIN status AS s ON m.status_id=s.status_id WHERE m.status_id>0 AND m.status_id < 4 ORDER BY m.status_id ASC,m.message_create ASC) as a LEFT OUTER JOIN call_types AS c ON a.call_type_id=c.call_type_id";
$sql="SELECT b.message_id,b.message_create,b.status,b.call_type,u.username FROM (SELECT a.message_id,a.message_create,a.status,c.call_type,a.current_owner FROM (SELECT m.call_type_id,m.message_id,m.message_create,s.status,m.current_owner FROM messages AS m LEFT OUTER JOIN status AS s ON m.status_id=s.status_id WHERE m.status_id>0 AND m.status_id < 4 ORDER BY m.status_id ASC,m.message_create ASC) as a LEFT OUTER JOIN call_types AS c ON a.call_type_id=c.call_type_id) as b LEFT OUTER JOIN users AS u ON b.current_owner=u.user_id";
$res=$db->query($sql);
while(($row=$res->fetchRow())==true) {
	$url="<a href=\"workTicket.php?message_id={$row[0]}\">{$row[0]}</a>";
	if($row[2] == "CLOSED") {
		$myClass="strike";
	} else {
		$myClass="plain";
	}
	$td2.="<tr><td class=\"{$myClass}\">{$url}</td><td class=\"{$myClass}\">{$row[1]}</td><td class=\"{$myClass}\">{$row[2]}</td><td class=\"{$myClass}\">{$row[3]}</td><td class=\"{$myClass}\">{$row[4]}</td></tr>\n";
}

$db->disconnect();

$refresh_time=date("m/d/Y H:i:s");
if(isset($_GET['lastmess']) && ($_GET['lastmess'] < $max)) {
	$alarm='<div class="alarm"><embed src="alert.wav" autostart="true" hidden="true" loop="false">';
	$alarm.='<bgsound src="alert.wav" LOOP="1">'."</div>\n";
} else {
	$alarm='';
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Messsages</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Refresh" content="<?php echo $refresh_interval;?>;URL=index.php?lastmess=<?php echo $max;?>">
<link href="css/index.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
	var startTime;
	var r = <?php echo $refresh_interval;?>-2;
	var result = r;
setInterval(function () {
	var d = parseInt(new Date().getTime()/1000);
	if(typeof startTime === 'undefined') {
		startTime = d;
	}
	document.getElementById('refreshCountdown').innerHTML = 'Auto Refresh in: '+result+' seconds.'; 
	result = r + startTime - d;
} , 1000) //calling it every 1 second to do a count down
</script>
</head>
<body>
<div id="refreshCountdown" class="refreshCountdown">
</div>

<div class="unlistened">
<table cellpadding="5" cellspacing="0" border="1">
<tr><td colspan="2" align="center">Newly Arrived</td></tr>
<tr><td>Msg #</td><td>Arrived</td></tr>
<?php echo $td; ?>
</table>
</div>

<div class="listened">
<table cellpadding="5" cellspacing="0" border="1">
<tr><td colspan="5" align="center">Initial Processing Done</td></tr>
<tr><td>Msg #</td><td>Arrived</td><td>Status</td><td>Call Type</td><td>Owner</td></tr>
<?php echo $td2; ?>
</table>
</div>
<?php echo $alarm; ?>
</body>
</html>
