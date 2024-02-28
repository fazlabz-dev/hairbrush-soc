<?php
foreach($_USERS as $u)
  if(!($_USER["flags"] & TYPE_ADMIN) && $u["flags"] & TYPE_ADMIN || $u["flags"] & TYPE_MODERATOR) unset($_USERS[$u["username"]]);

$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
$total = count($_USERS);
if(!isset($_GET["nolimit"])){ //A simple thing
  $limit = 10;
  $totalPages = ceil($total / $limit);
  $page = max($page, 1);
  $page = min($page, $totalPages);
  $offset = ($page - 1) * $limit;
  if($offset < 0) $offset = 0;
  $_USERS = array_slice($_USERS, $offset, $limit);
}

?>
<?php if(!isset($_GET["nolimit"])) { ?>
<a href="?nolimit" style="float:right;">No Limit</a>
<?php } ?>
<table style="width: 100%;">
  <thead>
    <tr>
      <th>ID</th>
      <th>Full Name</th>
      <th>Username</th>
      <th>E-Mail</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($_USERS as $u) {
  $s = $u["flags"] & TYPE_SUSPENDED;
  $v = $u["flags"] & TYPE_VERIFIED;
  $m = $u["flags"] & TYPE_MODERATOR;
?>
    <tr>
      <td width="20"><?=$u["id"]?></td>
      <td><?=$u["fullname"]?></td>
      <td><a href="/<?=$u["username"]?>"><?=$u["username"]?></a></td>
      <td><?=$u["email"]?></td>
      <td width="110">
        <a title="manage" href="/mod/user/<?=$u["id"]?>">Manage</a>
      </td>
    </tr>
    <?php } ?>
  </tbody>
</table>
<?php if(!isset($_GET["nolimit"])) include "inc/paginator.php"; ?>