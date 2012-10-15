<?php
require 'conn.php';
require 'global_fun.php';

session_start();

$type = $_POST['type'];
$uid = isset($_POST['uid']) ? s2n($_POST['uid']) : '';
$pid = isset($_POST['pid']) ? s2n($_POST['pid']) : '';
$rid = isset($_POST['rid']) ? $_POST['rid'] : '';

if ($type != 'getRowNum' && $type != 'getReview' && $type != 'getBug') {
  if (!isset($_SESSION['auth_token']) || !isset($_POST['auth']) || $_POST['auth'] != $_SESSION['auth_token']) exit();
}

switch ($type) {
  case 'getRowNum':
    $pre = "SELECT `id` FROM `ceedb`.`product_review_detail`
     WHERE `pid` = '$pid' AND `rid` = '$rid' AND `text` NOT LIKE ''";
    $mode1[] = "";
    $mode1[] = " AND `score` = '5'";
    $mode1[] = " AND `score` = '4'";
    $mode1[] = " AND `score` = '3'";
    $mode1[] = " AND `score` = '2'";
    $mode1[] = " AND `score` = '1'";
    $result = mysql_query($pre . $mode1[$_POST['mode1']]);
    outputDbError($result);
    echo json_encode(array(1, mysql_num_rows($result)));
    break;

  case 'getReview':
    $order = $_POST['mode2'];
    $start = $_POST['limit0'];
    $length = $_POST['limit1'];
    $pre = "SELECT `id`, `uid`, `score`, `text`, `time`, `like`, `dislike` FROM `ceedb`.`product_review_detail`
     WHERE `level` = '1' AND `pid` = '$pid' AND `rid` = '$rid' AND `text` NOT LIKE ''";
    $mode1[] = "";
    $mode1[] = " AND `score` = '5'";
    $mode1[] = " AND `score` = '4'";
    $mode1[] = " AND `score` = '3'";
    $mode1[] = " AND `score` = '2'";
    $mode1[] = " AND `score` = '1'";
    $mode2[] = " ORDER BY `like` DESC, `time` DESC";
    $mode2[] = " ORDER BY `time` DESC";
    $suf = " LIMIT $start, $length";
    $result = mysql_query($pre . $mode1[$_POST['mode1']] . $mode2[$order] . $suf);
    outputDbError($result);
    $r[0] = 1;
    $id = "";
    $mid = "";
    while ($row = mysql_fetch_assoc($result)) {
      $user[] = $row['uid'];
      if ($uid != '') $id .= " OR `text_id` = '" . $row['id'] . "'";
      $mid .= " OR `root_id` = '" . $row['id'] . "'";
      $row['id'] = n2s($row['id']);
      $r['root'][] = $row;
    }
    $mid = substr($mid, 3);
    $result = mysql_query("SELECT `id`, `par_id`, `level`, `uid`, `text`, `time`, `like`, `dislike`
     FROM `ceedb`.`product_review_detail` WHERE" . $mid . $mode2[$order]);
    outputDbError($result);
    while ($row = mysql_fetch_assoc($result)) {
      if ($uid != '') $id .= " OR `text_id` = '" . $row['id'] . "'";
      $row['id'] = n2s($row['id']);
      $row['par_id'] = n2s($row['par_id']);
      $r['children'][] = $row;
    }
    $user = array_unique($user);
    $query = '';
    foreach ($user as $k => $v) {
      if ($k == 0) {
        $query .= "`uid` = '$v'";
      } else {
        $query .= " OR `uid` = '$v'";
      }
    }
    $result = mysql_query("SELECT `uid`, `domain`, `name`, `avatar` FROM `ceedb`.`user` WHERE" . $query);
    outputDbError($result);
    while ($row = mysql_fetch_assoc($result)) {
      $r['user'][$row['uid']] = $row;
    }
    if ($uid != '') {
      $id = substr($id, 3);
      $result = mysql_query("SELECT `text_id`, `vote` FROM `ceedb`.`user_vote_detail`
       WHERE `uid` = '$uid' AND (" . $id . ")");
      outputDbError($result);
      while ($row = mysql_fetch_assoc($result)) {
        $r['vote'][n2s($row['text_id'])] = $row['vote'];
      }
    }
    echo json_encode($r);
    break;

  case 'getMine':
    $result = mysql_query("SELECT `rid`, `score`, `text` FROM `ceedb`.`product_review_detail`
     WHERE `uid` = '$uid' AND `pid` = '$pid' AND `level` = '1'");
    // outputDbError($result);
    $r[0] = 1;
    while ($row = mysql_fetch_assoc($result)) $r[1][] = $row;
    echo json_encode($r);
    break;

  case 'postScore':
    $score = $_POST['score'];
    $time = date('Y-m-d H:i:s');
    $query_getid = "SELECT `id` FROM `ceedb`.`product_review_detail`
     WHERE `uid` = '$uid' AND `pid` = '$pid' AND `rid` = '$rid' LIMIT 1";
    if (!mysql_num_rows(mysql_query($query_getid))) {
      $query = "INSERT INTO `ceedb`.`product_review_detail` (`uid`, `pid`, `rid`, `score`, `score_time`)
       VALUES ('$uid', '$pid', '$rid', '$score', '$time')";
    } else {
      $query = "UPDATE `ceedb`.`product_review_detail` SET `score` = '$score', `score_time` = '$time'
       WHERE `uid` = '$uid' AND `pid` = '$pid' AND `rid` = '$rid' LIMIT 1";
    }
    outputDbError(mysql_query($query));
    echo json_encode(array(1, ''));
    break;

  case 'postReview':
    $text = $_POST['text'];
    $time = date('Y-m-d H:i:s');
    outputDbError(mysql_query("UPDATE `ceedb`.`product_review_detail` SET `text` = '$text', `time` = '$time'
     WHERE `uid` = '$uid' AND `pid` = '$pid' AND `rid` = '$rid' LIMIT 1"));
    echo json_encode(array(1, ''));
    break;

  case 'delete':
    $time = date('Y-m-d H:i:s');
    outputDbError(mysql_query("UPDATE `ceedb`.`product_review_detail`
     SET `score` = '0', `score_time` = '$time', `text` = '', `time` = '$time',
     `like` = '0', `dislike` = '0', `wilson_score_interval` = '0'
     WHERE `level` = '1' AND `uid` = '$uid' AND `pid` = '$pid' AND `rid` = '$rid' LIMIT 1"));
    $result = mysql_query("SELECT `id` FROM `ceedb`.`product_review_detail`
     WHERE `level` = '1' AND `uid` = '$uid' AND `pid` = '$pid' AND `rid` = '$rid' LIMIT 1");
    outputDbError($result);
    $id = mysql_fetch_assoc($result)['id'];
    outputDbError(mysql_query("DELETE FROM `ceedb`.`product_review_detail` WHERE `root_id` = '$id'"));
    echo json_encode(array(1, ''));
    break;

  case 'postLike':
    $id = s2n($_POST['id']);
    $result = mysql_query("SELECT `id`, `vote` FROM `ceedb`.`user_vote_detail`
     WHERE `uid` = '$uid' AND `text_id` = '$id' LIMIT 1");
    if (mysql_num_rows($result)) {
      $r = mysql_fetch_assoc($result);
      $text_id = $r['id'];
      if ($r['vote'] == 't') exit(json_encode(array(0, '您已经投过票了...')));
      $dislike = mysql_fetch_row(mysql_query("SELECT `dislike` FROM `ceedb`.`product_review_detail`
       WHERE `id` = '$id' LIMIT 1"))[0];
      $dislike --;
      mysql_query("UPDATE `ceedb`.`product_review_detail` SET `dislike` = '$dislike' WHERE `id` = '$id' LIMIT 1");
      mysql_query("UPDATE `ceedb`.`user_vote_detail` SET `vote` = 't' WHERE `id` = '$text_id' LIMIT 1");
    } else {
      mysql_query("INSERT INTO `ceedb`.`user_vote_detail` VALUES (NULL, '$uid', '$id', 't')");
    }
    $like = mysql_fetch_row(mysql_query("SELECT `like` FROM `ceedb`.`product_review_detail` WHERE `id` = '$id' LIMIT 1"))[0];
    $like ++;
    mysql_query("UPDATE `ceedb`.`product_review_detail` SET `like` = '$like' WHERE `id` = '$id' LIMIT 1");
    echo json_encode(array(1, $like));
    break;

  case 'postDislike':
    $id = s2n($_POST['id']);
    $result = mysql_query("SELECT `id`, `vote` FROM `ceedb`.`user_vote_detail`
     WHERE `uid` = '$uid' AND `text_id` = '$id' LIMIT 1");
    if (mysql_num_rows($result)) {
      $r = mysql_fetch_assoc($result);
      $text_id = $r['id'];
      if ($r['vote'] == 'f') exit(json_encode(array(0, '您已经投过票了...')));
      $like = mysql_fetch_row(mysql_query("SELECT `like` FROM `ceedb`.`product_review_detail`
       WHERE `id` = '$id' LIMIT 1"))[0];
      $like --;
      mysql_query("UPDATE `ceedb`.`product_review_detail` SET `like` = '$like' WHERE `id` = '$id' LIMIT 1");
      mysql_query("UPDATE `ceedb`.`user_vote_detail` SET `vote` = 'f' WHERE `id` = '$text_id' LIMIT 1");
    } else {
      mysql_query("INSERT INTO `ceedb`.`user_vote_detail` VALUES (NULL, '$uid', '$id', 'f')");
    }
    $dislike = mysql_fetch_row(mysql_query("SELECT `dislike` FROM `ceedb`.`product_review_detail`
     WHERE `id` = '$id' LIMIT 1"))[0];
    $dislike ++;
    mysql_query("UPDATE `ceedb`.`product_review_detail` SET `dislike` = '$dislike' WHERE `id` = '$id' LIMIT 1");
    echo json_encode(array(1, $dislike));
    break;

  case 'postComment':
    $root_id = s2n($_POST['root_id']);
    $par_id = s2n($_POST['par_id']);
    $level = $_POST['level'];
    $comment = $_POST['comment'];
    $time = date('Y-m-d H:i:s');
    outputDbError(mysql_query("INSERT INTO `ceedb`.`product_review_detail`
     (`root_id`, `par_id`, `level`, `uid`, `text`, `time`)
     VALUES ('$root_id', '$par_id', '$level', '$uid', '$comment', '$time')"));
    $r[0] = 1;
    $r[1]['id'] = n2s(mysql_insert_id());
    $r[1]['time'] = $time;
    echo json_encode($r);
    break;

  case 'getBug':
    $result = mysql_query("SELECT `id`, `uid`, `text`, `time`, `like`, `dislike`
     FROM `ceedb`.`product_review_detail` WHERE `pid` = '$pid' AND `rid` = 'bug' ORDER BY `like` DESC");
    outputDbError($result);
    $r[0] = 1;
    $id = "";
    while ($row = mysql_fetch_assoc($result)) {
      $user[] = $row['uid'];
      if ($uid != '') $id .= " OR `text_id` = '" . $row['id'] . "'";
      $row['id'] = n2s($row['id']);
      $r['node'][] = $row;
    }
    $user = array_unique($user);
    $query = '';
    foreach ($user as $k => $v) {
      if ($k == 0) {
        $query .= "`uid` = '$v'";
      } else {
        $query .= " OR `uid` = '$v'";
      }
    }
    $result = mysql_query("SELECT `uid`, `domain`, `name`, `avatar` FROM `ceedb`.`user` WHERE" . $query);
    outputDbError($result);
    while ($row = mysql_fetch_assoc($result)) {
      $r['user'][$row['uid']] = $row;
    }
    if ($uid != '') {
      $id = substr($id, 3);
      $result = mysql_query("SELECT `text_id`, `vote` FROM `ceedb`.`user_vote_detail`
       WHERE `uid` = '$uid' AND (" . $id . ")");
      outputDbError($result);
      while ($row = mysql_fetch_assoc($result)) {
        $r['vote'][n2s($row['text_id'])] = $row['vote'];
      }
    }
    echo json_encode($r);
    break;

  case 'postBug':
    $bug = $_POST['bug'];
    $time = date('Y-m-d H:i:s');
    outputDbError(mysql_query("INSERT INTO `ceedb`.`product_review_detail`
     (`uid`, `pid`, `rid`, `text`, `time`) VALUES ('$uid', '$pid', 'bug', '$bug', '$time')"));
    $r[0] = 1;
    $r[1]['id'] = n2s(mysql_insert_id());
    $r[1]['time'] = $time;
    echo json_encode($r);
    break;

  case 'getSpecCandidates':
    $result = mysql_query("SELECT `id`, `spec_id`, `content`, `like` FROM `ceedb`.`candidates_spec_value`
     WHERE `pid` = '$pid' ORDER BY `spec_id`, `like` DESC");
    outputDbError($result);
    $r[0] = 1;
    while ($row = mysql_fetch_assoc($result)) {
      $row['id'] = n2s($row['id']);
      $row['spec_id'] = n2s($row['spec_id']);
      $r[1][] = $row;
    }
    echo json_encode($r);
    break;

  case 'postSpecVote':
    $time = date('Y-m-d H:i:s');
    $ids = str_split($_POST['candidate_id'], 6);
    foreach ($ids as $key => $id) {
      $id = s2n($id);
      $result = outputDbError(mysql_query("SELECT `like` FROM `ceedb`.`candidates_spec_value` WHERE `id` = '$id'"));
      $like = mysql_fetch_row($result)[0];
      $like ++;
      mysql_query("UPDATE `ceedb`.`candidates_spec_value` SET `like` = '$like' WHERE `id` = '$id'");
      mysql_query("INSERT INTO `ceedb`.`vote_spec_value` (`uid`, `candidate_id`, `time`) VALUES
       ('$uid', '$id', '$time')");
    }
    echo json_encode(array(1, ''));
    break;
}
?>