<?php include_once "inc/main.php";
if(!isset($_USER)) die(header("Location: /login?return=".urlencode($_SERVER["REQUEST_URI"])));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<?php include "inc/head.php"; ?>

<body class="account" id="front">
  <div id="container" class="subpage">
    <?php include "inc/header.php"; ?>
    <div id="content">
      <div class="wrapper" style="padding: 10px;">
        <?php if(!empty($_NOTICE)) { ?>
        <div
          style="border: 5px solid #87BC44; background: white; font-weight: bold; padding: 10px; margin:10px; font-size:1.1em">
          <?=$_NOTICE?></div>
        <?php } ?>
        <?php if(isset($_USER)) { ?>
        <form action="/bweet" method="POST" id="doingForm">
          <script>
            function u(e) {
              if (e.value.length > e.maxLength) {
                e.value = e.value.substr(0, e.maxLength);
              }
              document.getElementById('length').innerText = e.maxLength - e.value.length;
            }
          </script>
          <textarea name="content" maxlength="300" id="" cols="30" rows="4" oninput="u(this);"><?=!isset($_GET["text"]) ? "" : htmlspecialchars(urldecode($_GET["text"]))?></textarea>
          <div class="bar">
            <b id="length">300</b>
            <span><input type="submit" value="Bweet"
                style="padding: 2px 5px;background:white;border:1px solid black;"></span>
          </div>
        </form>
        <?php } ?>
      </div>
    </div>
  </div>
</body>
<?php ?>

</html>