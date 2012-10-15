<?php
require './php/conn.php';
require './php/global.php';

isset($_SESSION['auth_token']) || header('Location:/');

?>
<!DOCTYPE html>
<html>
<head>
<title>帐号 - 艺评</title>
<link type="text/css" rel="stylesheet" href="/css/global.css">
<link rel="stylesheet" type="text/css" href="/css/jcrop.min.css">
<style type="text/css">
.main {
  width: 980px;
  margin: 0 auto;padding-top: 40px;
}
.nav-tabs {
  height: 502px;
}
.tab-content {
  width: 859px;
  height: 500px;
  background-color: #e9e8e3;
  border: 1px solid #d9d8ce;
  border-top: none;
  border-radius: 0px 0px 4px 4px;
}
.usr-avatar {
  width: 96px;
  border-radius: 5px;
}
.img-uploader {
  position: relative;
  width: 100%;height: 300px;
  border-top: 1px solid #d9d8ce;
  border-bottom: 1px solid #d9d8ce;
  color: #888;
  text-align: center;
}
.dragarea {
  height: 194px; 
  margin: 10px;
  padding-top: 80px;
  border: 3px dashed #d9d8ce;border-radius: 3px;
}
.dragarea.hover {
  border-color: #aaa;
}
.img-select-btn {
  position: relative;
}
.invisible-input {
  position: absolute;z-index: 2;top: 0;left: 0;
  width: 100%;height: 100%;
  opacity: 0;
  cursor: default;
}
#editarea {
  position: absolute;z-index: 2;top: 0;left: 0;
  display: none;
  width: 100%;height: 100%;
  background-color: #e9e8e3;
}
.edit-img-holder {
  margin: 10px 0 10px 40px;
}
#editarea p {
  position: absolute;top: 120px;right: 40px;
}
#avatar .form-actions {
  margin: 0px;
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
      <div class="tabbable tabs-left">
        <ul class="nav nav-tabs">
          <li class="active">
            <a href="#" data-toggle="tab" data-target="#basic">基本设置</a>
          </li>
          <li>
            <a href="#" data-toggle="tab" data-target="#avatar">头像</a>
          </li>
          <li>
            <a href="#" data-toggle="tab" data-target="#security">密码</a>
          </li>
          <li></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="basic">
            <form class="form-horizontal" action="#" name="basic" method="post">
              <div class="control-group">
                <label class="control-label" for="name">昵称</label>
                <div class="controls">
                  <input class="input-medium" type="text" name="name" maxlength="32" value="<?php echo $_SESSION['name']; ?>">
                  <div class="help-inline warning"></div>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="domain">个人网址</label>
                <div class="controls">
                  <input class="input-medium" type="text" name="domain" maxlength="32" value="<?php
                  echo isset($_SESSION['domain']) ? $_SESSION['domain'] : ''; ?>">
                  <div class="help-inline warning"></div>
                  <div class="help-block">www.eeping.net/个人网址</div>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="mail">邮箱地址</label>
                <div class="controls">
                  <input class="input-medium" type="text" name="mail" maxlength="32" value="<?php echo $_SESSION['mail']; ?>">
                  <div class="help-inline warning"></div>
                </div>
              </div>
              <div class="form-actions">
                <input class="btn btn-primary" type="submit" value="保 存">
              </div>
            </form>
          </div>
          <div class="tab-pane" id="avatar">
            <form class="form-horizontal">
              <div class="control-group">
                <label class="control-label" for="">当前头像</label>
                <div class="controls">
                  <img class="usr-avatar" src="<?php echo $_SESSION['avatar'];?>">
                </div>
              </div>
              <div class="img-uploader">
                <div class="dragarea" id="dragarea">
                  <h2>将图片拖至此处</h2>
                  <p>或者...</p>
                  <div class="btn btn-primary btn-large img-select-btn" data-loading-text="正在上传...">
                    <span>选择图片文件</span>
                    <input class="invisible-input" id="invisible-input" type="file">
                  </div>
                </div>
                <div id="editarea">
                  <div class="edit-img-holder">
                    <img id="edit-img">
                  </div>
                  <p>拖拽选取要截取的部分</p>
                </div>
              </div>
              <div class="form-actions">
                <div class="btn btn-primary" id="save-photo">保 存</div>
                <div class="btn" id="cancle-photo">取 消</div>
              </div>
            </form>
          </div>
          <div class="tab-pane" id="security">
            <form class="form-horizontal" action="#" name="security" method="post">
              <div class="control-group">
                <label class="control-label" for="oldpwd">旧密码</label>
                <div class="controls">
                  <input class="input-medium" type="password" name="oldpwd" maxlength="32">
                  <div class="help-inline warning"></div>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="password">新密码</label>
                <div class="controls">
                  <input class="input-medium" type="password" name="password" maxlength="32">
                  <div class="help-inline warning"></div>
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="repassword">确认新密码</label>
                <div class="controls">
                  <input class="input-medium" type="password" name="repassword" maxlength="32">
                  <div class="help-inline warning"></div>
                </div>
              </div>
              <div class="form-actions">
                <input class="btn btn-primary" type="submit" value="更新密码">
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript" src="http://code.jquery.com/jquery.js"></script>
  <script type="text/javascript" src="/js/jcrop.min.js"></script>
  <script type="text/javascript" src="/js/global.js"></script>
  <script type="text/javascript" src="/js/global_input_helper.js"></script>
  <script type="text/javascript" src="/js/account.js"></script>
  <script type="text/javascript">
  var uid = '<?php echo $_SESSION['uid']; ?>'
    , auth = '<?php echo $_SESSION['auth_token']; ?>'
  sName = '<?php echo $_SESSION['name']; ?>'
  sDo = '<?php echo isset($_SESSION['domain']) ? $_SESSION['domain'] : ''; ?>'
  sMail = '<?php echo $_SESSION['mail']; ?>'
  </script>
</body>
</html>