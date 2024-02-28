<?php include_once "inc/main.php";
$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
$total = count($_ATWEETS);
$limit = 20;
$totalPages = ceil($total/ $limit);
$page = max($page, 1);
$page = min($page, $totalPages);
$offset = ($page - 1) * $limit;
if($offset < 0) $offset = 0;

$_STWEETS = array_slice($_ATWEETS, $offset, $limit);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<?php include "inc/head.php"; ?>

<body class="account" id="front">
  <div id="container" class="subpage">
    <?php include "inc/header.php"; ?>
    <div id="content">
      <div class="wrapper">
        <h2>A global community of friends and strangers answering one simple question: <em>What are you doing?</em>
          Answer on your phone, IM, or right here on the web!</h2>
        <?php if(!empty($_NOTICE)) { ?>
        <div
          style="border: 5px solid #87BC44; background: white; font-weight: bold; padding: 10px; margin:10px; font-size:1.1em">
          <?=parse_bweet($_NOTICE)?></div>
        <?php } ?>
        <?php if(isset($_USER)) { ?>
        <form action="/bweet" method="POST" id="doingForm">
          <h1 class="info">What are you doing?</h1>
          <script>
            function u(e) {
              if (e.value.length > e.maxLength) {
                e.value = e.value.substr(0, e.maxLength);
              }
              document.getElementById('length').innerText = e.maxLength - e.value.length;
            }
          </script>
          <textarea name="content" maxlength="300" id="" cols="30" rows="4" oninput="u(this);"></textarea>
          <div class="bar">
            <b id="length">300</b>
            <span><input type="submit" value="Bweet"
                style="padding: 2px 5px;background:white;border:1px solid black;"></span>
          </div>
        </form>
        <?php } ?>

        <h3>Look at what these people are doing right now&hellip;</h3>
        <table class="doing" id="timeline" cellspacing="0">
          <?php foreach($_STWEETS as $_STATUS) { render_bweet($_STATUS); } ?>
        </table>
        <?php include "inc/paginator.php"; ?>
      </div>
    </div>
    <hr />
    <?php include "inc/side.php"; ?>
    <hr />
    <hr />
    <?php include "inc/footer.php"; ?>
  </div>
</body>
<?php ?>

</html>