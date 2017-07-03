<?php

require_once("db.php");
$refresh_interval=120;

phpAuth();
if(isset($_GET['lastmess'])) {
	$last=$_GET['lastmess'];
	if(isset($_GET['showall'])) {
		$showall="<a href=\"index.php?lastmess={$last}\">Hide Final Messages</td></tr>\n";
	} else {
		$showall="<a href=\"index.php?lastmess={$last}&showall=true\">Show All Messages</td></tr>\n";
	}
} else {
	$showall="<a href=\"index.php?showall=true\">Show All Messages</td></tr>\n";
}
$callLogs="<tr><td colspan=\"6\"><a href=\"callLog.php\">Call Log Files</a> | {$showall}</td></tr>\n";

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
$sql="SELECT message_id,message_create,length from messages WHERE status_id=0 ORDER BY status_id ASC,message_create ASC";
$res=$db->query($sql);
$tdcount=0;
while(($row=$res->fetchRow())==true) {
	$msg_id = $row[0];
	$sql_note = "SELECT note FROM messages_notes WHERE message_id={$msg_id}";
	$res_note = $db->query($sql_note);
	$row_note = $res_note->fetchRow();
	$note = $row_note[0];
	if(preg_match("/GS Support/",$note)) {
		$type="GS Support";
	} elseif(preg_match("/Retail Call/",$note)) {
		$type="Retail Call";
	} elseif(preg_match("/Q Internet/",$note)) {
		$type="Q Internet";
	} elseif(preg_match("/Q Other Issue/",$note)) {
		$type="Q Other";
	} else {
		$type="UNKNOWN";
	}
	$url="<a href=\"workTicket.php?message_id={$row[0]}\">{$row[0]}</a>";
	$date_Time=$row[1];
	$arr=preg_split("/\./",$date_Time);
	$row[1]=$arr[0];
	$td.="<tr><td>{$url}</td><td>{$row[1]}</td><td>{$row[2]}</td><td>{$type}</td></tr>\n";
	$tdcount++;
}

$sql="SELECT b.message_id,b.message_create,b.status,b.call_type,u.username FROM (SELECT a.message_id,a.message_create,a.status,c.call_type,a.current_owner FROM (SELECT m.call_type_id,m.message_id,m.message_create,s.status,m.current_owner FROM messages AS m LEFT OUTER JOIN status AS s ON m.status_id=s.status_id WHERE m.status_id>0 AND m.status_id < 4 ORDER BY m.status_id ASC,m.message_create ASC) as a LEFT OUTER JOIN call_types AS c ON a.call_type_id=c.call_type_id) as b LEFT OUTER JOIN users AS u ON b.current_owner=u.user_id";

if(!isset($_GET['showall'])) {
	$sql=" SELECT b.message_id,b.message_create,b.status,b.call_type,u.username,b.length FROM (SELECT a.message_id,a.message_create,a.status,c.call_type,a.current_owner,a.length FROM (SELECT m.call_type_id,m.message_id,m.message_create,s.status,m.current_owner,m.length FROM messages AS m LEFT OUTER JOIN status AS s ON m.status_id=s.status_id WHERE m.status_id>0 AND m.status_id < 4 ORDER BY m.status_id ASC,m.message_create ASC) as a LEFT OUTER JOIN call_types AS c ON a.call_type_id=c.call_type_id) as b LEFT OUTER JOIN users AS u ON b.current_owner=u.user_id";
} else {
	$sql=" SELECT b.message_id,b.message_create,b.status,b.call_type,u.username,b.length FROM (SELECT a.message_id,a.message_create,a.status,c.call_type,a.current_owner,a.length FROM (SELECT m.call_type_id,m.message_id,m.message_create,s.status,m.current_owner,m.length FROM messages AS m LEFT OUTER JOIN status AS s ON m.status_id=s.status_id  ORDER BY m.status_id ASC,m.message_create ASC) as a LEFT OUTER JOIN call_types AS c ON a.call_type_id=c.call_type_id) as b LEFT OUTER JOIN users AS u ON b.current_owner=u.user_id";
}


$res=$db->query($sql);
$td2count=0;
while(($row=$res->fetchRow())==true) {
	$url="<a href=\"workTicket.php?message_id={$row[0]}\">{$row[0]}</a>";
	if($row[2] == "CLOSED") {
		$myClass="strike";
	} elseif($row[2] == 'COMPLETED') {
		$myClass='completed';
	} else {
		$myClass="plain";
	}
	$td2.="<tr><td class=\"{$myClass}\">{$url}</td><td class=\"{$myClass}\">{$row[1]}</td><td class=\"{$myClass}\">{$row[2]}</td><td class=\"{$myClass}\">{$row[3]}</td><td class=\"{$myClass}\">{$row[4]}</td><td class=\"{$myClass}\">{$row[5]}</td></tr>\n";
	$td2count++;
}

$db->disconnect();

$refresh_time=date("m/d/Y H:i:s");
if(isset($_GET['lastmess']) && ($_GET['lastmess'] < $max)) {
	$alarm='<div class="alarm"><embed src="alert.wav" autostart="true" hidden="true" loop="false">';
	$alarm.='<bgsound src="alert.wav" LOOP="1">'."</div>\n";
} else {
	$alarm='';
}


if(!isset($_GET['showall'])) {
	$http_equiv="<meta http-equiv=\"Refresh\" content=\"{$refresh_interval};URL=index.php?lastmess={$max}\">";
} else {
	$http_equiv="<meta http-equiv=\"Refresh\" content=\"{$refresh_interval};URL=index.php?lastmess={$max}&showall=true\">";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Messsages</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php echo $http_equiv; ?>
<link href="css/normalize.css" rel="stylesheet" type="text/css">
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
<table cellpadding="5" cellspacing="0" border="1" width="100%">
<tr><td colspan="4" align="center">Newly Arrived (<?php print $tdcount; ?>)</td></tr>
<tr><td>Msg #</td><td>Arrived</td><td>Len</td><td>Type</td></tr>
<?php echo $td; ?>
</table>
</div>

<div class="listened">
<table cellpadding="5" cellspacing="0" border="1">
<tr><td colspan="6" align="center">Initial Processing Done (<?php print $td2count ?>)</td></tr>
<tr><td>Msg #</td><td>Arrived</td><td>Status</td><td>Call Type</td><td>Owner</td><td>Len</td></tr>
<?php echo $td2; ?>
<?php echo $callLogs; ?>
</table>
</div>
<?php echo $alarm; ?>
</body>
</html>
