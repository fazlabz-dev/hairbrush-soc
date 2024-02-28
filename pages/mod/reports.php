<?php
foreach($_REPORTS as $u)
  if($u["resolved"] == 1) unset($u["resolved"]);

$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
$total = count($_REPORTS);
$limit = 10;
$totalPages = ceil($total / $limit);
$page = max($page, 1);
$page = min($page, $totalPages);
$offset = ($page - 1) * $limit;
if($offset < 0) $offset = 0;

$_REPORTS = array_slice($_REPORTS, $offset, $limit);
?><table style="width: 100%;">
  <thead>
    <tr>
      <th>Time</th>
      <th>Author</th>
      <th>Content</th>
      <th>Reported By</th>
      <th width="10">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($_REPORTS as $r) {
      if(!array_key_exists($r["bweet"], $_ATWEETS)) continue;
      $time = ($snowflake->parseId($r["id"], true)["timestamp"] + $unix)/1000;
      $t = $_ATWEETS[$r["bweet"]];
      $u = $_IDUSERS[$r["reporter"]];
    ?>
    <tr>
      <td><?=gmdate($adate, $time)?></td>
      <td><a href="/mod/user/<?=$t["user"]["id"]?>/"><?=$t["user"]["username"]?></a></td>
      <td><a href="/<?=$t["user"]["username"]?>/statuses/<?=$r["bweet"]?>"><?=$t["content"]?></a></td>
      <td><a href="/mod/user/<?=$u["id"]?>"><?=$u["username"]?></a></td>
      <td>
        <a title="Resolve" href="/mod/resolve/<?=$r["id"]?>"><i class="action fa fa-check"></i></a>
      </td>
    </tr>
    <?php } ?>
  </tbody>
</table>
<?php include "inc/paginator.php"; ?>