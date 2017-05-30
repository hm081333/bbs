//返回顶部按钮
jQuery(document).ready(function ($) {
	// browser window scroll (in pixels) after which the "back to top" link is shown
	var offset = 300,
		//browser window scroll (in pixels) after which the "back to top" link opacity is reduced
		offset_opacity = 1200,
		//duration of the top scrolling animation (in ms)
		scroll_top_duration = 700,
		//grab the "back to top" link
		$back_to_top = $('.cd-top');
	//hide or show the "back to top" link
	$(window).scroll(function () {
		($(this).scrollTop() > offset) ? $back_to_top.addClass('cd-is-visible') : $back_to_top.removeClass('cd-is-visible cd-fade-out');
		if ($(this).scrollTop() > offset_opacity) {
			$back_to_top.addClass('cd-fade-out');
		}
	});
	//smooth scroll to top
	$back_to_top.on('click', function (event) {
		event.preventDefault();
		$('body,html').animate({
			scrollTop: 0,
		}, scroll_top_duration);
	});
});

//上传图片预览
function preview(file) {
	var prevDiv = document.getElementById('preview');
	if (file.files && file.files[0]) {
		var reader = new FileReader();
		reader.onload = function (evt) {
			prevDiv.innerHTML = '<img src="' + evt.target.result + '" />';
		}
		reader.readAsDataURL(file.files[0]);
	} else {
		prevDiv.innerHTML = '<img style="filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\'' + file.value + '\'">';
	}
}

function stick_topic(topic_id) {
	$.ajax({
		type: 'POST',
		url: '?service=Topic.stick_Topic',
		data: {topic_id: topic_id},
		success: function (d) {
			if (d.ret == 200) {
				Materialize.toast(d.msg, 2000, 'rounded', function () {
					location.reload();
				});
			} else {
				Materialize.toast(d.msg, 2000, 'rounded');
			}
		}
	});
}

function unstick_topic(topic_id) {
	$.ajax({
		type: 'POST',
		url: '?service=Topic.unstick_Topic',
		data: {topic_id: topic_id},
		success: function (d) {
			if (d.ret == 200) {
				Materialize.toast(d.msg, 2000, 'rounded', function () {
					location.reload();
				});
			} else {
				Materialize.toast(d.msg, 2000, 'rounded');
			}
		}
	});
}

function delete_topic(topic_id) {
	$.ajax({
		type: 'POST',
		url: '?service=Topic.delete_Topic',
		data: {topic_id: topic_id},
		success: function (d) {
			if (d.ret == 200) {
				Materialize.toast(d.msg, 2000, 'rounded', function () {
					history.back();
				});
			} else {
				Materialize.toast(d.msg, 2000, 'rounded');
			}
		}
	});
}

