<?php
if(!isset($_POST["id"]) || !isset($_USER) || !safeReferrer() || !array_key_exists($_POST["id"], $_ATWEETS)) back();
if(is_restricted($_USER, "REPORT")) {
  $_SESSION["notice"] = "This feature is currently disabled.";
  back();
}

$b = $_POST["id"];
$u = $_USER["id"];
$i = $snowflake->id();

$_BWEET = $_ATWEETS[$_POST["id"]];
if($_BWEET["user"]["id"] == $_USER["id"]) {
  $_SESSION["notice"] = "Can't report your own bweet.";
  back();
}
$_BWEETER = $_BWEET["user"]["username"];
$_USERN = $_USER["username"];
$author = "[@$_BWEETER](https://bwitter.me/$_BWEETER)";
$reporter = "[@$_USERN](https://bwitter.me/$_USERN)";
$fields = array(
  array(
    "name" => "Bweet Author",
    "value" => $author,
    "inline" => true
  ),
  array(
    "name" => "Bweet Content",
    "value" => $_BWEET["content"],
    "inline" => true
  ),
  array(
    "name" => "Reported By",
    "value" => $reporter,
    "inline" => true
  )
);

$stmt = $db->prepare("SELECT COUNT(*) FROM `reports` WHERE `bweet` = ? AND `reporter` = ?");
if($stmt === false) die(mysqli_error($db));
$stmt->bind_param("ii", $b, $u);
if($stmt === false) die(mysqli_error($db));
$success = $stmt->execute();
if($success === false) die(mysqli_error($db));
$res = $stmt->get_result();
$count = $res->fetch_array()[0];
if($count > 0) {
  $_SESSION["notice"] = "Already reported this bweet.";
  back();
}
sendDiscord("reports", json_encode($_BWEET["content"]), false, $fields);
$stmt = $db->prepare("INSERT INTO `reports` (`id`, `bweet`, `reporter`) VALUES (?, ?, ?)");
if($stmt === false) die(mysqli_error($db));
$stmt->bind_param("iii", $i, $b, $u);
if($stmt === false) die(mysqli_error($db));
$stmt->execute();

$_SESSION["notice"] = "Bweet reported.";
back();
?>