<?php
include "inc/main.php";
if(isset($_USER)) {
  if(isset($_POST["logout"]))
    session_destroy();
  
  die(header("Location: /"));
}
function go() {
  if(isset($_POST["return"]))
    die(header("Location: ".$_POST["return"]));
  else
    die(header("Location: /"));
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if($_POST["logout"])
    die(header("Location: /"));
  $username_or_email = $_POST["username_or_email"];
  $password = $_POST["password"];

  $stmt = $db->prepare("SELECT * FROM `users` WHERE `username` = ? OR `email` = ?");
  if($stmt === false) die(mysqli_error($db));
  $stmt->bind_param("ss", $username_or_email, $username_or_email);
  $success = $stmt->execute();
  if($success === false) die(mysqli_error($db));
  $res = $stmt->get_result();
  while($row = $res->fetch_array()) {
    if(strlen($row["password"]) == 128) {
      for($i = 0; $i < 500; $i++) {
        $alt = md5($password);
        $password = hash("sha512", $alt.$password.$alt);
      }
      $check = $password == $row["password"];
    } else {
      $check = password_verify($password, $row["password"]);
    }
    if($check) {
      if($row["flags"] & TYPE_SUSPENDED) $_SESSION["notice"] = "Account Suspended.";
      else $_SESSION["uid"] = $row["id"];
      go();
    } else {
      $_SESSION["notice"] = "Invalid Username/Password.";
      die(header("Location: /"));
    }
  }
  $_SESSION["notice"] = "Invalid Username/Password.";
  die(header("Location: /"));
} else die(header("Location: /"));
?>