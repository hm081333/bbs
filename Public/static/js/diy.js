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
};


function open_url(service) {
	$.ajax({
		type: 'GET',
		data: {service: service, action: 'view'}
	});
};



function stick_topic(topic_id) {
	$.ajax({
		type: 'POST',
		data: {service: 'Topic.stick_Topic', topic_id: topic_id},
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
};

function unstick_topic(topic_id) {
	$.ajax({
		type: 'POST',
		data: {service: 'Topic.unstick_Topic', topic_id: topic_id},
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
};

function delete_topic(topic_id) {
	$.ajax({
		type: 'POST',
		data: {service: 'Topic.delete_Topic', topic_id: topic_id},
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
};

function admin_delete_topic(topic_id) {
	$.ajax({
		type: 'POST',
		data: {service: 'Topic.delete_Topic', topic_id: topic_id},
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
};

function update_user(user_id) {
	var auth = $('#auth' + user_id)[0]['checked'];
	if (auth == true) {
		auth = 1;
	} else {
		auth = 0;
	}
	var email = $('#email' + user_id).val();
	var realname = $('#realname' + user_id).val();
	var password = $('#password' + user_id).val();
	$.ajax({
		type: 'POST',
		data: {
			service: 'User.edit_Member', action: 'post', user_id: user_id, auth: auth, email: email, realname: realname,
			password: password
		},
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
};

function delete_user(user_id) {
	$.ajax({
		type: 'POST',
		data: {service: 'User.delete_User', user_id: user_id},
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
};

function login() {
	$.ajax({
		type: 'POST',
		data: $("#Login_in").serialize(),
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
};

function logoff() {
	$.ajax({
		type: 'POST',
		data: {service: 'User.logoff'},
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
};

function register() {
	$.ajax({
		type: 'POST',
		data: $("#Register").serialize(),
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
};

function update_admin(admin_id) {
	var auth = $('#auth' + admin_id)[0]['checked'];
	if (auth == true) {
		auth = 1;
	} else {
		auth = 0;
	}
	var password = $('#password' + admin_id).val();
	$.ajax({
		type: 'POST',
		data: {service: 'User.admin_list', action: 'post', admin_id: admin_id, auth: auth, password: password},
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
};

function delete_admin(admin_id) {
	$.ajax({
		type: 'POST',
		data: {service: 'User.delete_User', admin_id: admin_id},
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
};

function create_Class() {
	$.ajax({
		type: 'POST',
		data: $("#add_Class").serialize(),
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
};

function update_Class(class_id) {
	var name = $('#name' + class_id).val();
	var tips = $('#tips' + class_id).val();
	$.ajax({
		type: 'POST',
		data: {service: 'Class.update_Class', action: 'post', name: name, tips: tips, class_id: class_id},
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
};

function delete_Class(class_id) {
	$.ajax({
		type: 'POST',
		data: {service: 'Class.delete_Class', class_id: class_id},
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
};

function set_language(language) {
	$.ajax({
		type: 'POST',
		data: {service: 'Public.setLanguage', language: language},
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
};

