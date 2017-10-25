<h3 class="center"><?php echo T('添加BDUSS') ?></h3>
<fieldset>
	<legend><?php echo T('添加BDUSS') ?></legend>
	<div class="row">
		<div class="col s12">
			<ul class="tabs">
				<li class="tab col s3"><a href="#login0">手动添加</a></li>
				<li class="tab col s3"><a href="#login1">普通登录</a></li>
				<li class="tab col s3"><a href="#login2">扫码登录</a></li>
				<li class="tab col s3"><a href="#login3">短信验证码登录</a></li>
			</ul>

			<div id="login0" class="col s12">
				<div class="row" style="border: 1px solid #ddd; margin-bottom: 0;">
					<!-- <div class="col s12 center">
						<img src="https://m.baidu.com/static/index/plus/plus_logo.png"
							 style="margin: 1rem; width: 160px;">
					</div> -->
					<div id="load" class="col s12 center" style="font-weight: bold; border: 1px solid #ddd; color: #31708f; background-color: #d9edf7; padding: 10px 15px;">
						手动填写BDUSS，请自行打开百度页面获取该COOKIES
					</div>
				</div>
				<div id="login" class="row" style="border: 1px solid #ddd; margin-bottom: 0;">
					<div class="col s12">
						<div class="row">
							<div class="input-field col s12">
								<input id="bduss" type="text" value="" class="validate"
									   onkeydown="if(event.keyCode==13){addBduss.click()}"/>
								<label for="bduss">bduss</label>
							</div>
						</div>
						<div class="col s12 center">
							<button style="width: 100%;" type="button" id="addBduss"
									class="btn waves-effect waves-light"><?php echo T('提交') ?></button>
						</div>
					</div>
				</div>
			</div>

			<div id="login1" class="col s12">
				<div class="row" style="border: 1px solid #ddd; margin-bottom: 0;">
					<!-- <div class="col s12 center">
						<img src="https://m.baidu.com/static/index/plus/plus_logo.png"
							 style="margin: 1rem; width: 160px;">
					</div> -->
					<div id="load" class="col s12 center"
						 style="display: none; font-weight: bold; border: 1px solid #ddd; color: #31708f; background-color: #d9edf7; padding: 10px 15px;">
					</div>
				</div>
				<div id="login" class="row" style="border: 1px solid #ddd; margin-bottom: 0;">
					<div class="col s12">
						<div class="row">
							<div class="input-field col s12">
								<input id="user" name="user" type="text" value="" class="validate"
									   onkeydown="if(event.keyCode==13){submit.click()}"/>
								<label for="user">帐号</label>
							</div>
						</div>
						<div class="row">
							<div class="input-field col s12">
								<input id="pwd" name="pwd" type="password" value="" class="validate"
									   onkeydown="if(event.keyCode==13){submit.click()}"/>
								<label for="pwd">密码</label>
							</div>
						</div>
						<div class="row code" style="display:none;">
							<div class="input-field col s12 center">
								<div id="codeimg"></div>
							</div>
							<div class="input-field col s12">
								<input id="code" name="code" type="text" class="validate"
									   onkeydown="if(event.keyCode==13){submit.click()}"/>
								<label for="code">输入验证码</label>
							</div>
						</div>
						<div class="col s12 center">
							<button style="width: 100%;" type="button" id="submit"
									class="btn waves-effect waves-light"><?php echo T('提交') ?></button>
						</div>
					</div>
				</div>
				<div id="security" class="row" style="display:none; border: 1px solid #ddd;">
					<div class="col s12">
						<div class="row">
							<div class="input-field col s12" style="display: inline-flex;">
								<input id="smscode" name="smscode" type="text" class="validate"
									   onkeydown="if(event.keyCode==13){submit.click()}"/>
								<button type="button" style="width: 13rem;" id="sendcode"
										class="btn waves-effect waves-light right">发送验证码
								</button>
								<label for="smscode">短信验证码</label>
							</div>
						</div>
					</div>
					<div class="col s12 center">
						<button style="width: 100%;" type="button" id="submit2"
								class="btn waves-effect waves-light"><?php echo T('提交') ?></button>
						<pre>提示：60秒内只能发送一次验证码，否则会提示频繁</pre>
					</div>
				</div>
			</div>

			<div id="login2" class="col s12">
				<div class="row" id="login" style="display:none; border: 1px solid #ddd; margin-bottom: 0;">
					<div class="center" id="qrimg">
					</div>
					<button style="width: 100%;" type="button" id="submit"
							class="btn waves-effect waves-light"><?php echo T('已扫码') ?></button>
				</div>
			</div>

			<div id="login3" class="col s12">
				<div class="row" style="margin-bottom: 0;">
					<!-- <div class="col s12 center" style="border: 1px solid #ddd;">
						<img src="https://m.baidu.com/static/index/plus/plus_logo.png"
							 style="margin: 1rem; width: 160px;">
					</div> -->
					<div id="load" class="col s12 center"
						 style="font-weight: bold; border: 1px solid #ddd; color: #31708f; background-color: #d9edf7; padding: 10px 15px; display: none;">
					</div>
				</div>
				<div id="login" class="row" style="border: 1px solid #ddd; margin-bottom: 0;">
					<form class="col s12" onsubmit="return false;">
						<div class="row">
							<div class="input-field col s12">
								<input id="phone" name="phone" type="text" class="validate"
									   onkeydown="if(event.keyCode==13){submit.click()}"/>
								<label for="phone">手机号</label>
							</div>
						</div>
						<div class="row">
							<div class="input-field col s12" id="sms" style="display: none;/*display: inline-flex;*/">
								<input id="smscode" name="smscode" type="text" class="validate"
									   onkeydown="if(event.keyCode==13){submit.click()}"/>
								<button type="button" style="width: 13rem;" id="sendcode"
										class="btn waves-effect waves-light right">发送验证码
								</button>
								<label for="smscode">短信验证码</label>
							</div>
						</div>
						<div class="row code" style="display:none;">
							<div class="input-field col s12 center">
								<div id="codeimg">
								</div>
							</div>
							<div class="input-field col s12">
								<input id="code" name="code" type="text" class="validate"
									   onkeydown="if(event.keyCode==13){submit.click()}"/>
								<label for="code">输入验证码</label>
							</div>
						</div>
						<div class="col s12 center">
							<button style="width: 100%;" type="submit" id="submit"
									class="btn waves-effect waves-light"><?php echo T('提交') ?></button>
						</div>
					</form>
				</div>
			</div>
		</div>
