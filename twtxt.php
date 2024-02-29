# nick        = Hairbrush Social | Twtxt
# url         = http://<?= $_SERVER["HTTP_HOST"] ?>/twtxt
# avatar      = http://<?= $_SERVER["HTTP_HOST"] ?>/images/hairbrushappicon.png
# description = Latest status updates on <?= $_SERVER["HTTP_HOST"] ?>, an instance of Hairbrush Social.

<?php
include "inc/main.php";
header("Content-Type: text/plain; charset=utf-8");
foreach($_ATWEETS as $_STATUS) {
  $u = $_STATUS["user"]["username"];
  $d = htmlspecialchars($_STATUS["content"]);
  $t = date("c", strtotime($_STATUS["date"]));
  echo $t . "\t" . "@" . $u . ": " . $d . "\n";
}
?>