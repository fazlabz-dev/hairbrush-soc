<?php
// Debug
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

require "vendor/autoload.php";

define("TYPE_USER", 1);
define("TYPE_SUSPENDED", 2);
define("TYPE_VERIFIED", 4);
define("TYPE_MODERATOR", 8);
define("TYPE_ADMIN", 16);


$_FLAGS = array(
  TYPE_SUSPENDED => "Suspended",
  TYPE_VERIFIED => "Verified",
  TYPE_MODERATOR => "Moderator",
  TYPE_ADMIN => "Admin"
);

$_RESTRICTIONS = array(
  "BWEET" => "Restrict Bweeting",
  "REPORT" => "Restrict Reporting"
);

include "db.php";
$unix = strtotime('2020-10-20')*1000;

$snowflake = new \Godruoyi\Snowflake\Snowflake;
$snowflake->setStartTimeStamp($unix);

$date = "H:i F d, Y";
$adate = "D d, y @ h:i A";

// Session
session_start();

// Utilities
$webhooks = array(
  "updates" => "",
  "reports" => "",
  "invite" => ""
);

$titles = array(
  "updates" => "Hairbrush has been updated!",
  "reports" => "New report!",
  "invite" => "You're invited to Hairbrush!"
);

function sendDiscord($channel, $data, $ping = FALSE, $fields = array(), $title = NULL) {
  global $webhooks;
  global $titles;
  $timestamp = date("c", strtotime("now"));
  if(!isset($ping)) $ping = FALSE;
  if(!isset($title)) $title = $titles[$channel];
  $json_data = json_encode(array(
      "avatar_url" => "https://" . $_SERVER["HTTP_HOST"] . "/images/hairbrushappicon.png",
      "username" => "Hairbrush Social",
      "content" => $ping ? "@everyone" : "",
      "embeds" => array(array(
        "title" => $title,
        "type" => "rich",
        "timestamp" => $timestamp,
        "description" => count($fields) == 0 ? $data : "",
        "fields" => $fields
      ))
  ));
  $ch = curl_init($webhooks[$channel]);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_exec($ch);
  curl_close($ch);
}

if(isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
}

if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off" || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "https") || (isset($_SERVER['HTTP_CF_VISITOR']) && ($visitor = json_decode($_SERVER['HTTP_CF_VISITOR'])) && $visitor->scheme == 'https')) {
  $_SERVER['HTTPS'] = "on";
} else {
  $_SERVER['HTTPS'] = "off";
}

function device_time($dateFormatString = NULL) { 
  $responseTime = microtime(true);
  $ch = file_get_contents('https://ipinfo.io/'.$_SERVER["REMOTE_ADDR"].'/json/');
  $ipParts = json_decode($ch,true);
  $timezone = $ipParts['timezone'];
  $responseTime = microtime(true) - $responseTime;
  $date = new DateTime(date('m/d/Y h:i:s a', (time() - $responseTime) + 1));
  $date->setTimezone(new DateTimeZone($timezone));
  if($dateFormatString == null) return $date->getTimestamp();
  else return $date->format($dateFormatString);
}

function restricted($user, $feature) {
  global $db;

  $data = json_decode($user["data"], true);
  if(!isset($data["RESTRICT_$feature"])) {
    $data["RESTRICT_$feature"] = 0;
    $uid = $user["id"];
    $_data = json_encode($data);
    $stmt = $db->prepare("UPDATE `users` SET `data` = ? WHERE `id` = ?");
    $stmt->bind_param("si", $_data, $uid);
    $stmt->execute();
  }
  return $data["RESTRICT_$feature"] - time();
}

function is_restricted($user, $feature) {
  return restricted($user, $feature) > 0;
}

function restrict($user, $feature, $time = 60) {
  global $db;

  $data = json_decode($user["data"], true);
  if($time === -1) {
    $data["RESTRICT_$feature"] = PHP_INT_MAX;
  } elseif($time === 0) {
    $data["RESTRICT_$feature"] = 0;
  } else {
    $data["RESTRICT_$feature"] = time() + $time;
  }
  
  $uid = $user["id"];
  $_data = json_encode($data);
  $stmt = $db->prepare("UPDATE `users` SET `data` = ? WHERE `id` = ?");
  $stmt->bind_param("si", $_data, $uid);
  $stmt->execute();
}

function parse_bweet($content) {
  global $_USERS;
  global $_ATWEETS;

  // @user
  $mention = '/(?<=^|\s)(@([A-Za-z0-9_]{1,20}))/';
  $content = preg_replace_callback($mention, function($match) use($_USERS) {
    if (isset($_USERS[strtolower($match[2])])) {
      return '<a href="/'.$match[2].'" title="'.$match[0].'">'.$match[0].'</a>';
    } else {
      return $match[0];
    }
  }, $content);

  $url = '/((?:https?):\/\/[^"<\s]+)(?![^<>]*>|[^"]*?<\/a)/';
  $content = preg_replace($url, '<a href="$0" target="_blank">$0</a>', $content);

  return $content;
}

