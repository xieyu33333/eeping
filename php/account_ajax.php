<?php
require 'conn.php';
require 'global_fun.php';

session_start();

$type = $_POST['type'];

if ($type != 'checkMail' && $type != 'checkName' && $type != 'checkDomain') {
  if (!isset($_SESSION['auth_token']) || !isset($_POST['auth']) || $_POST['auth'] != $_SESSION['auth_token']) exit();
}

switch ($type) {
  case 'checkMail':
    $foo = $_POST['mail'];
    echo json_encode(mysql_num_rows(mysql_query("SELECT `uid` FROM `ceedb`.`user`
     WHERE `mail` = '$foo' LIMIT 1")));
    break;

  case 'checkName':
    $foo = $_POST['name'];
    echo json_encode(mysql_num_rows(mysql_query("SELECT `uid` FROM `ceedb`.`user`
     WHERE `name` = '$foo' LIMIT 1")));
    break;

  case 'checkDomain':
    $foo = $_POST['domain'];
    echo json_encode(mysql_num_rows(mysql_query("SELECT `uid` FROM `ceedb`.`user`
     WHERE `domain` = '$foo' LIMIT 1")));
    break;

  case 'basic':
    $uid = s2n($_POST['uid']);
    $name = $_POST['name'];
    $domain = $_POST['domain'];
    $mail = $_POST['mail'];
    outputDbError(mysql_query("UPDATE `ceedb`.`user`
     SET `name` = '$name', `domain` = '$domain', `mail` = '$mail' WHERE `uid` = '$uid'"));
    $_SESSION['name'] = $name;
    if ($domain != '') $_SESSION['domain'] = $domain;
    $_SESSION['mail'] = $mail;
    setcookie('mail', $mail, time() + 14*24*3600, '/');
    echo json_encode(array(1, ''));
    break;

  case 'security':
    $uid = s2n($_POST['uid']);
    $oldpwd = hash('sha1', $_POST['oldpwd']);
    if (!mysql_num_rows(mysql_query("SELECT `uid` FROM `ceedb`.`user` WHERE `uid` = '$uid' AND `password` = '$oldpwd'"))) {
      echo json_encode(array(0, '密码错误'));
    } else {
      $hash = hash('sha1', $_POST['password']);
      outputDbError(mysql_query("UPDATE `ceedb`.`user` SET `password` = '$hash' WHERE `uid` = '$uid'"));
      setcookie('security', $hash, time() + 14*24*3600, '/');
      echo json_encode(array(1, ''));
    }
    break;

  case 'uploadPhoto':
    $uid = $_POST['uid'];
    if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
      $dest = '../upload/' . $uid . '.png';
      $image_data = file_get_contents($_FILES['image']['tmp_name']);
      $src_image = imagecreatefromstring($image_data);
      $src_w = imagesx($src_image);
      $src_h = imagesy($src_image);
      $ratio = $src_w / $src_h;
      if ($src_w < 96 || $src_h <96) {
        echo json_encode(array(0, '图片要求必须不小于96×96像素'));
      } else if ($ratio < 0.5 || $ratio > 2) {
        echo json_encode(array(0, '请上传宽高比适当的图片'));
      } else {
        $dst_h = 280;
        $dst_w = round($dst_h * $ratio);
        $dst_image = imagecreatetruecolor($dst_w, $dst_h);
        imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
        imagepng($dst_image, $dest);
        imagedestroy($dst_image);
        echo json_encode(array(1, $dest));
      }
    } else {
      echo json_encode(array(0, '上传失败'));
    }
    break;

  case 'savePhoto':
    $uid = $_POST['uid'];
    $dest = '../user/' . $uid . '/' . $uid . '.png';
    $image_data = file_get_contents('../upload/' . $uid . '.png');
    $src_image = imagecreatefromstring($image_data);
    $dst_image = imagecreatetruecolor(96, 96);
    imagecopyresampled($dst_image, $src_image, 0, 0, $_POST['x'], $_POST['y'], 96, 96, $_POST['w'], $_POST['h']);
    imagepng($dst_image, $dest);
    imagedestroy($dst_image);
    unlink('../upload/' . $uid . '.png');
    $uid = s2n($uid);
    outputDbError(mysql_query("UPDATE `ceedb`.`user` SET `avatar` = '$dest' WHERE `uid` = '$uid'"));
    $_SESSION['avatar'] = $dest;
    echo json_encode(array(1, $dest));
    break;
}
?>