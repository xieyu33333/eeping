<?php
require './php/conn.php';
require './php/global.php';
?>
<!DOCTYPE html>
<html>
<head>
<title>艺评 - 专注于消费电子评测UGC</title>
<link type="text/css" rel="stylesheet" href="/css/global.css">
<link type="text/css" rel="stylesheet" href="/css/home.css">
</head>

<body>
  <header class="navbar navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">
        <a class="brand" href="/">Eeping</a>
        <ul class="nav">
          <li class="active">
            <a href="/">首页</a>
          </li>
          <li>
            <a href="#">热门</a>
          </li>
          <li>
            <a href="#">NEW!</a>
          </li>
          <li>
            <a href="/product/bfFTGr">Product</a>
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
    <section class="intro"></section>
    <div class="boardOuter">
      <div class="board">
        <div class="backward">
          <div class="backwardIn"></div>
        </div>
        <ul class="boardIn"></ul>
        <div class="loadIcon"></div>
        <div class="boardClose"></div>
      </div>
    </div>
    <ul class="homeNav">
      <li class="unactive" id="rFTozhVm">笔记本</li>
      <li class="unactive" id="QxPIeZfd">手 机</li>
      <li class="unactive" id="gQwTNVJO">数码相机</li>
      <li class="unactive" id="VYnmPWjO">平板电脑</li>
      <li class="unactive" id="QKDRdrNP">一体电脑</li>
      <li class="unactive" id="VVtJez65">装机配件</li>
      <li class="unactive" id="Uy32OBfZ">其他数码</li>
    </ul>
  </div>
  <footer>
  </footer>
  <?php echoSignin(); ?>
  <script type="text/javascript" src="http://code.jquery.com/jquery.js"></script>
  <script type="text/javascript" src="/js/global.js"></script>
  <script type="text/javascript" src="/js/home.js"></script>
</body>
</html>