function render_bweet($_STATUS) {
  global $_PROFILE;
  global $_USER;
  ?>
  <tr class="h-entry" id="status_<?=$_STATUS["timestamp"]?>">
<?php if(!isset($_PROFILE)) { ?>
    <td class="thumb vcard author">
      <a href="/<?=$_STATUS["user"]["username"]?>" class="url"><img alt="101" class="photo fn"
          src="/account/profile_image/<?=$_STATUS["user"]["username"]?>.jpg" width="50" height="50" /></a>
    </td>
<?php } ?>
    <td>
<?php if(!isset($_PROFILE)) { ?>
      <strong><a href="/<?=$_STATUS["user"]["username"]?>" title="<?=$_STATUS["user"]["username"]?>">
          <?=$_STATUS["user"]["username"]?></a><?=isset($_STATUS["user"]["badge"]) ? '&nbsp;<i class="badge '.$_STATUS["user"]["badge"].'"></i>' : ""?></strong><br>
<?php } ?>
      <span class="entry-title entry-content"><?=parse_bweet($_STATUS["content"])?></span><br>
      <span class="meta entry-meta">
        <a href="/<?=$_STATUS["user"]["username"]?>/statuses/<?=$_STATUS["id"]?>" class="entry-date" rel="bookmark"><abbr
            class="published"><?=time_since($_STATUS["timestamp"])?></abbr></a> from web
        <span id="status_actions_<?=$_STATUS["timestamp"]?>" style="float: right; display:flex;">
          <?php if(isset($_USER)) { ?>
          <?php if($_USER["flags"] & TYPE_MODERATOR || $_USER["flags"] & TYPE_ADMIN || $_USER["id"] == $_STATUS["user"]["id"]) { ?>
          <form action="/account/delbweet" method="post" id="delete_<?=$_STATUS["id"]?>">
            <input type="hidden" name="id" value="<?=$_STATUS["id"]?>">
            <a href="javascript:void(0);"
              onclick="document.getElementById(`delete_<?=$_STATUS["id"]?>`).submit();">Delete</a>
          </form>
          <?php } ?>
          <form action="/account/report" method="post" id="report_<?=$_STATUS["id"]?>" style="margin-left: 4px;">
            <input type="hidden" name="id" value="<?=$_STATUS["id"]?>">
            <a href="javascript:void(0);"
              onclick="document.getElementById(`report_<?=$_STATUS["id"]?>`).submit();">Report</a>
          </form>
          <?php } ?>
        </span>
      </span>
    </td>
  </tr>
<?php }

function back() {
  if(!isset($_SERVER['HTTP_REFERER'])) return die(header("Location: /"));
  else return die(header("Location: $_SERVER[HTTP_REFERER]"));
}

function safeReferrer() {
  if(!isset($_SERVER['HTTP_REFERER'])) return false;
  $parsed = parse_url($_SERVER['HTTP_REFERER']);
  return $parsed["host"] == $_SERVER['SERVER_NAME'];
}

function verifyCaptcha($response) {
  $options = array(
    'secret' => "### HCAPTCHA SECRET HERE ###",
    'response' => $response
  );
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, 'https://hcaptcha.com/siteverify');
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($options));
  curl_setopt($curl, CURLOPT_HEADER, false);
  $data = curl_exec($curl);
  curl_close($curl);
  $response = json_decode($data);
  return $response->success;
}

function time_since($since) {
  global $date;
  $since = time() - intval($since);

  if($since > 60 * 60 * 24) {
    return gmdate($date, time() - $since);
  }

  $chunks = array(
      array(60 * 60 * 24 * 365, 'year'),
      array(60 * 60 * 24 * 30, 'month'),
      array(60 * 60 * 24 * 7, 'week'),
      array(60 * 60 * 24, 'day'),
      array(60 * 60, 'hour'),
      array(60, 'minute'),
      array(1, 'second')
  );

  for ($i = 0, $j = count($chunks); $i < $j; $i++) {
      $seconds = $chunks[$i][0];
      $name = $chunks[$i][1];
      if (($count = floor($since / $seconds)) != 0) {
          break;
      }
  }

  $print = ($count == 1) ? '1 '.$name : "$count {$name}s";
  return $print." ago";
}

// Hard-coded variables
$_NOTICE = "";

// Database
if(isset($_SESSION["uid"])) {
  $uid = $_SESSION["uid"];
  $stmt = $db->prepare("SELECT * FROM users WHERE `id` = ?");
  if($stmt === false) die(mysqli_error($db));
  $stmt->bind_param("s", $uid);
  $success = $stmt->execute();
  if($success === false) die(mysqli_error($db));
  $res = $stmt->get_result();
  $_USER = $res->fetch_array();
}

