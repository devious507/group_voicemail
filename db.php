<?php

if((preg_match("/voicemail-dev/",$_SERVER['SCRIPT_FILENAME']) || preg_match($_SERVER['PWD'])) {
	DEFINE('dsn','pgsql://paulo@localhost/voicemail-dev');
	DEFINE('is_devel',true);
} else {
	DEFINE('dsn','pgsql://paulo@localhost/voicemail');
	DEFINE('is_devel',false);
}

require_once('MDB2.php');

function getName($userid) {
	$db=connect();
	$sql="SELECT username FROM users WHERE user_id={$userid}";
	$res=$db->query($sql);
	$row=$res->fetchRow();
	return $row[0];
}
function getTxtRow($name,$value,$type="text") {
	$form_part=txtBox($name,$value,$type);
	return "<tr><td>{$name}</td><td>{$form_part}</td></tr>\n";
}
function txtBox($name,$value,$type="text") {
	if($type == "hidden") {
		return "<input type=\"{$type}\" name=\"{$name}\" value=\"{$value}\">{$value}";
	} else {
		return "<input type=\"{$type}\" name=\"{$name}\" value=\"{$value}\">";
	}
}
function phpAuth() {
	global $_SERVER;
	if (!isset($_SERVER['PHP_AUTH_USER'])) {
		    header('WWW-Authenticate: Basic realm="My Realm"');
		        header('HTTP/1.0 401 Unauthorized');
		        echo 'Text to send if user hits Cancel button';
			exit;
	} else {
		$user = $_SERVER['PHP_AUTH_USER'];
		$pass = $_SERVER['PHP_AUTH_PW'];
		$db=connect();
		$sql="SELECT user_id FROM users WHERE username='{$user}' AND password='{$pass}'";
		$res=$db->query($sql);
		$valid=false;
		while(($row=$res->fetchRow())==true) {
			setcookie('voicemail-userid',$row[0]);
			$valid=true;
			return;
		}
		if($valid == false) {
			echo 'Invalid Login';
			exit();
		}
	}
}
function getStatusID($active) {
	global $_COOKIE;
	$myIDNUM=$_COOKIE['voicemail-userid'];
	$name=getName($myIDNUM);
	switch($name) {
	case "paulo":
	case "darlab":
	case "daveb":
		$admin=true;
		break;
	default:
		$admin=false;
		break;
	}
	$db=connect();
	if($admin) {
		$sql="select * FROM status WHERE status_id > 0";
	} else {
		$sql="select * FROM status WHERE status_id > 0 AND status_id < 4";
	}
	$res=$db->query($sql);
	$rv="<select name=\"status_id\">\n";
	while(($row=$res->fetchRow())==true) {
		$id=$row[0];
		$txt=$row[1];
		if($active == $id) {
			$rv.="<option value=\"{$id}\" selected=\"selected\">{$txt}</option>\n";
		} else {
			$rv.="<option value=\"{$id}\">{$txt}</option>\n";
		}
	}
	$rv.="</select>\n";
	return $rv;
}
function getCurrentOwner($active) {
	$db=connect();
	$sql="select user_id,username FROM users WHERE user_id > 0 ORDER BY username";
	$res=$db->query($sql);
	$rv="<select name=\"current_owner\">\n";
	while(($row=$res->fetchRow())==true) {
		$id=$row[0];
		$txt=$row[1];
		if($active == $id) {
			$rv.="<option value=\"{$id}\" selected=\"selected\">{$txt}</option>\n";
		} else {
			$rv.="<option value=\"{$id}\">{$txt}</option>\n";
		}
	}
	$rv.="</select>\n";
	return $rv;
}
function getCallType($active) {
	$db=connect();
	$sql="select * from call_types ORDER BY call_type_id";
	$res=$db->query($sql);
	$rv="<select name=\"call_type_id\">\n";
	while(($row=$res->fetchRow())==true) {
		$id=$row[0];
		$txt=$row[1];
		if($active == $id) {
			$rv.="<option value=\"{$id}\" selected=\"selected\">{$txt}</option>\n";
		} else {
			$rv.="<option value=\"{$id}\">{$txt}</option>\n";
		}
	}
	$rv.="</select>\n";
	return $rv;
}


function getStatusList() {
	$db=connect();
	$sql="SELECT * FROM status";
	$res=$db->query($sql);
	while(($row=$res->fetchRow())==true) {
		$list[$row[0]]=$row[1];
	}
	return $list;
}
function connect() {
	$db=MDB2::singleton(dsn);
	checkError($db);
	return $db;
}

function checkError($test) {
	if(PEAR::isError($test)) {
		print "Database Error:<br>\n";
		print $test->getMessage();
		exit();
	}
}
?>
