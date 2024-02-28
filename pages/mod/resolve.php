<?php
if(!isset($args[2]) || empty($args[2]) || !array_key_exists($args[2], $_REPORTS) || !safeReferrer()) die("e");
$r = $_REPORTS[$args[2]];
if($r["resolved"] == 1) back();

$stmt = $db->prepare("UPDATE `reports` SET `resolved` = 1 WHERE `id` = ?");
$i = $r["id"];
$stmt->bind_param("i", $i);
if(!$stmt->execute())
  die($stmt->error);

back();
?>