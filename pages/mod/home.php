<?php
$_LTWEET = $_ATWEETS[array_key_first($_ATWEETS)];
$_LUSER = $_USERS[array_key_last($_USERS)];
?>
<div style="padding: 0px 0px 0px 4px">
<h1 style="margin: 0;">Information</h1>
<h4 style="margin: 0;"><b><?=count($_USERS)?></b> User<?=count($_USERS) !== 1 ? "s" : ""?></h4>
<h4 style="margin: 0;"><b><?=count($_VUSERS)?></b> Verified User<?=count($_USERS) !== 1 ? "s" : ""?></h4>
<h4 style="margin: 0;"><b><?=count($_SUSERS)?></b> Suspended User<?=count($_USERS) !== 1 ? "s" : ""?></h4>
<h4 style="margin: 0;"><b><?=count($_ATWEETS)?></b> Bweet<?=count($_ATWEETS) !== 1 ? "s" : ""?></h4>

<br>
<h1 style="margin: 0;">Stats</h1>
<h4 style="margin: 0;">Latest User: <a href="/<?=$_LUSER["username"]?>"><?=$_LUSER["username"]?></a></h4>
<h4 style="margin: 0;">Latest Bweet: <a href="/<?=$_LTWEET["user"]["username"]?>/statuses/<?=$_LTWEET["id"]?>"><?=$_LTWEET["content"]?></a></h4>
</div>