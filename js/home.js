$(document).ready(function() {
	var $main = $('.boardOuter');
	var $mainIn = $('.boardIn');
	var $homeNav = $('.homeNav');
	var $back = $('.backward');
	var working = false;

	function resizeSelect() {
		var h = $(window).height() - 91;
		$('.board').css('min-height', h + 'px');
	}

	function fetchselect(val) {
		if (working) {
			return false;
		}
		working = true;
		$.getJSON('/php/home_ajax.php', {
			par_id: val
		}, function(r) {
			var ids, lis;
			$.each(r, function(k, v) {
				ids = 'id="' + k + '"';
				if (v.link) {
					lis += '<li><a href="' + v.link + '" target="_blank">\
					<p class="name">' + v.title + '</p>\
					<p class="detail">' + v.describe + '</p>\
					</a></li>';
				} else {
					lis += '<li ' + ids + '><img src="' + v.title + '"><p>' + v.describe + '</p></li>';
				}
			});
			$(lis).appendTo($mainIn);
			$mainIn.attr('id', val);
		});
		working = false;
	}
	$(window).resize(resizeSelect);
	$homeNav.find('.unactive').live('click', function() {
		$homeNav.find('.active').addClass('unactive').removeClass('active');
		$(this).addClass('active').removeClass('unactive');
		$back.css('visibility', 'hidden');
		$mainIn.empty().hide();
		$main.fadeIn(200);
		resizeSelect();
		var id = $(this).attr('id');
		fetchselect(id);
		$mainIn.fadeIn(200);
	});
	$('.boardClose').click(function() {
		$homeNav.find('.active').addClass('unactive').removeClass('active');
		$main.hide();
	});
	$main.find('li').live('click', function() {
		var id = $(this).attr('id');
		if (id) {
			$mainIn.empty().hide();
			fetchselect(id);
			$mainIn.fadeIn(200);
			$back.css('visibility', 'visible');
		}
	});
	$back.live('click', function() {
		if (working) {
			return false;
		}
		working = true;
		var id = $mainIn.attr('id');
		$mainIn.empty().hide();
		$.getJSON('/php/home_ajax.php', {
			id: id
		}, function(r) {
			var k = r[0];
			if (k == 'rFTozhVm' || k == 'QxPIeZfd' || k == 'gQwTNVJO' || k == 'VYnmPWjO' || k == 'QKDRdrNP' || k == 'VVtJez65' || k == 'Uy32OBfZ') {
				$back.css('visibility', 'hidden');
			}
			fetchselect(k);
		});
		$mainIn.fadeIn(200);
		working = false;
	});
	$('.loadIcon').ajaxStart(function() {
		$(this).show();
	}).ajaxStop(function() {
		$(this).hide();
	});
});