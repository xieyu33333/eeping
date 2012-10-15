<?php
require 'global_fun.php';

$self = $_SERVER['REQUEST_URI'];

function generateAuth($suffix = 6) {
  $index = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $base  = strlen($index);
  $out = '';
  for ($i = 0; $i < $suffix; $i++) {
    $out .= substr($index, mt_rand(0, $base -1), 1);
  }
  return $out;
}

function echoSignin() {
  global $self;
  if (!isset($_SESSION['auth_token'])) {
?>
<div class="modal" id="signin">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3>登录</h3>
  </div>
  <form class="form-horizontal modal-form" action="<?php echo $self; ?>" method="post">
    <div class="control-group">
      <label class="control-label" for="mail">邮 箱</label>
      <div class="controls">
        <input class="input-medium" id="mail" type="text" name="mail" maxlength="50" autofocus="autofocus" tabindex="1">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="password">密 码</label>
      <div class="controls">
        <input class="input-medium" id="password" type="password" name="password" maxlength="30" tabindex="2">
      </div>
    </div>
    <div class="control-group light-font">
      <div class="controls">
        <label class="checkbox">
          <input type="checkbox" name="remember" value="1" checked="checked" tabindex="3"> 两周内自动登录
        </label>
        <div class="help-block">
          <a id="thirdparty" href="#">其它方式登录</a>
          <span class="pipe">|</span>
          <a href="/forgot">忘记密码?</a>
        </div>
      </div>
    </div>
    <div class="modal-footer form-actions">
      <input class="btn btn-primary" id="signin-btn" type="submit" name="signin" value="登 录" tabindex="4">
      <a class="btn btn-success pull-right" href="/signup">注 册</a>
    </div>
  </form>
</div>
<?php
  }
}

function echoRightUL() {
  global $self;
  if (!isset($_SESSION['auth_token'])) {
?>
<ul class="nav pull-right">
  <li class="divider-vertical"></li>
  <li>
    <a data-show="modal" href="#signin">登录</a>
  </li>
  <li>
    <a href="/signup">注册</a>
  </li>
</ul>
<?php
  } else {
?>
<ul class="nav pull-right">
  <li class="divider-vertical"></li>
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
      <?php echo $_SESSION['name']; ?>
      <b class="caret"></b>
    </a>
    <ul class="dropdown-menu userpanel" data-stop="dropdown">
      <img class="userpanel-avatar" src="<?php echo $_SESSION['avatar']; ?>"/>
      <div class="userpanel-main">
        <h4><?php echo $_SESSION['name']; ?></h4>
        <p><?php echo $_SESSION['mail']; ?></p>
        <a class="btn btn-success btn-mypage" href="/user/<?php

        if (isset($_SESSION['domain'])) {
          echo $_SESSION['domain'];
        } else {
          echo $_SESSION['uid'];
        }

        ?>">我的页面</a>
      </div>
      <div class="userpanel-bottom">
        <a class="btn-grey pull-left" href="/account" target="_blank">帐号设置</a>
        <a class="btn-grey pull-right" id="signout" href="#signout">退出</a>
        <form id="signout-form" action="/signout" method="post">
          <input type="hidden" name="redir" value="<?php echo $self; ?>">
          <input type="hidden">
        </form>
      </div>
    </ul>
  </li>
</ul>
<?php
  }
}

session_start();

$bPost = isset($_POST['signin']);

if (!isset($_SESSION['auth_token']) && (isset($_COOKIE['mail']) && isset($_COOKIE['security']) || $bPost)) {

  $mail = $bPost ? $_POST['mail'] : $_COOKIE['mail'];
  $hash = $bPost ? hash('sha1', $_POST['password']) : $_COOKIE['security'];

  $result = mysql_query("SELECT `uid`, `domain`, `name`, `signin_count`, `avatar` FROM `ceedb`.`user`
   WHERE `mail` = '$mail' AND `password` = '$hash'");

  if (mysql_num_rows($result)) {
    $_SESSION['auth_token'] = hash('sha1', generateAuth());

    $r = mysql_fetch_assoc($result);
    $uid = $r['uid'];

    $_SESSION['uid'] = n2s($uid);
    if ($r['domain'] != '') $_SESSION['domain'] = $r['domain'];
    $_SESSION['name'] = $r['name'];
    $_SESSION['mail'] = $mail;
    $_SESSION['avatar'] = $r['avatar'];

    if (isset($_POST['remember'])) {
      setcookie('mail', $mail, time() + 14*24*3600, '/');
      setcookie('security', $hash, time() + 14*24*3600, '/');
    }

    $signin_count = $r['signin_count'] + 1;
    mysql_query("UPDATE `ceedb`.`user` SET `signin_count` = '$signin_count' WHERE `uid` = '$uid'");

  } else {
    $hint = $bPost ? '帐号或密码错误' : '请重新登录';
    $readyJs = '$(function(){$(\'[href="#signin"]\').click();setTimeout(function(){msg(3,\'' . $hint . '\',8000)},800)})';
  }
}
?>