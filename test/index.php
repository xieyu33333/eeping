<?php
require '../php/conn.php';
if (isset($_POST['uid'])) {
	$p[] = $_POST['uid'];
	$p[] = $_POST['pid'];
	$p[] = $_POST['rid'];
	if ($_POST['par_id'] != '') {
		$p[] = $_POST['par_id'];
		$p[] = magjcid();
		$p[] = 1;
		$p[] = '';
	} else {
		$p[] = '';
		$p[] = magjcid();
		$p[] = 1;
		$p[] = $_POST['score'];
	}
	$p[] = $_POST['review'];
	$p[] = date('Y-m-d H:i:s');
	$p[] = $_POST['like'];
	$p[] = $_POST['dislike'];
	$sql = "INSERT INTO `ceedb`.`product_rank_detail_bk` VALUES (
		'$p[0]', '$p[1]', '$p[2]', '$p[3]', '$p[4]', '$p[5]', '$p[6]', '$p[7]', '$p[8]', '$p[9]', '$p[10]', '')";
	$result = mysql_query($sql);
	if (!$result) {
		$r[] = 0;
		$r[] = mysql_error();
		exit(json_encode($r));
	}
	echo json_encode($p);
} else {
?>
<!DOCTYPE html>
<html>
<head>
<title>Test Space</title>
<!-- <link type="text/css" rel="stylesheet" href="/css/global.css"> -->
<style type="text/css">
.container {
	margin: 0 10px;padding-top: 20px;
}
th, td {
	height: 40px;
	border: 1px solid #CCC;
	text-align: center;
}
#g {
	position: fixed;top: 0;right: 0;
	margin: 20px;padding: 0 1em;
	font-size: 20px;line-height: 1.7;
}
#i {
	width: 120px;
	line-height: 1.7;
}
.id {
	color: #4D7BD6;
	cursor: pointer;
}
</style>
<script type="text/javascript" src="http://code.jquery.com/jquery.js"></script>
<script type="text/javascript" src="/js/global.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	function ranString (t) {
		var out = '';
		if (!t) {t = 8;}
		for (var i = 0; i < t; i++) {
			out += 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'.charAt(Math.floor(Math.random() * 62));
		}
		return out;
	}
	function ranNum (t) {
		return Math.ceil(Math.random() * t);
	}
	$('#i').click(function() {
		$(this).toggleClass('light-btn light-btn-actived');
	});
	$('.id').live('click', function() {
		$('#i').html($(this).html());
	});
	$('#g').click(function() {
		var uid = ranString(),
			pid = 'igGblEmv',
			rid = 0,
			par_id = '',
			score = ranNum(5),
			review = ranString(32),
			like = ranNum(100),
			dislike = ranNum(100);
		if ($('#i').hasClass('light-btn-actived')) {
			par_id = $('#i').html();
		}
		$.post('index.php', {uid:uid, pid:pid, rid:rid, par_id:par_id, score:score, review:review, like:like, dislike:dislike}, function(r) {
			if (r[0] == 0) {
				msg(4, r[1]);
				return false;
			}
			var arr = new Array();
			arr.push('<tr>');
			for (var i = 0; i < r.length; i++) {
				if (i == 4) {
					arr.push('<td class="id">' + r[i] + '</td>');
				} else {
					arr.push('<td>' + r[i] + '</td>');
				}
			};
			arr.push('</tr>');
			$('#table').append(arr.join());
		}, 'json');
	});
});
</script>
</head>
<body>
	<div class="container">
		<div>
			<div class="light-btn" id="g">generate</div>
			<div class="light-btn" id="i">N.A.</div>
			<table id="table">
				<tr>
					<th>uid</th>
					<th>pid</th>
					<th>rid</th>
					<th>par_id</th>
					<th>id</th>
					<th>score</th>
					<th>review</th>
					<th>time</th>
					<th>like</th>
					<th>dislike</th>
				</tr>
			</table>
			<input type="search">
			<input type="date"><input type="datetime"><input type="datetime-local"><input type="email"><input type="file">
			<input type="image"><input type="month"><input type="number"><input type="password"><input type="radio">
			<input type="range"><input type="reset"><input type="submit"><input type="text"><input type="time">
			<input type="url"><input type="week">
		</div>
	</div>
	<div id="msg-area"></div>
</body>
</html><?php } ?>