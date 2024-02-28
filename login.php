<?php include "inc/main.php"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<?php include "inc/head.php"; ?>

<body class="sessions" id="new">
  <div id="container" class="subpage">
    <?php include "inc/header.php"; ?>
    <div id="content">
      <div class="wrapper">
        <h2>Sign in to Bwitter</h2>

        <form action="/sessions" method="post">
          <?php if(isset($_GET["return"])) ?>
          <input type="hidden" name="return" value="<?=htmlspecialchars($_GET["return"])?>">
          <?php ?>
          <fieldset>
            <table cellspacing="0">
              <tr>
                <th><label for="username_or_email">Username or Email</label></th>
                <td><input id="username_or_email" name="username_or_email" type="text" /></td>
              </tr>
              <tr>
                <th><label for="password">Password</label></th>
                <td><input id="password" name="password" type="password" /> <small><a
                      href="/account/resend_password">Forgot?</a></small></td>
              </tr>
              <tr>
                <th></th>
                <td><input id="remember_me" name="remember_me" type="checkbox" value="1" /> <label for="remember_me"
                    class="inline">Remember me</label></td>
              </tr>
              <tr>
                <th></th>
                <td><input name="commit" type="submit" value="Sign In" /></td>
              </tr>
            </table>
          </fieldset>
        </form>

        <script type="text/javascript">
          document.getElementById('username_or_email').focus();
        </script>
      </div>
    </div>
    <hr />
    <div id="side">
      <div class="notify">
        Want an account?<br />
        <a href="/signup" class="join">Join for Free!</a><br />
        Have an account? <a href="/login">Sign in!</a>
      </div>
    </div>
    <hr />
    <hr />
    <?php include "inc/footer.php"; ?>
  </div>
</body>

</html>