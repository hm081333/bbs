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

//页面DOM加载后
$(document).ready(function () {
	// $(".button-collapse").sideNav();
	$('.modal').modal();
	$('.datepicker').pickadate({
		selectMonths: true, // Creates a dropdown to control month
		selectYears: 15 // Creates a dropdown of 15 years to control year
	});
	$('select').material_select();
	$('.collapsible').collapsible();


	var ip = returnCitySN['cip'];
	$('#ip').html(ip);
	$('#ip').attr('href', 'http://www.ip.cn/index.php?ip=' + ip);
	// var ip = $('#ip').html();
	$.ajax({
		type: 'POST',
		data: {service: 'Public.ip', ip: ip},
		success: function (d) {
			if (d.ret == 200) {
				$('#ip_address').text(d.data.country + ' ' + d.data.area + ' ' + d.data.region + ' ' + d.data.city + ' ' + d.data.isp)
			} else {
				$('#ip_address').text(d.msg)
			}
		}
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

// 打开url（未实现）
function open_url(service) {
	$.ajax({
		type: 'GET',
		data: {service: service, action: 'view'}
	});
}

// 帖子操作
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
}

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
}

function delete_topic(topic_id) {
	$.ajax({
		type: 'POST',
		data: {service: 'Topic.delete_Topic', topic_id: topic_id},
		success: function (d) {
			if (d.ret == 200) {
				Materialize.toast(d.msg, 2000, 'rounded', function () {
					if (d.data == 'admin') {
						location.reload();
					} else {
						history.back();
					}
				});
			} else {
				Materialize.toast(d.msg, 2000, 'rounded');
			}
		}
	});
}

// 会员操作
function update_user(user_id) {
	var auth = $('#auth' + user_id)[0]['checked'];
	if (auth == true) {
		auth = 1;
	} else {
		auth = 0;
	}
	var email = $('#email' + user_id).val();
	var real_name = $('#real_name' + user_id).val();
	var password = $('#password' + user_id).val();
	$.ajax({
		type: 'POST',
		data: {
			service: 'User.edit_Member', action: 'post', user_id: user_id, auth: auth, email: email,
			real_name: real_name,
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
}

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
}

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
}

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
}

function delete_admin(admin_id) {
	$.ajax({
		type: 'POST',
		data: {service: 'User.delete_Admin', admin_id: admin_id},
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
}

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
}

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
}

function check_google_auth(language) {
	var code = $('input[name="code"]').val();
	var secret = $('input[name="secret"]').val();
	$.ajax({
		type: 'POST',
		data: {service: 'Public.verify_Google_Auth_Code', code: code, secret: secret},
		success: function (d) {
			if (d.ret == 200) {
				Materialize.toast(d.msg, 2000, 'rounded', function () {
					$('input[name="check"]').attr('value', 1);
					$('#google_Auth').modal('close');
				});
			} else {
				Materialize.toast(d.msg, 2000, 'rounded');
			}
		}
	});
}

$('#Register').submit(function ()//提交表单
{
	$.ajax({
		type: 'POST',
		data: $("#Register").serialize(),
		success: function (d) {
			if (d.ret == 200) {
				Materialize.toast(d.msg, 2000, 'rounded', function () {
					if (d.data == 'admin') {
						location.reload();
					} else {
						history.back();
					}
				});
			} else {
				Materialize.toast(d.msg, 2000, 'rounded');
			}
		}
	});
});

$('#Login_in').submit(function ()//提交表单
{
	$.ajax({
		type: 'POST',
		data: $("#Login_in").serialize(),
		success: function (d) {
			if (d.ret == 200) {
				Materialize.toast(d.msg, 2000, 'rounded', function () {
					switch (d.data) {
						case 'user':
							history.back();
							break;
						default:
							location.reload();
							break;
					}
					/*if (d.data == 'admin') {
						location.reload();
					} else if (d.data == 'user') {
						history.back();
					} else if (d.data == 'tieba') {
						location.reload();
					}*/
				});
			} else {
				Materialize.toast(d.msg, 2000, 'rounded');
			}
		}
	});
});

// 谷歌身份认证登录
$('#forget').submit(function ()//提交表单
{
	$.ajax({
		type: 'POST',
		data: $("#forget").serialize(),
		success: function (d) {
			if (d.ret == 200) {
				Materialize.toast(d.msg, 2000, 'rounded', function () {
					history.back();
					// location.href='?service=User.login';
				});
			} else {
				Materialize.toast(d.msg, 2000, 'rounded');
			}
		}
	});
});

$('#add_Class').submit(function ()//提交表单
{
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
});

$('#Create_Topic').submit(function ()//提交表单
{
	$.ajax({
		type: 'POST',
		data: new FormData($('#Create_Topic')[0]),
		processData: false,
		contentType: false,
		success: function (d) {
			if (d.ret == 200) {
				Materialize.toast(d.msg, 2000, 'rounded', function () {
					// history.back();
					location.href = '?service=Topic.topic&topic_id=' + d.data['topic_id'];
				});
			} else {
				Materialize.toast(d.msg, 2000, 'rounded');
			}
		}
	});
});

$('#Reply_Topic').submit(function ()//提交表单
{
	$.ajax({
		type: 'POST',
		data: new FormData($('#Reply_Topic')[0]),
		processData: false,
		contentType: false,
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
});

$('#edit_member').submit(function ()//提交表单
{
	$.ajax({
		type: 'POST',
		data: $("#edit_member").serialize(),
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
});

$('#search').submit(function ()//提交表单
{
	$.ajax({
		type: 'POST',
		data: $("#search").serialize(),
		success: function (d) {
			if (d.ret == 200) {
				Materialize.toast(d.msg, 2000, 'rounded', function () {
					// location.reload();
				});
			} else {
				Materialize.toast(d.msg, 2000, 'rounded');
			}
		}
	});
});

$('#config').submit(function ()//提交表单
{
	$.ajax({
		type: 'POST',
		data: $("#config").serialize(),
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
});

$('.delivery_id').click(function () {
	var id = $(this).attr("data-id");
	$.ajax({
		type: 'POST',
		data: {service: 'Default.deliveryView', id: id},
		success: function (d) {
			if (d.ret == 200) {
				$('#delivery').html(d.data);
				$('.collapsible').collapsible();
				$('.collapsible').collapsible('open', 0);
				$('#delivery').modal('open');
			} else {
				Materialize.toast(d.msg, 2000, 'rounded');
			}
		}
	});
});

$('#Add_Delivery').submit(function ()//提交表单
{
	$.ajax({
		type: 'POST',
		data: $("#Add_Delivery").serialize(),
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
});

$('#Reset').submit(function ()//提交表单
{
	$.ajax({
		type: 'POST',
		data: $(this).serialize(),
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
});

$('#BackupModal form').submit(function ()//提交表单
{
	$.ajax({
		type: 'POST',
		data: $(this).serialize(),
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
});

$('a[restore]').click(function (event) {
	/* Act on the event */
	console.log($(this).data());
	var file_name = $(this).data();
	$('#RestoreModal input[name="name"]').val(file_name['name']);
	$('#RestoreModal').modal('open');
});

$('#RestoreModal form').submit(function ()//提交表单
{
	$.ajax({
		type: 'POST',
		data: $(this).serialize(),
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
});

/**
 * 点击跳转按钮
 */
/*$('.url').click(function () {
	var url = $(this).attr('data-url');
	console.log(url);
});*/
