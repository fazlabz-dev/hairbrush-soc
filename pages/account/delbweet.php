<?php
if(!isset($_POST["id"]) || !safeReferrer() || !array_key_exists($_POST["id"], $_ATWEETS)) back();
$i = $_POST["id"];
if($_ATWEETS[$i]["user"]["id"] != $_USER["id"])
  if(~$_USER["flags"] & TYPE_MODERATOR && ~$_USER["flags"] & TYPE_ADMIN)
    back();

$stmt = $db->prepare("DELETE FROM `tweets` WHERE `id` = ?");
$stmt->bind_param("i", $i);
$stmt->execute();
$_SESSION["notice"] = "Bweet deleted.";
back();
?>