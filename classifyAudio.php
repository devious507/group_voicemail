<?php

require_once("db.php");
//unset($_SERVER['PHP_AUTH_USER']);
//unset($_SERVER['PHP_AUTH_PW']);
phpAuth();

if(!isset($_GET['message_id'])) {
	header("Location: index.php");
	exit();
}
$message_id=intval($_GET['message_id']);

$db=connect();
$sql="SELECT filename FROM messages WHERE message_id={$message_id}";
$res=$db->query($sql);
$row=$res->fetchRow();
$fname=$row[0];
$tmp=preg_split("/\//",$fname);
$filename=$tmp[6]."/".$tmp[7];
$sql="select username FROM users WHERE user_id={$_COOKIE['voicemail-userid']}";
$res=$db->query($sql);
$row=$res->fetchRow();
$username=$row[0];
$msg=$username." started listening to voicemail";
$sql="INSERT INTO messages_logfile (message_id,action_description) VALUES ({$message_id},'{$msg}')";
$res=$db->query($sql);
$sql="SELECT message_id,caller_name,caller_Address,caller_city,caller_state,caller_zip,call_type_id,current_owner,status_id FROM messages WHERE message_id={$message_id}";
$res=$db->query($sql);
$data=$res->fetchRow(MDB2_FETCHMODE_ASSOC);
$callTypeSelector=getCallType($data['call_type_id']);


?>
<html>
<head>
<title>Classifier</title>
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
  <div id="jquery_jplayer_1" class="jp-jplayer"></div>
  <div id="jp_container_1" class="jp-audio">
    <div class="jp-type-single">
      <div class="jp-gui jp-interface">
        <ul class="jp-controls">
          <li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
          <li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
          <li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
          <li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
          <li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
          <li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
        </ul>
        <div class="jp-progress">
          <div class="jp-seek-bar">
            <div class="jp-play-bar"></div>
          </div>
        </div>
        <div class="jp-volume-bar">
          <div class="jp-volume-bar-value"></div>
        </div>
        <div class="jp-time-holder">
          <div class="jp-current-time"></div>
          <div class="jp-duration"></div>
          <ul class="jp-toggles">
            <li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a></li>
            <li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat off</a></li>
          </ul>
        </div>
      </div>
      <div class="jp-details">
        <ul>
          <li><span class="jp-title"></span></li>
        </ul>
      </div>
      <div class="jp-no-solution">
        <span>Update Required</span>
        To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
      </div>
    </div>
  </div>

<a href="dumpCookies.php">Dump Cookies</a><br>
<form method="post" action="updateRecord.php">
<table cellpadding="5" cellspacing="0" border="1">
<tr><td>Message ID</td><td><input type="hidden" name="message_id" value="<?php echo $message_id;?>"><?php echo $message_id; ?></td></tr>
<tr><td>Username</td><td><?php echo $username; ?></td></tr>
<tr><td>Caller Name</td><td><input type="text" name="caller_name" value="<?php echo $data['caller_name'];?>"></td></tr>
<tr><td>Caller Address</td><td><input type="text" name="caller_address" value="<?php echo $data['caller_address'];?>"></td></tr>
<tr><td>Caller City</td><td><input type="text" name="caller_city" value="<?php echo $data['caller_city'];?>"></td></tr>
<tr><td>Caller State</td><td><input type="text" name="caller_state" value="<?php echo $data['caller_state'];?>"></td></tr>
<tr><td>Caller Zip</td><td><input type="text" name="caller_zip" value="<?php echo $data['caller_zip'];?>"></td></tr>
<tr><td>Caller Phone</td><td><input type="text" name="caller_phone" value="<?php echo $data['caller_phone'];?>"></td></tr>
<tr><td>Call Type</td><td><?php echo $callTypeSelector; ?></td></tr>
<tr><td colspan="2">Notes:</td></tr>
<tr><td colspan="2"><textarea name="newnote" rows="5" cols="80"></textarea></td></tr>
<tr><td colspan="2"><input type="hidden" name="status_id" value="1"><input type="hidden" name="current_owner" value="<?php echo $_COOKIE['voicemail-userid']; ?>"><input type="submit"></td></tr>
</form>
</body>
</html>
