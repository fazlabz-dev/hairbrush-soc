<?php
include "inc/main.php";

$openSignups = false;
$reasonWhyNoSignups = "Hairbrush Social is in development. To signup for beta testing, please <a href=\"https://forms.gle/JAa9FsUGZ2nv4suZ8\">use this form</a>.";

$blacklistednames = array(
  "account" => true,
  "assets" => true,
  "admin" => true,
  "mod" => true,
  "images" => true,
  "inc" => true,
  "vendor" => true,
  "index" => true,
  "err" => true,
  "edit" => true,
  "bweet" => true,
  "login" => true,
  "pages" => true,
  "sessions" => true,
  "signup" => true,
  "resend" => true,
  "help" => true,
  "0" => true,
  "share" => true,
  "nanoblog" => true,
  "twtxt" => true,
  "share_button" => true,
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $vars = array("fullname", "email", "username", "time_zone", "password", "h-captcha-response");
  $continue = true;
  foreach($vars as $var) {
    if(!isset($_POST[$var]) && ($_POST[$var] = trim($_POST[$var])) && !empty($_POST[$var])) {
      $continue = false;
      $err = "$var is not set.";
    } 
  }

  $email = $_POST["email"];
  $username = $_POST["username"];

  if(isset($_USERS[strtolower($username)])) {
    $err = "Username taken.";
    $continue = false;
  } elseif(isset($_USERS[strtolower($email)])) {
    $err = "Email taken.";
    $continue = false;
  }
  
  if(!verifyCaptcha($_POST['h-captcha-response'])) {
    $continue = false;
    $err = "Captcha failed. (".json_encode($response).")";
  }

  $img = true;
  if(!isset($_FILES["profile_image"]) || !file_exists($_FILES["profile_image"]["tmp_name"])) {
    $img = false;
  } else {
    $file = $_FILES["profile_image"]["tmp_name"];
    if((mime_content_type($file) !== "image/jpeg" && mime_content_type($file) !== "image/png") || getimagesize($file) === false) {
      $continue = false;
      $err = "Invalid Image.";
    }
  }

if($img) {	
  list($width, $height) = getimagesize($file);
  $src = imagecreatefromstring(file_get_contents($file));
  $dst = imagecreatetruecolor(64, 64);
  imagecopyresampled($dst, $src, 0, 0, 0, 0, 64, 64, $width, $height);
  imagedestroy($src);
  $filepath = pathinfo($file, PATHINFO_DIRNAME);
  $filename = pathinfo($file, PATHINFO_FILENAME).".jpg";
  $file = $filepath.DIRECTORY_SEPARATOR.$filename;
  if(file_exists($file)) unlink($file);
  imagejpeg($dst, $file);
  imagedestroy($dst);
}
	
  if($continue) {
    $stmt = $db->prepare("INSERT INTO `users` (`fullname`, `email`, `username`, `timezone`, `password`) VALUES (?, ?, ?, ?, ?)");
    $fullname = htmlspecialchars($_POST["fullname"]);
    $timezone = $_POST["time_zone"];
    $password = $_POST["password"];
    $avatarxd = $_FILES["profile_image"];
    if($password != $_POST["password_confirmation"]) {
      $err = "Passwords don't match.";
    } elseif (strlen($password) < 6) {
      $err = "Password too short.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $err = "Invalid e-mail.";
    } elseif (!preg_match("/^[a-zA-Z0-9_]{1,20}$/", $username) || isset($blacklistednames[$username])) {
      $err = "Only alphanumerical characters and underscores allowed in username.";
    } elseif (!preg_match("/^[a-zA-Z0-9-\. ]{1,64}$/", $fullname)) {
      $err = "Only alphanumerical characters, spaces and hyphens allowed in name.";
    } else {
      $password = password_hash($password, PASSWORD_DEFAULT);
      $stmt->bind_param("sssis", $fullname, $email, $username, $timezone, $password);
      $stmt->execute();
      if($_FILES["profile_image"]["tmp_name"] && $img) move_uploaded_file($_FILES["profile_image"]["tmp_name"], "account/profile_image/$username.jpg");
      if($stmt->error) $err = $stmt->error; else {
        $_SESSION["notice"] = "Successfully registered!";
        $_SESSION["uid"] = $stmt->insert_id;
        die(header("Location: /"));
      }
    }
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<?php include "inc/head.php"; ?>

<body class="account" id="create">
  <style type="text/css">
    a {
      color: #0000ff;
    }

    #side {
      background-color: #e0ff92;
      border: 1px solid #87bc44;
    }

    #side .notify {
      border: 1px solid #87bc44;
    }

    #side .actions {
      border: 1px solid #87bc44;
    }

    h2.thumb,
    h2.thumb a {
      color: #000000;
    }
  </style>

  <div id="container" class="subpage">
    <?php include "inc/header.php"; ?>
    <div id="content">
      <div class="wrapper">
        <h2>Join Hairbrush!</h2>
        <?php if(isset($err)) { ?>
        <p style="color: red;"><?=$err?></p>
        <?php }
        if ($openSignups) { ?>
        <form action="/signup" enctype="multipart/form-data" method="post" name="f">
          <fieldset>
            <table cellspacing="0">
              <tr>
                <th><label for="user_name">Full Name:</label></th>
                <td><input id="user_name" name="fullname" size="30" type="text" value="" /></td>
              </tr>
              <tr>
                <th><label for="user_username">Create Username:</label></th>
                <td><input id="user_screen_name" name="username" size="30" type="text" value="" /> <small>For signing in
                    to Hairbrush (no spaces allowed!)</small></td>
              </tr>
              <tr>
                <th><label for="user_password">Create Password:</label></th>
                <td><input id="password" name="password" type="password" /> <small>Six characters or more
                    (be tricky!)</small></td>
              </tr>
              <tr>
                <th><label for="password_password_confirmation">Retype Password:</label></th>
                <td><input id="password_password_confirmation" name="password_confirmation" size="30" type="password" />
                </td>
              </tr>
              <tr>
                <th><label for="user_email">Email Address:</label></th>
                <td><input id="user_email" name="email" size="30" type="text" /> <small>In case you forget your
                    password!</small></td>
              </tr>
              <tr>
                <th><label for="user_time_zone">Time Zone:</label></th>
                <td><select id="user_time_zone" name="time_zone">
                    <?php foreach($_TIMEZONES as $_TIMEZONE) { ?>
                    <option value="<?=$_TIMEZONE["id"]?>"><?=$_TIMEZONE["name"]?></option>
                    <?php } ?>
                  </select></td>
              </tr>
              <tr>
                <th>
                  <label for="user_profile_image">
                    Picture:
                  </label>
                </th>
                <td>
                  <input id="user_profile_image_temp" name="profile_image_temp" type="hidden" /><input
                    id="user_profile_image" name="profile_image" size="30" type="file" />
                  <p><small>(Optional) JPEGs only!<br> If you don&rsquo;t include a picture, it&rsquo;ll be set to the
                      default avatar.</small></p>
                </td>
              </tr>
              <tr>
                <th></th>
                <td>
                  <p>By joining Hairbrush, you confirm that you are over 13 years of age and accept the <a href="/tos"
                      target="_blank">Terms of Service</a>.</p>
                </td>
              </tr>
							<tr>
								<th><label for="captcha">Prove you're human:</label></th>
								<td><div id="hcaptcha-demo" class="h-captcha" data-sitekey="7269be27-52c3-496d-99bc-8f30cf6a31ee"></div></td>
							</tr>
              <tr>
                <th></th>
                <td><button>Continue</button></td>
               <script src="https://hcaptcha.com/1/api.js?reportapi=https%3A%2F%2Faccounts.hcaptcha.com" type="text/javascript" async defer></script>
              </tr>
            </table>
          </fieldset>
        </form>
        <script type="text/javascript">
          document.getElementById('user_name').focus()
        </script>
        <?php } else { ?>
          We're sorry. But this Hairbrush instance doesn't accept signups for the following reason:
          <br>
          <blockquote>
            <?= $reasonWhyNoSignups ?>
          </blockquote>
        <?php } ?>
      </div>
    </div>
    <hr>

    <div id="side">
      <div class="msg">
        <h3>Already a member? Please Sign In!</h3>
      </div>

      <form action="/sessions" class="signin" method="post">
        <fieldset>
          <div>
            <label for="username_or_email">Username or Email</label>
            <input id="email" name="username_or_email" type="text" />
          </div>
          <div>
            <label for="password">Password</label>
            <input id="pass" name="password" type="password" />
          </div>
          <input id="remember_me" name="remember_me" type="checkbox" value="1" /> <label for="remember_me">Remember
            me</label>
          <small><a href="/account/resend_password">Forgot?</a></small>
          <input id="submit" name="commit" type="submit" value="Sign In!" />
        </fieldset>
      </form>
    </div>
    <hr />


    <hr />
    <?php include "inc/footer.php"; ?>
  </div>
</body>

</html>
