<?php

require_once("db.php");
$refresh_interval=20;

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
	$url="<a href=\"classifyAudio.php?message_id={$row[0]}\">{$row[0]}</a>";
	$td.="<tr><td>{$url}</td><td>{$row[1]}</td></tr>\n";
}

$sql="SELECT a.message_id,a.message_create,a.status,c.call_type FROM (SELECT m.call_type_id,m.message_id,m.message_create,s.status FROM messages AS m LEFT OUTER JOIN status AS s ON m.status_id=s.status_id WHERE m.status_id>0 AND m.status_id < 4 ORDER BY m.status_id ASC,m.message_create ASC) as a LEFT OUTER JOIN call_types AS c ON a.call_type_id=c.call_type_id";
$sql="SELECT b.message_id,b.message_create,b.status,b.call_type,u.username FROM (SELECT a.message_id,a.message_create,a.status,c.call_type,a.current_owner FROM (SELECT m.call_type_id,m.message_id,m.message_create,s.status,m.current_owner FROM messages AS m LEFT OUTER JOIN status AS s ON m.status_id=s.status_id WHERE m.status_id>0 AND m.status_id < 4 ORDER BY m.status_id ASC,m.message_create ASC) as a LEFT OUTER JOIN call_types AS c ON a.call_type_id=c.call_type_id) as b LEFT OUTER JOIN users AS u ON b.current_owner=u.user_id";
$res=$db->query($sql);
while(($row=$res->fetchRow())==true) {
	$url="<a href=\"workTicket.php?message_id={$row[0]}\">{$row[0]}</a>";
	if($row[2] == "CLOSED") {
		$td2.="<tr><td><strike>{$url}</strike></td><td><strike>{$row[1]}</strike></td><td><strike>{$row[2]}</strike></td><td><strike>{$row[3]}</strike></td><td><strike>{$row[4]}</td></tr>\n";
	} else {
		$td2.="<tr><td>{$url}</td><td>{$row[1]}</td><td>{$row[2]}</td><td>{$row[3]}</td><td>{$row[4]}</td></tr>\n";
	}
}

$db->disconnect();

$refresh_time=date("m/d/Y H:i:s");
if(isset($_GET['lastmess']) && ($_GET['lastmess'] < $max)) {
	$alarm='<embed src="alert.wav" autostart="true" hidden="true" loop="false">';
	$alarm.='<bgsound src="alert.wav" LOOP="1">'."\n";
} else {
	$alarm='';
}

?>
<html>
<head>
<title>Messsages</title>
<meta http-equiv="Refresh" content="<?php echo $refresh_interval;?>;URL=index.php?lastmess=<?php echo $max;?>">
</head>
<body>
Refresh Time: <?php echo $refresh_time; ?>
<table cellpadding="5" cellspacing="0" border="1">
<tr><td colspan="2" align="center">Newly Arrived</td></tr>
<tr><td>Msg #</td><td>Arrived</td></tr>
<?php echo $td; ?>
</table>
<hr>
<table cellpadding="5" cellspacing="0" border="1">
<tr><td colspan="5" align="center">Initial Processing Done</td></tr>
<tr><td>Msg #</td><td>Arrived</td><td>Status</td><td>Call Type</td><td>Owner</td></tr>
<?php echo $td2; ?>
</table>
<?php echo $alarm; ?>
</body>
</html>
