<?php

//print "<pre>"; var_dump($_POST); print "</pre>"; 

require_once("db.php");

$myName=getName($_COOKIE['voicemail-userid']);
$message_id=$_POST['message_id'];
$note=addslashes($_POST['mynote']);
$logentry=$myName." added a note";

$db=connect();
$sql="INSERT INTO messages_logfile (message_id,action_description) VALUES ({$message_id},'{$logentry}')";
$db->query($sql);

$sql="INSERT INTO messages_notes (message_id,note) VALUES ({$message_id},'{$note}')";
$db->query($sql);

header("Location: workTicket.php?message_id={$message_id}");

?>