if(isset($_USER) && $_USER["flags"] & TYPE_SUSPENDED) {
  unset($_SESSION);
  session_unset();
  session_destroy();
  die(header("Location: /"));
}

$stmt = $db->prepare("SELECT * FROM `data`");
if($stmt === false) die(mysqli_error($db));
$success = $stmt->execute();
if($success === false) die(mysqli_error($db));
$res = $stmt->get_result();
$_NOTICE = "";
while($row = $res->fetch_array()) {
  $GLOBALS["_".strtoupper($row["name"])] = $row["value"];
}

if(isset($_SESSION["notice"])) {
  $_NOTICE = $_SESSION["notice"];
  $_SESSION["notice"] = null;
}

if(isset($_SESSION["error"])) {
  $_ERROR = $_SESSION["error"];
  $_SESSION["error"] = null;
}

$stmt = $db->prepare("SELECT * FROM `users` ORDER BY id DESC");
if($stmt === false) die(mysqli_error($db));
$success = $stmt->execute();
if($success === false) die(mysqli_error($db));
$res = $stmt->get_result();
$_LATEST = array();
while($row = $res->fetch_array()) {
  if(count($_LATEST) >= 5) break;
  if(~$row["flags"] & TYPE_SUSPENDED) $_LATEST[] = $row["username"];
}

$stmt = $db->prepare("SELECT * FROM `timezones`");
if($stmt === false) die(mysqli_error($db));
$success = $stmt->execute();
if($success === false) die(mysqli_error($db));
$res = $stmt->get_result();
$_TIMEZONES = array();
while($row = $res->fetch_array()) {
  $_TIMEZONES[$row["id"]] = $row;
}

$stmt = $db->prepare("SELECT * FROM `users`");
if($stmt === false) die(mysqli_error($db));
$success = $stmt->execute();
if($success === false) die(mysqli_error($db));
$res = $stmt->get_result();
$_USERS = array();
$_IDUSERS = array();
$_EUSERS = array();
$_VUSERS = array();
$_SUSERS = array();
while($row = $res->fetch_array()) {
  if($row["flags"] & TYPE_VERIFIED) {
    $row["badge"] = "verified";
  }
  $_USERS[strtolower($row["username"])] = $row;
  $_LUSERS[strtolower($row["username"])] = $row;
  $_EUSERS[strtolower($row["email"])] = $row;
  $_IDUSERS[$row["id"]] = $row;
  if($row["flags"] & TYPE_VERIFIED)
    $_VUSERS[$row["username"]] = $row;
  if($row["flags"] & TYPE_SUSPENDED)
    $_SUSERS[$row["username"]] = $row;
}

$stmt = $db->prepare("SELECT * FROM `tweets` ORDER BY `timestamp` DESC");
if($stmt === false) die(mysqli_error($db));
$success = $stmt->execute();
if($success === false) die(mysqli_error($db));
$res = $stmt->get_result();
$_TWEETS = array();
$_ATWEETS = array();
$_MYTWEETS = array();
while($row = $res->fetch_array()) {
  $uid = $row["user"];
  if(isset($_IDUSERS[$uid])) {
    $user = $_IDUSERS[$uid];
    $user[6] = $user["email"] = $user[8] = $user["password"] = null;
    if($user["flags"] & TYPE_SUSPENDED) continue;
  } else continue;

  $_ATWEETS[$row["id"]] = $row;
  $_ATWEETS[$row["id"]]["user"] = $user;
  $_TWEETS[$row["user"]][$row["id"]] = $row;
  $_TWEETS[$row["user"]][$row["id"]]["user"] = $user;
  if(isset($_USER) && $row["user"] == $_USER["id"]) $_MYTWEETS[] = $row;
}

$stmt = $db->prepare("SELECT `id`, `content`, `date` FROM `tweets` ORDER BY `date` DESC");
if($stmt === false) die(mysqli_error($db));
$success = $stmt->execute();
if($success === false) die(mysqli_error($db));
$res = $stmt->get_result();
$_NBLOG = array();
while($row = $res->fetch_array()) {
	$_NBLOG[] = $row;
}

$stmt = $db->prepare("SELECT * FROM `reports`");
if($stmt === false) die(mysqli_error($db));
$success = $stmt->execute();
if($success === false) die(mysqli_error($db));
$res = $stmt->get_result();
$_REPORTS = array();
$_AREPORTS = array();
while($row = $res->fetch_array()) {
  if(!array_key_exists($row["bweet"], $_ATWEETS)) continue;
  $_AREPORTS[$row["id"]] = $row;
  if($row["resolved"] == 0)
    $_REPORTS[$row["id"]] = $row;
}
?>