</fieldset>
<script src="<?php echo DI()->tool->staticPath('js/bduss/base.js'); ?>"></script>
<script>
	/*普通登陆*/
	function trim(str) {
		return str.replace(/(^\s*)|(\s*$)/g, "");
	}

	function getpwd(pwd, time) {
		var passwd = pwd + time;
		var rsa = "B3C61EBBA4659C4CE3639287EE871F1F48F7930EA977991C7AFE3CC442FEA49643212E7D570C853F368065CC57A2014666DA8AE7D493FD47D171C0D894EEE3ED7F99F6798B7FFD7B5873227038AD23E3197631A8CB642213B9F27D4901AB0D92BFA27542AE890855396ED92775255C977F5C302F1E7ED4B1E369C12CB6B1822F";
		setMaxDigits(131);
		var key = new RSAKeyPair("10001", "", rsa);
		return encryptedString(key, passwd);
	}

	function showresult(arr, elementId) {
		$(elementId + ' #load').html('<div class="alert alert-success">登录成功！' + decodeURIComponent(arr.displayname) + '</div><div class="input-group"><span class="input-group-addon">用户UID</span><input id="uid" value="' + arr.uid + '" class="form-control" /></div><br/><div class="input-group"><span class="input-group-addon">用户名</span><input id="user" value="' + arr.user + '" class="form-control"/></div><br/><div class="input-group"><span class="input-group-addon">BDUSS</span><input id="bduss" value="' + arr.bduss + '" class="form-control"/></div><br/><div class="input-group"><span class="input-group-addon">PTOKEN</span><input id="ptoken" value="' + arr.ptoken + '" class="form-control"/></div><br/><div class="input-group"><span class="input-group-addon">STOKEN</span><input id="stoken" value="' + arr.stoken + '" class="form-control"/></div>');
	}

	function checkvc(user, pwd, elementId) {
		$(elementId + ' #load').html('登录中，请稍候...');
		Ajax({'service': 'Tieba.CheckVC', 'user': user}, function (d) {
			if (d.ret == 200) {
				var data = d.data;
				if (data.code == 0) {
					gettime(user, pwd, null, null, elementId);
				} else if (data.code == 1) {
					$(elementId + ' #load').html('请输入验证码。');
					getvc(data.vcodestr, elementId);
				} else {
					$(elementId + ' #load').html(data.msg + " (" + data.code + ")");
					$(elementId + ' .code').hide();
				}
			} else {
				Materialize.toast(d.msg, 2000, 'rounded');
			}

		});
	}

	function gettime(user, pwd, vcode, vcodestr, elementId) {
		vcode = vcode || null;
		vcodestr = vcodestr || null;
		$(elementId + ' #load').html('正在获取Token，请稍等...');
		Ajax({'service': 'Tieba.Time'}, function (d) {
			if (d.ret == 200) {
				var data = d.data;
				if (data.code == 0) {
					login(data.time, user, pwd, vcode, vcodestr, elementId);
				} else {
					$(elementId + ' #load').html(data.msg);
				}
			} else {
				Materialize.toast(d.msg, 2000, 'rounded');
			}
		});
	}

	function sendcode(type, lstr, ltoken, elementId) {
		Ajax({
			'service': 'Tieba.SendCode', 'type': type, 'lstr': lstr, 'ltoken': ltoken, 'r': Math.random(1)
		}, function (d) {
			if (d.ret == 200) {
				var d = d.data;
				if (d.code == 0) {
					$(elementId + ' .code').hide();
					$(elementId + ' #smscode').focus();
					alertMsg('验证码发送成功，请查收');
				} else {
					$(elementId + ' .code').hide();
					alertMsg(d.msg);
				}
			} else {
				Materialize.toast(d.msg, 2000, 'rounded');
			}
		});
	}

	function getvc(vcodestr, elementId) {
		$(elementId + ' #codeimg').attr('vcodestr', vcodestr);
		$(elementId + ' #codeimg').html('<img onclick="this.src=\'?service=Tieba.GetVCPic&vcodestr=' + vcodestr + '&r=\'+Math.random();" src="?service=Tieba.GetVCPic&vcodestr=' + vcodestr + '&r=' + Math.random(1) + '" title="点击刷新">');
		$(elementId + ' #submit').attr('do', 'code');
		$(elementId + ' #code').val("");
		$(elementId + ' .code').show();
	}

	function login(time, user, pwd, vcode, vcodestr, elementId) {
		$(elementId + ' #load').html('正在登录，请稍等...');
		var p = getpwd(pwd, time);
		Ajax({
			'service': 'Tieba.Login', 'time': time, 'user': user, 'pwd': pwd, 'p': p, 'vcode': vcode,
			'vcodestr': vcodestr, 'r': Math.random(1)
		}, function (d) {
			if (d.ret == 200) {
				var d = d.data;
				if (d.code == 0) {
					$(elementId + ' #login').hide();
					$(elementId + ' .code').hide();
					$(elementId + ' #submit').hide();
					$(elementId + ' #security').hide();
					$(elementId + ' #submit2').hide();
					showresult(d, elementId);
				} else if (d.code == 400023) {
					if (d.type == 'phone') {
						$(elementId + ' #load').html("请验证密保后登录，密保手机是：" + d.phone);
					} else {
						$(elementId + ' #load').html("请验证密保后登录，密保邮箱是：" + d.email);
					}
					$(elementId + ' #submit').hide();
					$(elementId + ' .code').hide();
					$(elementId + ' #code').val("");
					$(elementId + ' #security').show();
					$(elementId + ' #security').attr('type', d.type);
					/*$(elementId + ' #security').attr('lstr', encodeURIComponent(d.lstr));*/
					$(elementId + ' #security').attr('lstr', d.lstr);
					$(elementId + ' #security').attr('ltoken', d.ltoken);
				} else if (d.code == 310006 || d.code == 500001 || d.code == 500002) {
					/*需要验证码*/
					$(elementId + ' #load').html(d.msg);
					getvc(d.vcodestr, elementId);
				} else if (d.code == 230048 || d.code == 400010) {
					$(elementId + ' #load').html("您输入的账号不存在，请重新输入");
					$(elementId + ' #submit').attr('do', 'submit');
					$(elementId + ' .code').hide();
					$(elementId + ' #code').val("");
					$(elementId + ' #user').focus();
					$(elementId + ' #user').val("");
				} else if (d.code == 400011 || d.code == 400015) {
					$(elementId + ' #load').html("您输入的密码有误，请重新输入");
					$(elementId + ' #submit').attr('do', 'submit');
					$(elementId + ' .code').hide();
					$(elementId + ' #code').val("");
					$(elementId + ' #pwd').focus();
					$(elementId + ' #pwd').val("");
				} else {
					$(elementId + ' #load').html(d.msg + " (" + d.code + ")");
					$(elementId + ' #submit').attr('do', 'submit');
					$(elementId + ' .code').hide();
					$(elementId + ' #login').show();
				}
			} else {
				Materialize.toast(d.msg, 2000, 'rounded');
			}
		});
	}

	function login2(type, lstr, ltoken, vcode, elementId) {
		$(elementId + ' #load').html('正在登录，请稍等...');
		Ajax({
			'service': 'Tieba.login2', 'type': type, 'lstr': lstr, 'ltoken': ltoken, 'vcode': vcode, 'r': Math.random(1)
		}, function (d) {
			if (d.ret == 200) {
				var d = d.data;
				if (d.code == 0) {
					$(elementId + ' #login').hide();
					$(elementId + ' .code').hide();
					$(elementId + ' #submit').hide();
					$(elementId + ' #security').hide();
					$(elementId + ' #submit2').hide();
					showresult(d, elementId);
				} else {
					$(elementId + ' #load').html(d.msg + " (" + d.code + ")");
					$(elementId + ' .code').hide();
					$(elementId + ' #login').show();
				}
			} else {
				Materialize.toast(d.msg, 2000, 'rounded');
			}
		});
	}

	/*获取二维码*/
	function getqrcode() {
		Ajax({
			'service': 'Tieba.GetQRCode', 'r': Math.random(1)
		}, function (d) {
			if (d.ret == 200) {
				var d = d.data;
				if (d.code == 0) {
					$('#login2 #qrimg').attr('sign', d.sign);
					$('#login2 #qrimg').html('<img onclick="getqrcode()" src="https://' + d.imgurl + '" title="点击刷新">');
					$('#login2 #login').show();
					$('#login2 #submit').html('已扫码');
				} else {
					alertMsg(d.msg);
				}
			} else {
				Materialize.toast(d.msg, 2000, 'rounded');
			}
		});
	}

	/*扫码登陆*/
	function qrlogin() {
		var sign = $('#login2 #qrimg').attr('sign');
		if (sign == '') {
			return;
		}
		$('#login2 #submit').html('Loading...');
		$('#login2 #submit').addClass('disabled');
		Ajax({'service': 'Tieba.QRLogin', 'sign': sign, 'r': Math.random(1)}, function (d) {
			SuccessMsg(d, null, function () {
				$('#login2 #submit').html('二维码正在刷新');
				getqrcode();
				$('#login2 #submit').removeClass('disabled');
			});
		});
	}

	/*手机号登陆*/
	function loginByPhone(phone, smsvc, elementId) {
		$(elementId + ' #load').html('正在登录，请稍等...');
		Ajax({'service': 'Tieba.Login3', 'phone': phone, 'smsvc': smsvc, 'r': Math.random(1)}, function (d) {
			if (d.ret == 200) {
				var d = d.data;
				if (d.code == 0) {
					$(elementId + ' #login').hide();
					$(elementId + ' .code').hide();
					$(elementId + ' #submit').hide();
					$(elementId + ' #sms').hide();
					showresult(d, elementId);
				} else {
					$(elementId + ' #load').html(d.msg + " (" + d.code + ")");
					$(elementId + ' .code').hide();
					$(elementId + ' #login').show();
				}
			} else {
				Materialize.toast(d.msg, 2000, 'rounded');
			}
		});
	}

	function sendsms(phone, vcode, vcodestr, vcodesign, elementId) {
		vcode = vcode || null;
		vcodestr = vcodestr || null;
		vcodesign = vcodesign || null;
		$(elementId + ' #load').html('正在发送验证码...');
		Ajax({
			'service': 'Tieba.SendSms', 'phone': phone, 'vcode': vcode, 'vcodestr': vcodestr, 'vcodesign': vcodesign,
			'r': Math.random(1)
		}, function (d) {
			if (d.ret == 200) {
				var d = d.data;
				if (d.code == 0) {
					$(elementId + ' .code').hide();
					$(elementId + ' #sms').show();
					$(elementId + ' #sms').css('display', 'inline-flex');
					$(elementId + ' #submit').attr('do', 'smscode');
					$(elementId + ' #smscode').focus();
					$(elementId + ' #load').html('请输入短信验证码');
					alertMsg('已发送验证码到 ' + phone);
				} else if (d.code == 50020) {
					$(elementId + ' #load').html(d.msg);
					$(elementId + ' #codeimg').attr('vcodesign', d.vcodesign);
					$(elementId + ' #sms').hide();
					$(elementId + ' #submit').attr('do', 'code');
					getvc(d.vcodestr, elementId);
				} else if (d.code == 500002 || d.code == 500001) {
					$(elementId + ' #load').html('请输入验证码');
					$(elementId + ' #submit').attr('do', 'code');
					alertMsg(d.msg);
				} else if (d.code == 50014) {
					$(elementId + ' #load').html('提示：60秒内只能发送一次验证码，否则会提示频繁');
					$(elementId + ' .code').hide();
					alertMsg(d.msg);
				} else {
					$(elementId + ' .code').hide();
					alertMsg(d.msg);
				}
			} else {
				Materialize.toast(d.msg, 2000, 'rounded');
			}
		});
	}

	function getphone(phone, elementId) {
		$(elementId + ' #load').html('正在检测手机号是否存在...');
		Ajax({'service': 'Tieba.GetPhone', 'phone': phone, 'r': Math.random(1)}, function (d) {
			if (d.ret == 200) {
				var d = d.data;
				if (d.code == 0) {
					sendsms(phone, '', '', '', elementId);
				} else if (d.code == 3) {
					$(elementId + ' #load').html('');
					$(elementId + ' .code').hide();
					$(elementId + ' #submit').attr('do', 'submit');
					alertMsg('该手机号不存在，请重新输入！');
				} else {
					$(elementId + ' #load').html(d.msg + " (" + d.code + ")");
					$(elementId + ' #submit').attr('do', 'submit');
					$(elementId + ' .code').hide();
				}
			} else {
				Materialize.toast(d.msg, 2000, 'rounded');
			}
		});
	}

	$(document).ready(function () {
		/*手动填写BDUSS*/
		$('#login0 #addBduss').click(function () {
			$('#login0 #load').hide();
			var self = $(this);
			var bduss = trim($('#login0 #bduss').val());
			if (bduss == '') {
				$('#login0 #load').html('<span style="color: red;">请确保每项不能为空！</span>');
				$('#login0 #load').show();
				return false;
			}
			self.addClass('disabled');
			$('#login0 #load').show();
			Ajax({'service': 'Tieba.AddBdussAC', 'bduss': bduss});
			self.removeClass('disabled');
		});

		/*账号密码登陆*/
		$('#login1 #submit').click(function () {
			$('#login1 #load').hide();
			var self = $(this);
			self.addClass('disabled');
			var user = trim($('#login1 #user').val()),
				pwd = trim($('#login1 #pwd').val());
			if (user == '' || pwd == '') {
				$('#login1 #load').html('<span style="color: red;">请确保每项不能为空！</span>');
				$('#login1 #load').show();
				self.removeClass('disabled');
				return false;
			}
			$('#login1 #load').show();
			if (self.attr('do') == 'code') {
				var vcode = trim($('#login1 #code').val()),
					vcodestr = $('#login1 #codeimg').attr('vcodestr');
				gettime(user, pwd, vcode, vcodestr, '#login1');
			} else {
				checkvc(user, pwd, '#login1');
			}
			self.removeClass('disabled');
		});
		$('#login1 #submit2').click(function () {
			var self = $(this);
			var code = trim($('#smscode').val());
			if (code == '') {
				$('#login1 #load').html('<span style="color: red;">验证码不能为空！</span>');
				$('#login1 #load').show();
				self.removeClass('disabled');
				return false;
			}
			$('#login1 #load').show();
			self.addClass('disabled');
			var type = $('#login1 #security').attr('type'),
				lstr = $('#login1 #security').attr('lstr'),
				ltoken = $('#login1 #security').attr('ltoken');
			login2(type, lstr, ltoken, code, '#login1');
			self.removeClass('disabled');
		});
		$('#login1 #sendcode').click(function () {
			var self = $(this);
			$('#login1 #load').show();
			self.addClass('disabled');
			var type = $('#login1 #security').attr('type'),
				lstr = $('#login1 #security').attr('lstr'),
				ltoken = $('#login1 #security').attr('ltoken');
			sendcode(type, lstr, ltoken, '#login1');
			self.removeClass('disabled');
		});

		/*扫码登陆*/
		getqrcode();
		$('#login2 #submit').click(function () {
			qrlogin();
		});

		/*手机号登陆*/
		$('#login3 #submit').click(function () {
			var self = $(this);
			var elementId = '#login3';
			var phone = trim($('#login3 #phone').val()),
				smscode = trim($('#login3 #smscode').val());
			if (phone == '') {
				alertMsg("手机号不能为空！");
				return false;
			}
			$('#login3 #load').show();
			self.addClass('disabled');
			if (self.attr('do') == 'smscode') {
				if (smscode == '') {
					alertMsg("验证码不能为空！");
					return false;
				}
				loginByPhone(phone, smscode, elementId);
			} else if (self.attr('do') == 'code') {
				if (code == '') {
					alertMsg("验证码不能为空！");
					return false;
				}
				var code = trim($('#login3 #code').val()),
					vcodestr = $('#login3 #codeimg').attr('vcodestr'),
					vcodesign = $('#login3 #codeimg').attr('vcodesign');
				sendsms(phone, code, vcodestr, vcodesign, elementId);
			} else {
				getphone(phone, elementId);
			}
			self.removeClass('disabled');
		});
		$('#login3 #sendcode').click(function () {
			var self = $(this);
			var elementId = '#login3';
			var phone = trim($('#login3 #phone').val());
			if (phone == '') {
				alertMsg("手机号不能为空！");
				return false;
			}
			$('#login3 #load').show();
			self.addClass('disabled');
			var code = trim($('#login3 #code').val()),
				vcodestr = $('#login3 #codeimg').attr('vcodestr'),
				vcodesign = $('#login3 #codeimg').attr('vcodesign');
			sendsms(phone, code, vcodestr, vcodesign, elementId);
			self.removeClass('disabled');
		});

	});
</script>