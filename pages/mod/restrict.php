<?php
if(!safeReferrer()) back();

if(!isset($args[2]) || !array_key_exists($args[2], $_IDUSERS)) back();
$u = $_IDUSERS[$args[2]];

$_POST["torestrict"] = strtoupper($_POST["torestrict"]);

$_POST["time"]["hours"] = intval($_POST["time"]["hours"]);
$_POST["time"]["hours"] += intval($_POST["time"]["days"])*24;
$hours = $_POST["time"]["hours"];
$seconds = $hours * 3600;
if(isset($_POST["unrestrict"])) $seconds = 0;

restrict($u, $_POST["torestrict"], $seconds);
back();
?>