<?php

require_once("db.php");
phpAuth();

if(!isset($_GET['message_id'])) {
	header("Location: index.php");
} else {
	$message_id=$_GET['message_id'];
}

$db=connect();
$sql="SELECT * FROM messages WHERE message_id='{$message_id}'";
$res=$db->query($sql);
$td='<form method="post" action="updateRecord.php">';
$td2='';
$td3='';
while(($row=$res->fetchRow(MDB2_FETCHMODE_ASSOC))==true) {
	foreach($row as $k=>$v) {
		switch($k) {
		case "message_id":
			$td.=getTxtRow($k,$v,'hidden');
			break;
		case "message_create":
		case "filename":
			break;
		case "status_id":
			$td.="<tr><td>{$k}</td><td>".getStatusID($v)."</td></tr>";
			break;
		case "call_type_id":
			$td.="<tr><td>{$k}</td><td>".getCallType($v)."</td></tr>";
			break;
		case "current_owner":
			$td.="<tr><td>{$k}</td><td>".getCurrentOwner($v)."</td></tr>";
			break;
		default:
			$td.=getTxtRow($k,$v);
			break;
		}
	}
}
$td.="<tr><td colspan=\"2\"><input type=\"submit\"></td></tr></form>";

$sql="select entry_time,note from messages_notes WHERE message_id={$message_id} ORDER BY entry_time";
$res=$db->query($sql);
while(($row=$res->fetchRow())==true) {
	$td2.="<tr><td colspan=\"2\"><b>{$row[0]}</b><hr>{$row[1]}<hr></td></tr>\n";
}

$sql="select action_timestamp,action_description from messages_logfile WHERE message_id={$message_id} ORDER BY action_timestamp";
$res=$db->query($sql);
while(($row=$res->fetchRow())==true) {
	$td3.="<tr><td>{$row[0]}</td><td>{$row[1]}</td></tr>\n";
}

?>
<html>
<head>
<title>Working Ticket</title>
</head>
<body>
<a href="index.php">Home</a> | <a target="_float" href="audioPlayer.php?message_id=<?php echo $message_id;?>">Play Audio</a>
<table border="0" cellpadding="10" cellspacing="0"><tr><td valign="top">
<table cellpadding="5" cellspacing="0" border="1">
<?php echo $td; ?>
</table></td>

<td valign="top"><table cellpadding="5" cellspacing="0" border="1"> <?php echo $td3; ?> </table></td></tr>

<tr><td colspan="2">
<table width="100%" cellpadding="5" cellspacing="0" border="1">
<tr><td>Notes:</td></tr>
<tr><td><form method="post" action="addnote.php"><input type="hidden" name="message_id" value="<?php echo $message_id; ?>"><textarea name="mynote" rows="5" cols="80"></textarea><br><input type="submit"></form></td></tr>
<?php echo $td2; ?>
</table>
</td></tr></table>


</body>
</html>
