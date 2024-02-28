<?php
if(!isset($args[2])) return;
if(!array_key_exists($args[2], $_IDUSERS)) {
  $args[2] = strtolower($args[2]);
  if(array_key_exists($args[2], $_USERS)) {
    $u = $_USERS[$args[2]];
  } else {
    return;
  }
} else {
  $u = $_IDUSERS[$args[2]];
}
?>
<table style="width: 100%;">
  <thead>
    <tr>
      <th style="width: 0px;">User ID</th>
      <td><?=$u["id"]?></td>
    </tr>
    <tr>
      <th style="width: 0px;">Username</th>
      <td><?=$u["username"]?></td>
    </tr>
    <tr>
      <th style="width: 0px;">Full Name</th>
      <td><?=$u["fullname"]?></td>
    </tr>
    <tr>
      <th style="width: 0px;">Email</th>
      <td><?=$u["email"]?></td>
    </tr>
    <tr>
      <th style="width: 0px;">Flags&nbsp;(Click&nbsp;to&nbsp;Toggle)</th>
      <td><?php foreach($_FLAGS as $flag => $text) {
          if($flag == TYPE_ADMIN && ~$_USER["flags"] & TYPE_ADMIN) continue;
          echo '<a ';
          switch($flag) {
            case TYPE_SUSPENDED:
                echo 'href="/mod/suspend/'.$u["id"].'" ';
              break;
            case TYPE_VERIFIED:
                echo 'href="/mod/verify/'.$u["id"].'" ';
              break;
            case TYPE_MODERATOR:
                echo 'href="/admin/mod/'.$u["id"].'" ';
              break;
            default:
                break;
          }
          echo 'style="color: '.(($u["flags"] & $flag) ? "green" : "red").';">'.$text.'</a> ';
       } ?></td>
    </tr>
    <tr>
      <th style="width: 0px;">Current&nbsp;Restrictions</th>
      <td><?php
        foreach($_RESTRICTIONS as $flag => $text) {
          echo '<a style="color: '.(is_restricted($u, $flag) ? "green" : "red").';">'.$text.'</a> ';
        } ?></td>
    </tr>
    <tr>
      <th style="width: 0px;">Restrict&nbsp;Access</th>
      <td>
        <form action="/mod/restrict/<?=$u["id"]?>" method="POST">
        <select name="torestrict" ><?php
        foreach($_RESTRICTIONS as $flag => $text) {
          echo '<option value="';
          echo strtolower($flag);
          echo '">';
          echo $text;
          echo '</option>';
        } ?></select>
        <br>
        <input type="number" name="time[days]" id="days" min="0" value="0" max="364" style="width:3em">
        <label for="days">Days</label>
        <input type="number" name="time[hours]" id="hours" min="0" value="1" max="24" style="width:2em">
        <label for="days">Hours</label>
        <br>
        <input type="submit" id="submit" name="restrict" style="float: left;" value="Restrict">&nbsp;
        <input type="submit" id="submit" name="unrestrict" style="float: left;" value="Unrestrict">
        </form>
      </td>
    </tr>
  </thead>
  <tbody>
  </tbody>
</table>