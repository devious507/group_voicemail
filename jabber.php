<?php

function MailMessage() {
	if(is_devel) {
		$dir="voicemail-dev";
	} else {
		$dir="voicemail";
	}
	$now=date("H:i:s m/d/Y");
	$mailto="darla@visionsystems.tv";
	$subject="New Voicemail Message Arrived at {$now} see http://dashboard.visionsystems.tv/{$dir}";
	mail($mailto,$subject,$subject);

}

function JabberMessage() {
	require_once("classes/class.jabber.php");
	if(is_devel) {
		$dir="voicemail-dev";
	} else {
		$dir="voicemail";
	}
	$now=date("H:i:s m/d/Y");
	$jabber = new Jabber;

	$jabber->server         =       'asterisk.visionsystems.tv';
	$jabber->port           =       5222;
	$jabber->username       =       'alertsystem';
	$jabber->password       =       'doodle';
	$jabber->resource       =       'ClassJabberPHP';

	//$jabber->enable_logging       =       '/tmp/jabbeR_logfile.txt';
	$jabber->Connect() or die ("Couldn't Connect");
	$jabber->SendAuth() or die ("Couldn't Authenticate");
	$jabber->SendPresence();
	$jabber->RosterUpdate();
	foreach($jabber->roster as $item) {
		$jid=$item['jid'];
		if(($item['group'] == 'IT') || ($item['group'] == 'CSR')) {
			$jabber->SendMessage($jid,
				'chat',
				NULL,
				array("body" => "New Voicemail Message Arrived at {$now} see http://dashboard.visionsystems.tv/{$dir}"));
		}
	}
	$jabber->CruiseControl(1);
	$jabber->disconnect();

}
?>
