<?php
require './php/conn.php';
require './php/global.php';

isset($_SESSION['auth_token']) || $_SESSION['redir'] = $self;

$sPid = substr_replace($self, '', 0, 9);
$pid = s2n($sPid);

mysql_num_rows(mysql_query("SELECT `pid` FROM `ceedb`.`current_product` WHERE `pid` = '$pid' LIMIT 1")) || exit('请求的页面不存在...');

$product = mysql_fetch_assoc(mysql_query("SELECT `cid`, `model`, `series`, `company`, `name`, `description`, `img`
 FROM `ceedb`.`current_product` WHERE `pid` = '$pid' LIMIT 1"));
$cid = $product['cid'];

$model = mysql_fetch_row(mysql_query("SELECT `title` FROM `ceedb`.`current_catalog_by_company`
 WHERE `id` = '" . $product['model'] . "'"))[0];
$series = mysql_fetch_row(mysql_query("SELECT `title` FROM `ceedb`.`current_catalog_by_company`
 WHERE `id` = '" . $product['series'] . "'"))[0];
$company = mysql_fetch_row(mysql_query("SELECT `title` FROM `ceedb`.`current_catalog_by_company`
 WHERE `id` = '" . $product['company'] . "'"))[0];

$spec = '';
$spec_editor = '';

$result = mysql_query("SELECT `spec_id`, `title` FROM `ceedb`.`current_spec_name`
 WHERE `cid` = '$cid' ORDER BY `order`");
while ($row = mysql_fetch_assoc($result)) {
  $spec_n = $row['title'];
  $spec_id = $row['spec_id'];
  $spec_v = mysql_fetch_row(mysql_query("SELECT `content` FROM `ceedb`.`current_spec_value`
   WHERE `pid` = '$pid' AND `spec_id` = '$spec_id' LIMIT 1"))[0];
  $spec_id = n2s($spec_id);
  $spec .= '
  <div class="specline" id="'.$spec_id.'">
    <div class="spec_n">'.$spec_n.'</div>
    <div class="spec_v">'.$spec_v.'</div>
  </div>';
  $spec_editor .= '
  <div class="spec-editor-line btn-toolbar" id="'.$spec_id.'">
    <div class="spec-editor-n">'.$spec_n.'</div>
    <div class="btn-group"></div>
    <a class="btn-simple" href="#">全部有误?</a>    
  </div>';
}

$rank = '';
$s100l67 = array('#5656FE', '#54a9ff', '#54feff', '#54ffaa', '#54ff54', '#aaff54',
 '#feff54', '#ffa954', '#ff5454', '#ff54aa', '#ff54fe', '#A954FF');
$s67l67 = array('#7272e3', '#72aae3', '#72e3e3', '#72e3aa', '#72e372', '#aae372',
 '#e3e372', '#e3aa72', '#e37272', '#e372aa', '#e372e3', '#aa72e3');
$s62l62 = array('#6262da', '#629eda', '#62dada', '#62da9e', '#62da62', '#9eda62',
 '#dada62', '#da9e62', '#da6262', '#da629e', '#da62da', '#9e62da');
$i = 0;

$result = mysql_query("SELECT `rid`, `title` FROM `ceedb`.`current_review_class`
 WHERE `cid` = '$cid' ORDER BY `order`");
while ($row = mysql_fetch_assoc($result)) {
  $review_n = $row['title'];
  $rid = $row['rid'];
  $review_score = mysql_fetch_row(mysql_query("SELECT `score` FROM `ceedb`.`current_review_score`
   WHERE `pid` = '$pid' AND `rid` = '$rid' LIMIT 1"))[0];
  $rank .= '
  <li class="rankname" id="'.$rid.'">
    <a href="#" data-toggle="tab" data-target="#review">'.$review_n.'</a>
    <div class="colorful" style="height:'.round(($review_score - 20) / 80 * 70).'px;background-color:'.$s62l62[$i].'">
      <div class="score">'.($review_score / 10).'</div>
    </div>
  </li>';
  $i ++;
  $i == 12 && $i = 0;
}

$thumb = '';

for ($i=1; $i <= $product['img']; $i++) {
  $thumb .= '<img class="thms" src="/img/'.$sPid.'/'.$sPid.$i.'.png">';
}

?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $product['name']; ?>- 艺评</title>
<link type="text/css" rel="stylesheet" href="/css/global.css">
<link type="text/css" rel="stylesheet" href="/css/product.css">
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
          <li class="active">
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
    <ul class="nav breadcrumb">
      <li>
        <a href="#"><?php echo $company; ?></a>
      </li>
      <li>
        <a href="#"><?php echo $series; ?></a>
      </li>
      <li class="active">
        <a><?php echo $product['name'] . " ($model)"; ?></a>
      </li>
    </ul>

    <div class="aside">
      <div class="show-gallery" data-show="modal" href="#gallery">
        <img src="http://www.networksasia.net/system/files/nwa/100611.jpg">
      </div>
      <div class="function">
        <button class="btn"><i class="icon-picture"></i> 更多照片</button>
        <button class="btn"><i class="icon-star"></i> 收藏</button>
        <button class="btn"><i class="icon-signal"></i> 趋势</button>
        <button class="btn"><i class="icon-flag"></i> 标记</button>
      </div>
      <div id="spec">
        <a id="btn-edit-spec" class="btn-edit btn-simple" data-show="modal" href="#spec-editor">编辑</a>
        <h4>技术规格</h4>
        <div><?php echo $spec; ?></div>
      </div>
      <div id="desc">
        <a id="btn-edit-desc" class="btn-edit btn-simple" data-show="modal" href="#desc-editor">编辑</a>
        <h4>简介 . . .</h4>
        <p><?php echo $product['description']; ?></p>
      </div>
    </div>

    <div class="main">
      <div class="mainchart">
        <div class="subnav">
          <ul class="nav nav-pills">
            <?php echo $rank; ?>
          </ul>
        </div>
        <div class="add-on-nav">
          <ul class="nav nav-pills">
            <li class="bug-report">
              <a href="#" data-toggle="tab" data-target="#bug-report"><i class="icon-warning-sign"></i> 缺陷</a>
            </li>
            <li class="buy">
              <a href="#"><i class="icon-shopping-cart"></i> 购买</a>
            </li>
          </ul>
        </div>
      </div>
      <div class="tab-content">
        <div id="review" class="tab-pane active review">
          <div class="review-panel">
            <img class="review-avatar" src="<?php
            echo isset($_SESSION['avatar']) ? $_SESSION['avatar'] : '/img/default_avatar.gif';
            ?>">
            <div class="review-panel-inner">
              <div class="star-holder">
                <p class="star-title">评分:</p>
                <ul class="stars">
                  <li class="unstarred"></li>
                  <li class="unstarred"></li>
                  <li class="unstarred"></li>
                  <li class="unstarred"></li>
                  <li class="unstarred"></li>
                </ul>
              </div>
              <div class="textarea-holder">
                <p class="text-published"></p>
                <textarea placeholder="写点什么(限140字)" disabled="disabled"></textarea>
                <div class="textarea-counter"></div>
                <div class="textarea-btn-holder">
                  <button class="btn disabled" type="button">发 布</button>
                  <button class="btn disabled" type="button">删除</button>
                </div>
              </div>
            </div>
          </div>
          <div class="stream review-stream">
            <div class="stream-header">
              <p class="stream-title">大家的看法</p>
              <div class="stream-control">
                <div class="btn-group">
                  <div class="btn" id="byrank">
                    <span id="rankstat">1-5星</span>
                    <span class="caret"></span>
                  </div>
                  <div id="ranklist">
                    <div class="rankopt">1-5星</div>
                    <div class="rankopt">5星</div>
                    <div class="rankopt">4星</div>
                    <div class="rankopt">3星</div>
                    <div class="rankopt">2星</div>
                    <div class="rankopt">1星</div>
                  </div>
                </div>
                <div class="btn-group">
                  <div class="btn" id="bypage">
                    <span id="pagestat">第1页</span>
                    <span class="caret"></span>
                  </div>
                  <div id="pagelist"></div>
                </div>
                <div class="btn-group">
                  <button class="btn active" id="byuse">按有用</button>
                  <button class="btn" id="bytime">按时间</button>
                </div>
              </div>
            </div>
            <div class="stream-content"></div>
            <div class="stream-footer">
              <div class="light-btn-holder">
                <div class="light-btn" id="nextpage">下一页</div>
              </div>
            </div>
          </div>
        </div>

        <div id="bug-report" class="tab-pane stream">
          <div class="stream-header">
            <p class="stream-title">这个产品可能存在的设计缺陷</p>
            <div class="stream-control">
              <button id="submit-bug" class="btn"><i class="icon-pencil"></i> 报告缺陷</button>
            </div>
          </div>
          <div class="stream-content"></div>
          <div class="stream-footer"></div>
        </div>

      </div>
    </div>
  </div>

  <div id="gallery">
    <div class="gallery-header">
      <a class="close" data-dismiss="modal">×</a>
      <p>正面</p>
    </div>
    <div class="gallery-image"></div>
    <div class="gallery-thumb">
      <?php echo $thumb; ?>
    </div>
  </div>

  <div id="spec-editor" class="modal">
    <div class="modal-header">
      <a class="close" data-dismiss="modal">×</a>
      <h3>更改这个产品的信息</h3>
    </div>
    <div class="modal-body">
      <?php echo $spec_editor; ?>
    </div>
    <div class="modal-footer">
      <button class="btn btn-primary">好的</button>
      <button class="btn" data-dismiss="modal">放弃</button>
    </div>
  </div>

  <div class="prototype" id="">
    <div class="node" level="1">
      <div class="node-content">
        <div class="node-header">
          <a class="node-link" href="" target="_blank">
            <img class="review-avatar" src=""/>
            <p class="node-name"></p>
          </a>
          <div class="node-time"></div>
          <div class="node-star">
            <div class="unstarred-tiny"></div>
            <div class="unstarred-tiny"></div>
            <div class="unstarred-tiny"></div>
            <div class="unstarred-tiny"></div>
            <div class="unstarred-tiny"></div>
          </div>
        </div>
        <div class="node-text"></div>
        <div class="reply">回复</div>
        <div class="vote btn-group">
          <button class="btn btn-mini"><i class="icon-heart"></i><div class="like-data">0</div></button>
          <button class="btn btn-mini"><i class="icon-trash"></i><div class="dislike-data">0</div></button>
        </div>
        </div>
      </div>
    </div>
  </div>

  <?php echoSignin(); ?>

  <script type="text/javascript" src="http://code.jquery.com/jquery.js"></script>
  <script type="text/javascript" src="/js/global.js"></script>
  <script type="text/javascript" src="/js/product.js"></script>
  <script type="text/javascript">
    var pid = '<?php echo $sPid; ?>'
      , uid = '<?php echo isset($_SESSION['uid']) ? $_SESSION['uid'] : ''; ?>'
      , auth = '<?php echo isset($_SESSION['auth_token']) ? $_SESSION['auth_token'] : ''; ?>'
      , name = '<?php echo isset($_SESSION['name']) ? $_SESSION['name'] : ''; ?>'
      , avatar = '<?php echo isset($_SESSION['avatar']) ? $_SESSION['avatar'] : ''; ?>'
    <?php echo isset($readyJs) ? $readyJs : ''; ?>
  </script>
</body>
</html>