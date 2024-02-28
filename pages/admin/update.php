<?php
if(!safeReferrer()) back();

if($_SERVER['REQUEST_METHOD'] == "POST") {
  sendDiscord("updates", $_POST["data"], isset($_POST["ping"]));
  back();
} else
?>
<style>
  .ping {
    background: hsla(235,85.6%,64.7%,0.15);
    color: hsl(235,66.7%,58.8%);
    border-radius: 3px;
    padding: 0 2px;
    font-weight: 500;
    unicode-bidi: -moz-plaintext;
    unicode-bidi: plaintext;
    position: relative;
    top: -2px;
  }
</style>
<form action="" method="post">
  <textarea name="data" style="width:100%;height:250px;resize:none"></textarea>
  <div style="display: inline-block;">
    <input type="checkbox" name="ping" id="ping">&nbsp;<label for="ping" class="ping">@everyone</label>
  </div>
  <div style="display: inline-block;float:right;">
    <input type="submit" id="submit" value="Post" style="margin:0px;margin-left:auto;">
  </div>
</form>