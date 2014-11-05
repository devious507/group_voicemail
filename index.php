<?php

require_once("db.php");
phpAuth();

$td='';
$td2='';
$db=connect();
$sql="SELECT message_id,message_create from messages WHERE status_id=0 ORDER BY status_id ASC,message_create ASC";
$res=$db->query($sql);
while(($row=$res->fetchRow())==true) {
	$url="<a href=\"classifyAudio.php?message_id={$row[0]}\">{$row[0]}</a>";
	$td.="<tr><td>{$url}</td><td>{$row[1]}</td></tr>\n";
}

$sql="SELECT a.message_id,a.message_create,a.status,c.call_type FROM (SELECT m.call_type_id,m.message_id,m.message_create,s.status FROM messages AS m LEFT OUTER JOIN status AS s ON m.status_id=s.status_id WHERE m.status_id>0 AND m.status_id < 4 ORDER BY m.status_id ASC,m.message_create ASC) as a LEFT OUTER JOIN call_types AS c ON a.call_type_id=c.call_type_id";
$res=$db->query($sql);
while(($row=$res->fetchRow())==true) {
	$url="<a href=\"workTicket.php?message_id={$row[0]}\">{$row[0]}</a>";
	if($row[2] == "CLOSED") {
		$td2.="<tr><td><strike>{$url}</strike></td><td><strike>{$row[1]}</strike></td><td><strike>{$row[2]}</strike></td><td><strike>{$row[3]}</strike></td></tr>\n";
	} else {
		$td2.="<tr><td>{$url}</td><td>{$row[1]}</td><td>{$row[2]}</td><td>{$row[3]}</td></tr>\n";
	}
}

$db->disconnect();
?>
<html>
<head><title>Messsages</title></head>
<body>
<table cellpadding="5" cellspacing="0" border="1">
<tr><td colspan="2" align="center">Newly Arrived</td></tr>
<tr><td>Msg #</td><td>Arrived</td></tr>
<?php echo $td; ?>
</table>
<hr>
<table cellpadding="5" cellspacing="0" border="1">
<tr><td colspan="4" align="center">VM Listened To</td></tr>
<tr><td>Msg #</td><td>Arrived</td><td>Status</td><td>Call Type</td></tr>
<?php echo $td2; ?>
</table>
</body>
</html>
