<?php

require_once("Mail/mimeDecode.php");
require_once("db.php");


if(is_devel) {
	$filedir='/home/paulo/public_html/dashboard/voicemail-dev/audio_files';
} else {
	$filedir='/home/paulo/public_html/dashboard/voicemail/audio_files';
}

$message='';

while(!feof(STDIN)) {
	$message.=fread(STDIN,1024);
}
//$message = file_get_contents('message.parse');
//


$params['include_bodies'] = true;
$params['decode_bodies'] = true;
$params['decode_headers'] = true;

$parser = new Mail_mimeDecode($message);

$structure = $parser->decode($params);

foreach($structure->parts as $x) {
	print $x->headers['content-type']."\n";
	if(preg_match("/^audio\/x-wav/",$x->headers['content-type'])) {
		$binary_data=$x->body;
		$filename=tempnam($filedir,'msg_');
		$fh=fopen($filename,'w');
		fwrite($fh,$binary_data);
		fclose($fh);
		$newname=$filename.".wav";
		rename($filename,$newname);
		$filename=$newname;
		unset($newname);
		chmod($filename,444);
		$sql="INSERT INTO messages (filename) values ('{$filename}')";
		$db=connect();
		$db->query($sql);
		$sql="SELECT max(message_id) FROM messages";
		$res=$db->query($sql);
		$row=$res->fetchRow();
		$max=$row[0];
		$sql="INSERT INTO messages_logfile (message_id,action_description) values ({$max},'SYSTEM created ticket with audio')";
		$db->query($sql);
		$db->disconnect();
		exit();
	}
}




?>
