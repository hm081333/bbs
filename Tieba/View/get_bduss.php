<h3 class="center"><?php echo T('自动获取BDUSS') ?></h3>
<fieldset>
	<legend><?php echo T('自动获取BDUSS') ?></legend>
	<div class="row">
		<div class="col s12">
			<ul class="tabs">
				<li class="tab col s3"><a href="#login1">普通登录</a></li>
				<li class="tab col s3"><a href="#login2">扫码登录</a></li>
				<li class="tab col s3"><a href="#login3">短信验证码登录</a></li>
				<li class="tab col s3"><a href="#login4">第三方登录</a></li>
			</ul>

			<div id="login1" class="col s12">
				<div class="row" style="border: 1px solid #ddd; margin-bottom: 0;">
					<div class="col s12 center">
						<img src="https://m.baidu.com/static/index/plus/plus_logo.png"
							 style="margin: 1rem; width: 160px;">
					</div>
					<div id="load" class="col s12 center"
						 style="display: none; font-weight: bold; border: 1px solid #ddd; color: #31708f; background-color: #d9edf7; padding: 10px 15px;">
					</div>
				</div>
				<div id="login" class="row" style="border: 1px solid #ddd; margin-bottom: 0;">
					<div class="col s12">
						<div class="row">
							<div class="input-field col s12">
								<input id="user" name="user" type="text" value="hm081333" class="validate"/>
								<label for="user">帐号</label>
							</div>
						</div>
						<div class="row">
							<div class="input-field col s12">
								<input id="pwd" name="pwd" type="password" value="lyihe110" class="validate"/>
								<label for="pwd">密码</label>
							</div>
						</div>
						<div class="row code" style="display:none;">
							<div class="input-field col s12 center">
								<div id="codeimg"></div>
							</div>
							<div class="input-field col s12">
								<input id="code" name="code" type="text" class="validate"/>
								<label for="code">输入验证码</label>
							</div>
						</div>
						<div class="col s12 center">
							<button style="width: 100%;" type="submit" id="submit"
									class="btn waves-effect waves-light"><?php echo T('提交') ?></button>
						</div>
					</div>
				</div>
				<div id="security" class="row" style="display:none; border: 1px solid #ddd;">
					<div class="col s12">
						<div class="row">
							<div class="input-field col s12" style="display: inline-flex;">
								<input id="smscode" name="smscode" type="text" class="validate"/>
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
				<div class="row" style="margin-bottom: 0;">
					<div class="col s12 center" style="border: 1px solid #ddd;">
						<img src="https://m.baidu.com/static/index/plus/plus_logo.png"
							 style="margin: 1rem; width: 160px;">
					</div>
					<div id="load" class="col s12 center"
						 style="font-weight: bold; border: 1px solid #ddd; color: #31708f; background-color: #d9edf7; padding: 10px 15px;">
						<span id="loginmsg">正在加载</span>
					</div>
				</div>
				<div class="row" id="login" style="/*display:none;*/ border: 1px solid #ddd; margin-bottom: 0;">
					<div class="col offset-s1 s10 center" id="qrimg"
						 style="border: 1px solid #ddd; margin-bottom: 0; border-radius:5px;">
						<img style="padding: 1rem;" onclick="getqrcode()"
							 src="https://passport.baidu.com/v2/api/qrcode?sign=dfb4ca3b9892e0ae7b127f034350a08b&uaonly="
							 title="点击刷新">
						<button style="width: 100%;" type="button" name="submit"
								class="btn waves-effect waves-light"><?php echo T('已完成扫码') ?></button>
					</div>
				</div>
			</div>

			<div id="login3" class="col s12">
				<div class="row" style="margin-bottom: 0;">
					<div class="col s12 center" style="border: 1px solid #ddd;">
						<img src="https://m.baidu.com/static/index/plus/plus_logo.png"
							 style="margin: 1rem; width: 160px;">
					</div>
					<div id="load" class="col s12 center"
						 style="font-weight: bold; border: 1px solid #ddd; color: #31708f; background-color: #d9edf7; padding: 10px 15px; display: none;">
					</div>
				</div>
				<div id="login" class="row" style="border: 1px solid #ddd; margin-bottom: 0;">
					<form class="col s12" onsubmit="return false;">
						<div class="row">
							<div class="input-field col s12">
								<input id="phone" name="phone" type="text" class="validate"/>
								<label for="phone">手机号</label>
							</div>
						</div>
						<div class="row">
							<div class="input-field col s12" style="display: none;/*display: inline-flex;*/">
								<input id="smscode" name="smscode" type="text" class="validate"/>
								<button type="button" style="width: 13rem;" id="sendcode"
										class="btn waves-effect waves-light right">发送验证码
								</button>
								<label for="smscode">短信验证码</label>
							</div>
						</div>
						<div class="row code" style="display:none;">
							<div class="input-field col s12 center">
								<div id="codeimg">1234</div>
							</div>
							<div class="input-field col s12">
								<input id="code" name="code" type="text" class="validate"/>
								<label for="code">输入验证码</label>
							</div>
						</div>
						<div class="col s12 center">
							<button style="width: 100%;" type="submit" name="submit"
									class="btn waves-effect waves-light"><?php echo T('提交') ?></button>
						</div>
					</form>
				</div>
			</div>

			<div id="login4" class="col s12">
				<div class="row" style="margin-bottom: 0;">
					<div class="col s12 center" style="border: 1px solid #ddd;">
						<img src="https://m.baidu.com/static/index/plus/plus_logo.png"
							 style="margin: 1rem; width: 160px;">
					</div>
					<div class="col s12" style="border: 1px solid #ddd;">
						<ul class="tabs">
							<li class="tab col s3"><a href="#login4-1">QQ普通登录</a></li>
							<li class="tab col s3"><a href="#login4-2">QQ扫码登录</a></li>
						</ul>
					</div>
					<div class="col s12" style="border: 1px solid #ddd; margin-bottom: 0;">
						<div id="login4-1" class="row" style="margin-bottom: 0;">
							<div id="load" class="col s12 center"
								 style="font-weight: bold; border: 1px solid #ddd; color: #31708f; background-color: #d9edf7; padding: 10px 15px;">
								请使用百度账号绑定的QQ登录
							</div>
							<form class="col s12">
								<div class="row">
									<div class="input-field col s12">
										<input id="uin" name="uin" type="text" class="validate"/>
										<label for="uin">QQ帐号</label>
									</div>
								</div>
								<div class="row">
									<div class="input-field col s12">
										<input id="pwd" name="pwd" type="password" class="validate"/>
										<label for="pwd">QQ密码</label>
									</div>
								</div>
								<div class="row code" style="display:none;">
									<div class="input-field col s12 center">
										<div id="codeimg">1234</div>
									</div>
									<div class="input-field col s12">
										<input id="code" name="code" type="text" class="validate"/>
										<label for="code">输入验证码</label>
									</div>
								</div>
								<div class="col s12 center">
									<button style="width: 100%;" type="submit" name="submit"
											class="btn waves-effect waves-light"><?php echo T('提交') ?></button>
								</div>
							</form>
						</div>
						<div id="login4-2" class="row" style="margin-bottom: 0;">
							<div id="load" class="col s12 center"
								 style="font-weight: bold; border: 1px solid #ddd; color: #31708f; background-color: #d9edf7; padding: 10px 15px;">
								<span id="loginmsg">使用QQ手机版扫描二维码</span>
								<span id="loginload" style="padding-left: 10px;color: #790909;">.</span>
							</div>
							<div class="col offset-s1 s10 center" id="qrimg">
								<img style="padding: 1rem;" onclick="getqrcode()"
									 src="https://passport.baidu.com/v2/api/qrcode?sign=dfb4ca3b9892e0ae7b127f034350a08b&uaonly="
									 title="点击刷新">
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
</fieldset>
<script src="<?php echo DI()->tool->staticPath('js/bduss/base.js'); ?>"></script>
<script src="<?php echo DI()->tool->staticPath('js/bduss/collect.js'); ?>"></script>
<script src="<?php echo DI()->tool->staticPath('js/bduss/qqlogin.js'); ?>"></script>
<script>
	function trim(str) {
		return str.replace(/(^\s*)|(\s*$)/g, "");
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

	function sendcode(type, lstr, ltoken) {
		Ajax({
			'service': 'Tieba.SendCode', 'type': type, 'lstr': lstr, 'ltoken': ltoken, 'r': Math.random(1)
		}, function (d) {
			if (d.ret == 200) {
				var d = d.data;
				if (d.code == 0) {
					$('.code').hide();
					$('#smscode').focus();
					alert('验证码发送成功，请查收');
				} else {
					$('.code').hide();
					alert(d.msg);
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

	function getpwd(pwd, time) {
		var passwd = pwd + time;
		var rsa = "B3C61EBBA4659C4CE3639287EE871F1F48F7930EA977991C7AFE3CC442FEA49643212E7D570C853F368065CC57A2014666DA8AE7D493FD47D171C0D894EEE3ED7F99F6798B7FFD7B5873227038AD23E3197631A8CB642213B9F27D4901AB0D92BFA27542AE890855396ED92775255C977F5C302F1E7ED4B1E369C12CB6B1822F";
		setMaxDigits(131);
		var key = new RSAKeyPair("10001", "", rsa);
		return encryptedString(key, passwd);
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
					$('.code').hide();
					$(elementId + ' #code').val("");
					$(elementId + ' #security').show();
					$(elementId + ' #security').attr('type', d.type);
					$(elementId + ' #security').attr('lstr', encodeURIComponent(d.lstr));
					$(elementId + ' #security').attr('ltoken', d.ltoken);
				} else if (d.code == 310006 || d.code == 500001 || d.code == 500002) {
					/*需要验证码*/
					$(elementId + ' #load').html(d.msg);
					console.log(d);
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
					showresult(d);
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

	function showresult(arr, elementId) {
		$(elementId + ' #load').html('<div class="alert alert-success">登录成功！' + decodeURIComponent(arr.displayname) + '</div><div class="input-group"><span class="input-group-addon">用户UID</span><input id="uid" value="' + arr.uid + '" class="form-control" /></div><br/><div class="input-group"><span class="input-group-addon">用户名</span><input id="user" value="' + arr.user + '" class="form-control"/></div><br/><div class="input-group"><span class="input-group-addon">BDUSS</span><input id="bduss" value="' + arr.bduss + '" class="form-control"/></div><br/><div class="input-group"><span class="input-group-addon">PTOKEN</span><input id="ptoken" value="' + arr.ptoken + '" class="form-control"/></div><br/><div class="input-group"><span class="input-group-addon">STOKEN</span><input id="stoken" value="' + arr.stoken + '" class="form-control"/></div>');
	}

	$(document).ready(function () {
		$('ul.tabs').tabs({
			/*swipeable: true,*/
		});

		$('#login1 #login #submit').click(function () {
			$('#login1 #load').hide();
			var self = $(this);
			self.addClass('disabled');
			var user = trim($('#login1 #login #user').val()),
				pwd = trim($('#login1 #login #pwd').val());
			if (user == '' || pwd == '') {
				$('#login1 #load').html('<span style="color: red;">请确保每项不能为空！</span>');
				$('#login1 #load').show();
				return false;
			}
			$('#login1 #load').show();
			if (self.attr('do') == 'code') {
				var vcode = trim($('#login1 #login #code').val()),
					vcodestr = $('#login1 #login #codeimg').attr('vcodestr');
				gettime(user, pwd, vcode, vcodestr, '#login1');
			} else {
				checkvc(user, pwd, '#login1');
			}
			self.removeClass('disabled');
		});
		$('#login1 #security #submit2').click(function () {
			var self = $(this);
			var code = trim($('#smscode').val());
			if (code == '') {
				$('#login1 #load').html('<span style="color: red;">验证码不能为空！</span>');
				$('#login1 #load').show();
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
		$('#login1 #security #sendcode').click(function () {
			var self = $(this);
			$('#login1 #load').show();
			self.addClass('disabled');
			var type = $('#login1 #security').attr('type'),
				lstr = $('#login1 #security').attr('lstr'),
				ltoken = $('#login1 #security').attr('ltoken');
			sendcode(type, lstr, ltoken, '#login1');
			self.removeClass('disabled');
		});
	});
</script>