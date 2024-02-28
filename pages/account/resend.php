<?php include_once "inc/main.php"; ?>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer();

// Settings
$mail->IsSMTP();
$mail->CharSet = 'UTF-8';

$mail->Host       = "smtp.porkbun.com";
$mail->SMTPDebug  = false;
$mail->SMTPAuth   = true;
$mail->Port       = 587;
$mail->Username   = "noreply@bwitter.me";
$mail->Password   = "$4N^5A*!TQfs";

$mail->setFrom('noreply@bwitter.me', 'Bwitter');

$mail->isHTML(true);
$mail->Subject = 'Password Reset';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $vars = array("email","h-captcha-response");
  $continue = true;
  foreach($vars as $var) {
    if(!isset($_POST[$var]) && ($_POST[$var] = trim($_POST[$var])) && !empty($_POST[$var])) {
      $continue = false;
      $err[] = "$var is not set.";
    } 
  }

  if(!verifyCaptcha($_POST['h-captcha-response'])) {
    $continue = false;
    $err = "Captcha failed.";
  }

  if($continue) {
    $email = $_POST["email"];
    if(isset($_EUSERS[$email])) {
      $u = $_EUSERS[$email];

			$mail->addAddress($email, $user);
			
			$key = base64_encode(md5($email)."|".md5($u["password"]));

      $mail->Body    = 'You have requested a password reset, if this was not done by you please ignore this email.<br>Otherwise, <a href="https://bwitter.me/account/reset?key='.$key.'">Click here</a> to reset your password.';
      $mail->send();

      $_SESSION["notice"] = "Password reset instructions sent.";
      die(header("Location: /"));
    } else {
      $_SESSION["error"] = "No user with the email \"$email\" found.";
      die(header("Location: /account/resend_password"));
    }
  } elseif($err) {
    $_SESSION["error"] = $err;
    die(header("Location: /account/resend_password"));
  } else {
    $_SESSION["error"] = $err;
    die(header("Location: /account/resend_password"));
  }
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
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
				<h2>Forgot?</h2>

				<p>Put in your email address below and we&rsquo;ll reset it for you.</p>

				<?php if(isset($_ERROR)) { ?>
					<p style="color: red;"><?=$_ERROR?></p>

				<?php } ?>
				<form action="/account/resend_password" method="post" name="f">
					<fieldset>
						<table>
							<tr>
								<th><label for="email">Email:</label></th>
								<td><input id="email" name="email" type="text" /></td>
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
					document.getElementById('email').focus();
				</script>
			</div>
			<hr />
			<hr />
			<?php include "inc/footer.php"; ?>
		</div>
</body>

</html>