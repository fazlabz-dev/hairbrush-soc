<div id="side">
  <?php if(!isset($_PROFILE)) { ?>
  <?php if(!isset($_USER)) { ?>
  <div class="msg">
    <h3>Please Sign In!</h3>
  </div>
  <form method="post" class="signin" action="/sessions">
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

  <script type="text/javascript">
    document.getElementById('email').focus()
  </script>

  <div class="notify">
    Want an account?<br />
    <a href="/signup" class="join">Join for Free!</a><br />
    It&rsquo;s fast and easy!
  </div>
  <?php } else { ?>
  <div class="msg">
    <h3 style="margin: 0;padding: 0;">Welcome,</h3>
    <h1 style="margin: 0;padding: 0;"><a href="/<?=$_USER["username"]?>"><?=$_USER["username"]?></a></h1>
  </div>
  <p><b>Currently: </b> <i><?=count($_MYTWEETS) > 0 ? $_MYTWEETS[0]["content"] : "N/A"?></i></p>
  <ul class="featured">
    <li><strong>Latest Bweeters</strong></li>
    <?php $_OUSER = isset($_USER) ? $_USER : null; foreach($_LATEST as $_USER) { ?>
    <li>
      <a href="/<?=$_USER?>"><img alt="Logo" height="24" src="/account/profile_image/<?=$_USER?>.jpg" width="24" /></a>
      <a href="/<?=$_USER?>"><?=$_USER?></a>
    </li>
    <?php } $_USER = $_OUSER; ?>
  </ul>
  <?php } ?>
  <?php if(isset($_USER)) { ?>
  <form action="/sessions" method="post" style="display:inline;"><input type="submit" name="logout" value="Log Out"
      id="submit"></form>
  <?php } ?>
  <?php } else { ?>
  <div class="msg">
    <h3 style="margin: 0;padding: 0;">About</h3>
    <h1 style="margin: 0;padding: 0;"><?=$_PROFILE["username"]?></h1>
  </div>
  <p><b>Name: </b> <i><?=$_PROFILE["fullname"]?></i></p>

  <?php if(!empty($_PROFILE["bio"])) { ?>
  <p><b>Bio: </b> <i><?=$_PROFILE["bio"]?></i></p>
  <?php } ?>

  <?php if(!empty($_PROFILE["location"])) { ?>
  <p><b>Location: </b> <i><?=$_PROFILE["location"]?></i></p>
  <?php } ?>

  <?php if(!empty($_PROFILE["web"])) { ?>
  <p><b>Web: </b> <i><a href="<?=$_PROFILE["web"]?>"><?=$_PROFILE["web"]?></a></i></p>
  <?php } ?>

  <?php } ?>
</div>
<?php if(!isset($_USER) || ~$_USER["flags"] & TYPE_ADMIN) { ?>
<div id="side">
  <!-- these ads are not commercial, they are decorational -->
  <iframe width="160" height="160" style="border:none" src="https://fazlabz-dev.github.io/openlink/embed.html" name="neolink"></iframe>
</div>
<?php } ?>