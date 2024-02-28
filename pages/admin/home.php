<?php
if($_SERVER['REQUEST_METHOD'] == "POST") {
  $name = "notice";
  $stmt = $db->prepare("UPDATE `data` SET `value` = ? WHERE `name` = ?");
  $stmt->bind_param("ss", $_POST["data"], $name);
  $stmt->execute();
  if($_POST["send"])
    sendDiscord("updates", $_POST["data"], isset($_POST["ping"]), array(), "Notice");
  back();
} else {
$d = dirname(__FILE__);
if (PHP_OS_FAMILY === "Windows") {
  $cpuload = "0";
  $ncpu = 1;
} else {
  if(is_file('/proc/cpuinfo')) {
    $cpuinfo = file_get_contents('/proc/cpuinfo');
    preg_match_all('/^processor/m', $cpuinfo, $matches);
    $ncpu = count($matches[0]);
  }
	$load = sys_getloadavg();
	$cpuload = ($load[0]*100)/$ncpu;
}
function hr($bytes, $precision = null) {
  static $BYTE_UNITS = ["B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
  static $BYTE_PRECISION = [0, 0, 1, 2, 2, 3, 3, 4, 4];
  static $BYTE_NEXT = 1024;
  for ($i = 0; ($bytes / $BYTE_NEXT) >= 0.9 && $i < count($BYTE_UNITS); $i++) $bytes /= $BYTE_NEXT;
  return round($bytes, is_null($precision) ? $BYTE_PRECISION[$i] : $precision) . " " . $BYTE_UNITS[$i];
}
$_LTWEET = $_ATWEETS[array_key_first($_ATWEETS)];
$_LUSER = $_USERS[array_key_last($_USERS)];
?>
<style>
  label {
    position: relative;
    top: -2px;
  }
  .ping {
    background: hsla(235,85.6%,64.7%,0.15);
    color: hsl(235,66.7%,58.8%);
    border-radius: 3px;
    padding: 0 2px;
    font-weight: 500;
    unicode-bidi: -moz-plaintext;
    unicode-bidi: plaintext;
  }
</style>
<table class="stats">
  <tr>
    <th>PHP Version</th>
    <td><?=PHP_VERSION?></td>
  </tr>
  <tr>
    <th>PHP Extensions</th>
    <td><?=count(get_loaded_extensions())?></td>
  </tr>
  <tr>
    <th>MySQL Version</th>
    <td><?=explode("-", $db->server_info)[0]?></td>
  </tr>
  <tr><th>&nbsp;</th><td>&nbsp;</td></tr>
  <tr>
    <th>CPU Usage</th>
    <td><?=$cpuload?>% (<?=$ncpu?> cores)</td>
  </tr>
  <tr>
    <th>RAM Usage</th>
    <td><?=hr(memory_get_usage(true))?></td>
  </tr>
  <tr>
    <th>Disk Space</th>
    <td><?=hr(disk_free_space($d), 0)?> / <?=hr(disk_total_space($d), 0)?> (<?=round(disk_free_space($d)/disk_total_space($d)*100)?>%)</td>
  </tr>
</table>
<div style="padding: 0px 0px 0px 4px">
<h1 style="margin: 0;">Information</h1>
<h4 style="margin: 0;"><b><?=count($_USERS)?></b> User<?=count($_USERS) !== 1 ? "s" : ""?></h4>
<h4 style="margin: 0;"><b><?=count($_VUSERS)?></b> Verified User<?=count($_USERS) !== 1 ? "s" : ""?></h4>
<h4 style="margin: 0;"><b><?=count($_SUSERS)?></b> Suspended User<?=count($_USERS) !== 1 ? "s" : ""?></h4>
<h4 style="margin: 0;"><b><?=count($_ATWEETS)?></b> Bweet<?=count($_ATWEETS) !== 1 ? "s" : ""?></h4>

<br>
<h1 style="margin: 0;">Stats</h1>
<h4 style="margin: 0;">Latest User: <a href="/<?=$_LUSER["username"]?>"><?=$_LUSER["username"]?></a></h4>
<h4 style="margin: 0;">Latest Bweet: <a href="/<?=$_LTWEET["user"]["username"]?>/statuses/<?=$_LTWEET["id"]?>"><?=$_LTWEET["content"]?></a></h4>

<br>
<h1 style="margin: 0;">Site Notice</h1>
<h4 style="margin: 0;">Note: Notice gets parsed as a Bweet, any mentions or BitView embeds will be added automatically.</h4>
<form action="" method="post" id="doingForm">
  <textarea name="data" style="height:250px;resize:none"><?=$_NOTICE?></textarea>
  <div class="bar">
    <input type="checkbox" name="send" id="send">&nbsp;<label for="send">Send Discord Update</label>
    <input type="checkbox" name="ping" id="ping">&nbsp;<label for="ping" class="ping">@everyone</label>
    <span><input type="submit" value="Update" style="padding: 2px 5px;background:white;border:1px solid black;"></span>
  </div>
</form>
</div><?php } ?>