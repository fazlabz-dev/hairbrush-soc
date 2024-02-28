<?php
http_response_code(404);
include_once "inc/main.php";
$url = parse_url($_SERVER["REQUEST_URI"]);
if(isset($url["query"])) parse_str($url["query"], $_GET);
$u = $url["path"];
$args = explode('/', $u);
$args = array_filter($args, function($x) {
  return $x != "/";
});
array_shift($args);
if(count($args) == 0) {
  include "pages/error/".http_response_code().".php";
  exit;
}
switch($args[0]) {
  case "account":
    http_response_code(200);
    switch($args[1]) {
      case "profile_image":
        header("Content-Type: image/jpg");
        die(readfile("assets/default.jpg"));
      case "resend_password":
        include "pages/account/resend.php";
        exit;
      case "reset":
        include "pages/account/reset.php";
        exit;
      case "redeem":
        include "pages/account/redeem.php";
        exit;
      case "delbweet":
        include "pages/account/delbweet.php";
        exit;
      case "report":
        include "pages/account/report.php";
        exit;
      default:
        http_response_code(404);
        break;
    }
    break;
  case "mod":
    if(isset($_USER) && ($_USER["flags"] & TYPE_ADMIN || $_USER["flags"] & TYPE_MODERATOR)) {
      http_response_code(200);
      include "pages/modpanel.php";
      exit;
    } else {
      http_response_code(403);
    }
    break;
  case "admin":
    if(isset($_USER) && $_USER["flags"] & TYPE_ADMIN) {
      http_response_code(200);
      include "pages/panel.php";
      exit;
    } else {
      http_response_code(403);
    }
    break;
  case "help":
    if(count($args) > 1) {
      switch($args[1]) {
        case "contact":
          http_response_code(200);
          include "pages/contact.php";
          exit;
        case "aboutus":
          http_response_code(200);
          include "pages/about.php";
          exit;
        default:
          break;
      }
    }
  /*
  wip
  case "post":
    http_response_code(200);
    include "bweet.php";
    exit;
  */
  default:
    break;
}
$user = strtolower($args[0]);

if(isset($_LUSERS[$user])) {
  http_response_code(200);
  $_PROFILE = $_LUSERS[$user];
  $_EDIT = isset($args[1]) && $args[1] == "edit";
  if($_EDIT && $_USER["id"] != $_PROFILE["id"]) die(header("Location: /$_PROFILE[username]"));
  if($_PROFILE["flags"] & TYPE_SUSPENDED) {
    include "pages/suspended.php";
  } else {
    $_SINGLE = false;
    if(isset($_TWEETS[$_PROFILE["id"]])) {
      $_TWEETS = $_TWEETS[$_PROFILE["id"]];

      if(count($args) > 1 && $args[1] == "statuses" && isset($args[2])) {
        if(array_key_exists($args[2], $_TWEETS)) {
          $_STATUS = $_TWEETS[$args[2]];
          $_SINGLE = true;
        } else {
          http_response_code(404);
          include "pages/error/".http_response_code().".php";
          exit;
        }
      } else {
        $_STATUS = array_shift($_TWEETS);
      }
    } else {
      $_TWEETS = array();
      $_STATUS = array("content" => "<i>No Bweets.</i>", "timestamp" => time(), "id" => 0);
    }
    include "pages/profile.php";
  }
  exit;
} else {
  include "pages/error/".http_response_code().".php";
  exit;
}
?>
