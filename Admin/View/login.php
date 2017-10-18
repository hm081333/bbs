<h3 class="center"><?php echo T('后台登录') ?></h3>

<fieldset>
	<legend><?php echo T('登录') ?></legend>
	<div class="row">
		<form id="Login_in" method="post" onsubmit="return false;" class="col s12">
			<input name="action" value="post" type="hidden">
			<input name="service" value="User.login" type="hidden">
			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">account_box</i>
					<input id="user_name" name="user_name" type="text">
					<label for="user_name"><?php echo T('用户名') ?></label>
				</div>
				<div>
					<p class="msg right"><i class="material-icons">warning</i><?php echo T('请输入用户名') ?></p>
				</div>
			</div>
			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">vpn_key</i>
					<input id="password" name="password" type="password">
					<label for="password"><?php echo T('密码') ?></label>
				</div>
				<div>
					<p class="msg right"><i class="material-icons">warning</i><?php echo T('请输入密码') ?></p>
				</div>
			</div>
			<div class="col s12">
				<p style="text-align: center;">
					<input type="checkbox" id="remember" name="remember"/>
					<label for="remember">记住我</label>
				</p>
			</div>
			<div class="col s12 center">
				<button type="submit" name="submit" class="btn waves-effect waves-light"><?php echo T('登录') ?></button>
				<button type="reset" class="btn waves-effect waves-light">清空</button>
			</div>
		</form>
</fieldset>