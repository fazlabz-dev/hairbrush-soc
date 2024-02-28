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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $vars = array("name","email","subject","h-captcha-response");
  $continue = true;
  foreach($vars as $var) {
    if(!isset($_POST[$var]) || ($_POST[$var] = trim($_POST[$var])) && empty($_POST[$var])) {
      $continue = false;
      $err = "$var is not set.";
    }
  }

  if(isset($_USER)) {
    $_POST["name"] = $_USER["username"];
    $_POST["email"] = $_USER["email"];
  }

  $mail->setFrom('noreply@bwitter.me', 'Bwitter');
  $mail->AddReplyTo($_POST["email"], $_POST["name"]);
  
  $mail->isHTML(false);
  $mail->Subject = $_POST["name"].": ".$_POST["subject"];

  if(!VerifyCaptcha($_POST['h-captcha-response'])) {
    $continue = false;
    if(!isset($err)) $err = "Captcha failed.";
  }

  if($continue) {
    $email = $_POST["email"];

    $mail->addAddress("contact@bwitter.me", "Site Admin");
    $mail->Body = $_POST["message"];
    $mail->send();

    $_SESSION["notice"] = "Request sent.";
    die(header("Location: /"));
  } else {
    $_ERROR = $err;
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
				<h2>Contact</h2>

				<p>Put in your email address below and we&rsquo;ll get in touch.</p>
				<?php if(isset($_ERROR)) { ?><p style="color: red;"><?=$_ERROR?></p><?php } ?>
				<form action="/help/contact" method="post">
					<fieldset>
						<table>
							<tr>
								<th><label for="name">Name:</label></th>
								<td><input id="name" name="name" type="text" value="<?=isset($_USER) ? $_USER["fullname"].'" readonly="' : ""?>" /></td>
							</tr>
							<tr>
								<th><label for="email">Email:</label></th>
								<td><input id="email" name="email" type="email" value="<?=isset($_USER) ? $_USER["email"].'" readonly="' : ""?>" /></td>
							</tr>
							<tr>
								<th><label for="subject">Subject:</label></th>
								<td><input id="subject" name="subject" type="text" /></td>
							</tr>
							<tr>
								<th><label for="message">Message:</label></th>
								<td><textarea id="message" name="message" type="text" rows="4" style="resize: none;"></textarea></td>
							</tr>
							<tr>
								<th><label for="captcha">Prove you're human:</label></th>
								<td><div id="hcaptcha-demo" class="h-captcha" data-sitekey="7269be27-52c3-496d-99bc-8f30cf6a31ee"></div></td>
							</tr>
							<tr>
								<th></th>
                <td><input type="submit" value="Send" id="submit"></td>
								<script src="https://hcaptcha.com/1/api.js?reportapi=https%3A%2F%2Faccounts.hcaptcha.com" type="text/javascript" async defer></script>
							</tr>
						</table>
					</fieldset>
				</form>
      </div>
    </div>
    <hr />
    <?php include "inc/side.php"; ?>
    <hr />
    <hr />
    <?php include "inc/footer.php"; ?>
  </div>
</body>

</html>