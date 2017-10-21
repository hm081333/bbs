<h3 class="center"><?php echo T('自动获取BDUSS') ?></h3>
<fieldset>
	<legend><?php echo T('自动获取BDUSS') ?></legend>
	<div class="row">
		<div class="col s12">
			<ul id="tabs-swipe-demo" class="tabs">
				<li class="tab col s3"><a href="#login1">普通登录</a></li>
				<li class="tab col s3"><a href="#login2">扫码登录</a></li>
				<li class="tab col s3"><a href="#login3">短信验证码登录</a></li>
				<li class="tab col s3"><a href="#login4">第三方登录</a></li>
			</ul>

			<div id="login1" class="col s12">
				<div class="row" style="border: 1px solid #ddd; margin-bottom: 0;">
					<div class="col s12 center">
						<img src="https://m.baidu.com/static/index/plus/plus_logo.png" style="margin: 1rem; width: 160px;">
					</div>
					<div id="load" class="alert alert-info" style="display:none;"></div>
				</div>
				<div id="login" class="row" style="border: 1px solid #ddd; margin-bottom: 0;">
					<form class="col s12">
						<div class="row">
							<div class="input-field col s12">
								<input id="user" name="user" type="text" class="validate"/>
								<label for="user">帐号</label>
							</div>
						</div>
						<div class="row">
							<div class="input-field col s12">
								<input id="pwd" name="pwd" type="password" class="validate"/>
								<label for="pwd">密码</label>
							</div>
						</div>
						<div class="row code" style="display:none;">
							<div class="input-field col s12 center">
								<div id="codeimg">1234</div>
							</div>
							<div class="input-field col s12">
								<input id="code" name="code" type="password" class="validate"/>
								<label for="code">输入验证码</label>
							</div>
						</div>
						<div class="col s12 center">
							<button style="width: 100%;" type="submit" name="submit" class="btn waves-effect waves-light"><?php echo T('提交') ?></button>
						</div>
					</form>
				</div>
				<div id="security" class="row" style="display:none; border: 1px solid #ddd;">
					<div class="col s12">
						<div class="row">
							<div class="input-field col s12" style="display: inline-flex;">
								<input id="smscode" name="smscode" type="text" class="validate"/>
								<button type="button" style="width: 13rem;" id="sendcode" class="btn waves-effect waves-light right">发送验证码</button>
								<label for="smscode">短信验证码</label>
							</div>
						</div>
					</div>
					<div class="col s12 center">
						<button style="width: 100%;" type="button" name="submit2" class="btn waves-effect waves-light"><?php echo T('提交') ?></button>
						<pre>提示：60秒内只能发送一次验证码，否则会提示频繁</pre>
					</div>
				</div>
			</div>

			<div id="login2" class="col s12">
				<div class="row" style="margin-bottom: 0;">
					<div class="col s12 center" style="border: 1px solid #ddd;">
						<img src="https://m.baidu.com/static/index/plus/plus_logo.png" style="margin: 1rem; width: 160px;">
					</div>
					<div id="load" class="col s12 center" style="font-weight: bold; border: 1px solid #ddd; color: #31708f; background-color: #d9edf7; padding: 10px 15px;">
						<span id="loginmsg">正在加载</span>
					</div>
				</div>
				<div class="row" id="login" style="/*display:none;*/ border: 1px solid #ddd; margin-bottom: 0;">
					<div class="col offset-s1 s10 center" id="qrimg" style="border: 1px solid #ddd; margin-bottom: 0; border-radius:5px">
						<img style="padding: 10px 15px;" onclick="getqrcode()" src="https://passport.baidu.com/v2/api/qrcode?sign=b5d0875082702db6168413d72d1b8187&amp;uaonly="
							 title="点击刷新">
						<button style="width: 100%;" type="button" name="submit" class="btn waves-effect waves-light"><?php echo T('已完成扫码') ?></button>
					</div>
				</div>
			</div>

			<div id="login3" class="col s12">
				Test 3
			</div>

			<div id="login4" class="col s12">
				Test 3
			</div>

		</div>
</fieldset>
<script>
	$(document).ready(function () {
		$('ul.tabs').tabs({
			/*swipeable: true,*/
		});
	});
</script>