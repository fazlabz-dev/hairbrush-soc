<?php include_once "inc/main.php";
$videos = array(
  "FGSFM5-5D9D5L-YZQLAF-RFXAPK" => "BsIa_LKojJI",
);

foreach($videos as $video => $vid) {
  if(file_exists("assets/".$videos[$video].".mp4")) continue;
  parse_str(file_get_contents("http://youtube.com/get_video_info?video_id=" . $vid), $info);

  // probably doesnt work
  $videoData = json_decode($info['player_response'], true);
  $videoDetails = $videoData['videoDetails'];
  $streamingData = $videoData['streamingData'];
  $streamingDataFormats = $streamingData['formats'];
  $video_title = $videoDetails["title"];
  file_put_contents("assets/".$videos[$video].".mp4", file_get_contents($streamingDataFormats[1]['url']));
}

unset($video);

if($_SERVER['REQUEST_METHOD'] === 'POST') {
	if(isset($_POST["code"])) {
		switch($_POST["code"]) {
			case "mac":
				die(header("Location: https://bitview.net/profile.php?user=mac"));
			default:
        if(array_key_exists($_POST["code"], $videos))
          $video = $videos[$_POST["code"]];
        else
          $_ERROR = "Invalid code.";
				break;
		}
	} else {
		$_ERROR = "Invalid code.";
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<?php include "inc/head.php"; ?>

<body class="account" id="front">
  <div id="container" class="subpage">
    <?php include "inc/header.php"; ?>
    <div id="content">
      <div class="wrapper">
        <?php if(!isset($video)) { ?>
        <h2>Redeem Code</h2>

        <p>Enter the code you received and press &ldquo;redeem&rdquo; to use it.</p>

        <?php if(isset($_ERROR)) { ?><p style="color: red;"><?=$_ERROR?></p><?php } ?>
        <form action="/account/redeem" method="post" name="f">
          <fieldset>
            <table>
              <tr>
                <th><label for="code">Code:</label></th>
                <td><input id="code" name="code" placeholder="XXXXXX-XXXXXX-XXXXXX-XXXXXX" style="width: 227px;"
                    type="text" /></td>
              </tr>
              <tr>
                <th></th>
                <td><input type="submit" id="submit" value="Redeem Code"></td>
                <script src="https://hcaptcha.com/1/api.js?reportapi=https%3A%2F%2Faccounts.hcaptcha.com"
                  type="text/javascript" async defer></script>
              </tr>
            </table>
          </fieldset>
        </form>

        <script type="text/javascript">
          document.getElementById('code').focus();
        </script>
        <?php } else { ?>
        <form action="" onsubmit="claim();return false">
          <h2>Redeem Code</h2>
          <p>Code entered: <code><?=$_POST["code"]?></code>.</p>
          <p>Press the button below to activate it.</p>
          <input type="submit" id="submit" value="Redeem Code" style="margin:0;">
        </form>

        <div id="videocontainer" style="display:none;">
          <h2>You've been trolled</h2>
          <p>There isn't actually a code system yet.</p>
          <p>Have a nice day :3</p>
          <div style="z-index: 100;position: absolute;width:535px;height:301px;"></div>
          <video id="video" style="z-index:0;" loop src="/assets/<?=$video?>.mp4" width="535" height="301"></video>
        </div>

        <script>
          function claim() {
            document.forms[0].remove();
            var c = document.getElementById('videocontainer');
            c.style.display = "";
            var v = document.getElementById('video');
            v.play();
          }
        </script>
        <?php } ?>
      </div>
    </div>
    <hr />
    <hr />
    <?php include "inc/footer.php"; ?>
  </div>
</body>

</html>