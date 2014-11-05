<?php

require_once('db.php');
//print "<pre>"; var_dump($_POST); "</pre>"; exit();

$message_id=$_POST['message_id'];
if(isset($_POST['newnote'])) {
	$myNote=addslashes($_POST['newnote']);
	unset($_POST['newnote']);
} else {
	$myNote='';
}
unset($_POST['message_id']);

foreach($_POST as $k=>$v) {
	switch($k) {
	case "call_type_id":
	case "current_owner":
		$pairs[]=$k."=".$v;
		break;
	default:
		$pairs[]=$k."='".$v."'";
		break;
	}
}

$db=connect();
$sql="select username FROM users WHERE user_id={$_COOKIE['voicemail-userid']}";
$res=$db->query($sql);
$row=$res->fetchRow();
$username=$row[0];
if($_POST['status_id'] == 2) {
	$msg=$username." closed the call";
} elseif($_POST['status_id'] == 3) {
	$msg=$username." re-opened the call";
} elseif($_POST['status_id'] == 4) {
	$msg=$username." finalized the call";
} else {
	$msg=$username." updated message properties";
}
$sql="INSERT INTO messages_logfile (message_id,action_description) VALUES ({$message_id},'{$msg}')";
$res=$db->query($sql);

$sql="UPDATE messages SET ".implode(",",$pairs)." WHERE message_id={$message_id}";
$db->query($sql);
if($myNote != '') {
	$myName=getName($_COOKIE['voicemail-userid']);
	$newNote="--".$myName."--<br>&nbsp;<br>".$myNote;
	$myNote=$newNote;
	unset($newNote);
	$sql="INSERT INTO messages_notes (message_id,note) VALUES ({$message_id},'$myNote')";
	$db->query($sql);
}
if($_POST['status_id'] == 2) {
	header("Location: index.php");
	exit();
} elseif($_POST['status_id'] == 4) {
	header("Location: index.php");
	exit();
} else {
	header("Location: workTicket.php?message_id={$message_id}");
	exit();
}

?>
