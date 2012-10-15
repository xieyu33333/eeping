<?php
require './php/conn.php';
require './php/global.php';

$hint = '';

if (isset($_POST['account'])) {
  $sql = "SELECT `uid`, `name` FROM `ceedb`.`user` WHERE `mail` = '" . $_POST['account'] . "'";
  $result = mysql_query($sql);
  $num_rows = mysql_num_rows($result);
  if ($num_rows) {
    $content = '您的帐户密码已被重置为';
    $r = mysql_fetch_assoc($result);
    $headers = 'From: 艺评网 <service@eeping.net>' . '"\r\n"';
    $to = $r['name'].' <'.$_POST['account'].'>';
    mail($to, '艺评网 - 密码找回', $content, $headers);
  } else {
    $hint = '找不到该帐号, <a href="/signup?mail=' . urlencode($_POST['account']) . '">马上注册?</a>';
  }
} else if (isset($_COOKIE['mail']) && isset($_COOKIE['security'])) {
  header('Location:/');
}
?>
<!DOCTYPE html>
<html>
<head>
<title>密码找回 - 艺评</title>
<link type="text/css" rel="stylesheet" href="/css/global.css">
<style type="text/css">
.main {
  width: 400px;
  margin: 0 auto;padding-top: 80px;
}
#hint {
  width: 100%;
  border-radius: 2px;
  background-color: rgba(255,165,0,.3);
  color: #69664F;
  font-size: 12px;line-height: 2;text-align: center;
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
            <a href="/product/igGblEmv">Product</a>
          </li>
        </ul>
        <?php echoRightUL(); ?>
        <form class="navbar-search pull-right" action="#">
          <input class="search-query" type="text" name="key" placeholder="搜索" maxlength="20">
          <input class="navbar-search-mag" type="submit"  name="search" value="">
        </form>
      </div>
    </div>
  </header>
  <div class="container">
    <div class="main">
      <div id="hint"><?php echo $hint;?></div>
      <form class="form-horizontal" action="/forgot" method="post">
        <div class="control-group">
            <label class="control-label" for="account">注册邮箱</label>
          <div class="controls">
            <input class="input-medium" id="account" type="text" name="account" placeholder="请输入您的注册邮箱" maxlength="50">
          </div>
        </div>
        <div class="form-actions">
          <input class="btn btn-success" id="forgot-btn" type="submit" value="发送邮件">
        </div>
      </form>
    </div>
  </div>
  <?php echoSignin(); ?>
  <script type="text/javascript" src="http://code.jquery.com/jquery.js"></script>
  <script type="text/javascript" src="/js/global.js"></script>
  <script type="text/javascript">
  $(function () {
    $('#forgot-btn').click(function () {
      var mailReg = /^[_\.0-9a-zA-Z+-]+@([0-9a-zA-Z]+[0-9a-zA-Z-]*\.)+[a-zA-Z]{2,4}$/
        , ac = $('#account').val()
      if (!ac) {
        return false
      } else if (!mailReg.test(ac)) {
        $('#hint').html('请输入正确的Email地址')
        return false
      } else {
        return
      }
    })
  })
  </script>
</body>
</html>