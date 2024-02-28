<?php
include "inc/main.php";
header("Content-type: text/xml");

echo "<?xml version='1.0' encoding='UTF-8'?><rss version='2.0'><channel><title>Hairbrush Social | RSS</title><link>/</link><description>Latest status updates on " . $_SERVER["HTTP_HOST"] . ", an instance of Hairbrush Social.</description><language>en-us</language>";

foreach($_ATWEETS as $_STATUS) {
  $u = $_STATUS["user"]["username"];
  $d = htmlspecialchars($_STATUS["content"]);
  $t = time_since($_STATUS["timestamp"]);
  echo "<item><title>@$u: $d</title><link>/".$_STATUS["user"]["username"]."/statuses/".$_STATUS["id"]."</link><description>$t</description></item>";
}

echo "</channel></rss>";
?>