<?php
if(!safeReferrer()) back();

if(!isset($args[2]) || !array_key_exists($args[2], $_IDUSERS)) back();
$u = $_IDUSERS[$args[2]];

if($u["flags"] & TYPE_MODERATOR) {
  $u["flags"] &= ~TYPE_MODERATOR;
} else {
  $u["flags"] |= TYPE_MODERATOR;
}
$stmt = $db->prepare("UPDATE `users` SET `flags` = ? WHERE `username` = ?");
$un = $u["username"];
$f = $u["flags"];
$stmt->bind_param("is", $f, $un);
$stmt->execute();
back();
?>