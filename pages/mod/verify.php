<?php
if(!safeReferrer()) back();

if(!isset($args[2]) || !array_key_exists($args[2], $_IDUSERS)) back();
$u = $_IDUSERS[$args[2]];

if(($u["flags"] & TYPE_MODERATOR || $u["flags"] & TYPE_ADMIN))
  if(~$_USER["flags"] & TYPE_ADMIN)
    back();

if($u["flags"] & TYPE_VERIFIED) {
  $u["flags"] &= ~TYPE_VERIFIED;
} else {
  $u["flags"] |= TYPE_VERIFIED;
}
$stmt = $db->prepare("UPDATE `users` SET `flags` = ? WHERE `username` = ?");
$un = $u["username"];
$f = $u["flags"];
$stmt->bind_param("is", $f, $un);
$stmt->execute();
back();
?>