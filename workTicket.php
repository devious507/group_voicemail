<?php

require_once("db.php");
phpAuth();

if(!isset($_GET['message_id'])) {
	header("Location: index.php");
} else {
	$message_id=$_GET['message_id'];
}

$db=connect();

$sql="select username FROM users WHERE user_id={$_COOKIE['voicemail-userid']}";
$res=$db->query($sql);
$row=$res->fetchRow();
$username=$row[0];

$sql="SELECT count(*) FROM messages_logfile WHERE message_id={$message_id}";
$res=$db->query($sql);
$row=$res->fetchRow();
$count=$row[0];
if($count < 2) {
	$msg="{$username} listened to the voicemail for the first time";
	$sql="INSERT INTO messages_logfile (message_id,action_description) VALUES ({$message_id},'{$msg}')";
	$db->query($sql);
}

$sql="SELECT filename FROM messages WHERE message_id='{$message_id}'";
$res=$db->query($sql);
$row=$res->fetchRow();
$tmpfile=$row[0];
$tmp=preg_split("/\//",$tmpfile);
$filename=$tmp[6]."/".$tmp[7];
unset($tmpfile);
unset($tmp);


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

$sql="select entry_time,note from messages_notes WHERE message_id={$message_id} ORDER BY entry_time DESC";
$res=$db->query($sql);
while(($row=$res->fetchRow())==true) {
	$td2.="<tr><td colspan=\"2\"><b>{$row[0]}</b><hr>{$row[1]}<hr></td></tr>\n";
}

$sql="select action_timestamp,action_description from messages_logfile WHERE message_id={$message_id} ORDER BY action_timestamp";
$res=$db->query($sql);
while(($row=$res->fetchRow())==true) {
	$td3.="<tr><td>{$row[0]}</td><td>{$row[1]}</td></tr>\n";
}

$player_html=file_get_contents("player.html");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Working Ticket</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/index.css" rel="stylesheet" type="text/css">
<link type="text/css" href="/voicemail/skins/jplayer.blue.monday.css" rel="stylesheet" />
<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/voicemail/js/jquery.jplayer.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function(){
      $("#jquery_jplayer_1").jPlayer({
        ready: function () {
          $(this).jPlayer("setMedia", {
                  title: "Voicemail # <?php echo $message_id; ?>",
                wav: "<?php echo $filename; ?>"
          });
        },
        swfPath: "/voicemail/js",
        supplied: "wav"
      });
    });
  </script>

</head>
<body>
<a href="index.php">Home</a> | <a target="_float" href="audioPlayer.php?message_id=<?php echo $message_id;?>">Play Audio</a>
<div class="work-message">
<table cellpadding="5" cellspacing="0" border="1">
<?php echo $td; ?>
</table>
</div>

<div class="jsplayerWorkTicket">
<?php echo $player_html; ?>
</div>
<div class="work-logs">
<table cellpadding="5" cellspacing="0" border="1"> <?php echo $td3; ?> </table>
</div>

<div class="work-notes">
<table cellpadding="5" cellspacing="0" border="1">
<tr><td>Notes:</td></tr>
<tr><td><form method="post" action="addnote.php"><input type="hidden" name="message_id" value="<?php echo $message_id; ?>"><textarea name="mynote" rows="5" cols="80"></textarea><br><input type="submit"></form></td></tr>
<?php echo $td2; ?>
</table>
</div>


</body>
</html>
