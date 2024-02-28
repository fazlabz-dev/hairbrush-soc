<?php include_once "inc/main.php"; ?>
<?php
if(isset($_REQUEST["key"])) {
  $key = $_REQUEST["key"];
  $data = explode("|", base64_decode($key));
  if(count($data) != 2) die(header("Location: /account/resend_password"));
  $md5 = "/^[a-f0-9]{32}$/";
  if(!preg_match($md5, $data[0]) || !preg_match($md5, $data[1])) die(header("Location: /account/resend_password"));
  $t = null;
  foreach($_USERS as $u) {
    if(md5($u["email"]) == $data[0]) {
      $t = $u;
    }
  }
  if(md5($t["password"]) != $data[1]) die(header("Location: /account/resend_password"));
  
  if(isset($t)) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if(isset($_POST["password"]) && isset($_POST["cpassword"])) {
        if($_POST["password"] != $_POST["cpassword"]) {
          $_ERROR = "Passwords don't match.";
        } else {
          $hpassword = password_hash($_POST["password"], PASSWORD_DEFAULT);
          $stmt = $db->prepare("UPDATE `users` SET `password` = ? WHERE `id` = ?");
          $stmt->bind_param("si", $hpassword, $uid);
          $stmt->execute();

          $_SESSION["notice"] = "Password changed.";
          die(header("Location: /"));
        }
      }
    } ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<?php include "inc/head.php"; ?>

<body class="account" id="front">
  <div id="container" class="subpage">
    <h1 id="header">
      <a href="/" title="Bitter: home" accesskey="1">
        <img alt="Bitter.com" height="49" src="/images/twitter.png" width="210" />
      </a>
    </h1>


    <div id="content">
      <div class="wrapper">
        <h2>Reset Password</h2>

        <p>Please enter your new password.</p>

        <?php if(isset($_ERROR)) { ?><p style="color: red;"><?=$_ERROR?></p><?php } ?>
        <form action="/account/reset" method="post" name="f">
          <fieldset>
            <table>
              <tr>
                <th><label for="password">New Password:</label></th>
                <td><input id="password" name="password" type="password" /></td>
              </tr>
              <tr>
                <th><label for="cpassword">Confirm Password:</label></th>
                <td><input id="cpassword" name="cpassword" type="password" /></td>
              </tr>
              <tr>
                <th><input type="hidden" name="key" value="<?=$_REQUEST["key"]?>"></th>
                <td><button>Continue</button></td>
                <script src="https://hcaptcha.com/1/api.js?reportapi=https%3A%2F%2Faccounts.hcaptcha.com"
                  type="text/javascript" async defer></script>
              </tr>
            </table>
          </fieldset>
        </form>

        <script type="text/javascript">
          document.getElementById('email').focus();
        </script>
      </div>
      <hr />
      <hr />
      <?php include "inc/footer.php"; ?>
    </div>
</body>

</html><?php } else die(header("Location: /account/resend_password"));
} else {
  die(header("Location: /account/resend_password"));
}
?>