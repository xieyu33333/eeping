<?php
require './php/conn.php';
require './php/global.php';

if (isset($_POST['signup'])) {

  $mail = $_POST['mail'];
  $hash = hash('sha1', $_POST['password']);
  $name = $_POST['name'];
  $time = date('Y-m-d H:i:s');
  $avatar = '/img/default_avatar.gif';

  mysql_query("INSERT INTO `ceedb`.`user` (`uid`, `name`, `signup_time`, `mail`, `password`, `avatar`)
   VALUES (NULL, '$name', '$time', '$mail', '$hash', '$avatar')");

  mkdir('./user/' . n2s(mysql_insert_id()));

  setcookie('mail', $mail, time() + 7*24*3600, '/');
  setcookie('security', $hash, time() + 7*24*3600, '/');

  header('Location:' . (isset($_SESSION['redir']) ? $_SESSION['redir'] : '/'));

} else if (isset($_COOKIE['mail']) && isset($_COOKIE['security'])) {
  header('Location:' . (isset($_SESSION['redir']) ? $_SESSION['redir'] : '/'));
}
?>
<!DOCTYPE html>
<html>
<head>
<title>注册 - 艺评</title>
<link type="text/css" rel="stylesheet" href="/css/global.css">
<style>
.main {
  float: left;
  width: 65%;
  padding-top: 40px;
}
</style>
</head>

<body>
  <header class="navbar navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">
        <a class="brand" href="/">Eeping</a>
        <ul class="nav">
          <li>
            <a href="/">首页</a>
          </li>
          <li>
            <a href="#">热门</a>
          </li>
          <li>
            <a href="#">NEW!</a>
          </li>
          <li>
            <a href="product/igGblEmv">Product</a>
          </li>
        </ul>
        <ul class="nav pull-right">
          <li class="divider-vertical"></li>
          <li>
            <a data-show="modal" href="#signin">登录</a>
          </li>
          <li class="active">
            <a href="#">注册</a>
          </li>
        </ul>
        <form class="navbar-search pull-right" action="#">
          <input class="search-query" type="text" name="key" placeholder="搜索" maxlength="20">
          <input class="navbar-search-mag" type="submit"  name="search" value="">
        </form>
      </div>
    </div>
  </header>
  <div class="container">
    <div class="main">
      <h2>您可以直接注册或选择右方合作网站帐号登录</h2>
      <hr/>
      <form class="form-horizontal" action="/signup" name="signup-form" method="post">
        <div class="control-group">
          <label class="control-label" for="mail">邮箱</label>
          <div class="controls">
            <input class="input-medium" type="text" name="mail" maxlength="32" tabindex="1">
            <div class="help-inline warning"></div>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="password">密码</label>
          <div class="controls">
            <input class="input-medium" type="password" name="password" maxlength="32" tabindex="2">
            <div class="help-inline warning"></div>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="repassword">重复密码</label>
          <div class="controls">
            <input class="input-medium" type="password" name="repassword" maxlength="32" tabindex="3">
            <div class="help-inline warning"></div>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="name">昵称</label>
          <div class="controls">
            <input class="input-medium" type="text" name="name" maxlength="14" tabindex="4">
            <div class="help-inline warning"></div>
          </div>
        </div>
        <div class="form-actions">
          <input class="btn btn-primary btn-large" type="submit" name="signup" value="注册" tabindex="5">
        </div>
      </form>
    </div>
  </div>
  <?php echoSignin(); ?>
  <script type="text/javascript" src="http://code.jquery.com/jquery.js"></script>
  <script type="text/javascript" src="/js/global.js"></script>
  <script type="text/javascript" src="/js/global_input_helper.js"></script>
  <script>
  $("form[name='signup-form']").submit(function () {
    $("input[name='name']").blur()
    if (bMail && bPass && bRepass && bName) {
      return
    } else {
      return false
    }
  })
  </script>
</body>
</html>