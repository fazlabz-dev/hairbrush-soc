<h1 id="header">
  <a href="/" title="Home" accesskey="1" style="text-decoration: none;">
    <img alt="Hairbrush Social" src="/images/hairbrushlogo.png" style="max-width: 45%" />
  </a>
  <?php if(isset($_USER) && ($_USER["flags"] & TYPE_MODERATOR || $_USER["flags"] & TYPE_ADMIN)) { ?>
  <ul class="nav">
    <?php if($_USER["flags"] & TYPE_ADMIN) { ?>
    <li><a href="/admin">Admin CP</a></li>
    <?php } ?>
    <li><a href="/mod">Mod CP</a>
      <?php if(count($_REPORTS) > 0) { ?>
      <small class="b"><?=count($_REPORTS)?></small>
      <?php } ?>
    </li>
  </ul>
  <?php } ?>
</h1>