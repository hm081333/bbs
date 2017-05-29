<?php
require_once './Public/static/header/header.php';
?>
<script src="./Public/static/js/login.js"></script>
<h3 class="center">用户登录</h3>

<fieldset>
	<legend>Login</legend>
	<div class="row">
		<form id="Login_in" class="col s12">
			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">account_box</i>
					<input id="username" name="username" type="text">
					<label for="username">用户名</label>
				</div>
				<div>
					<p class="msg right"><i class="material-icons">warning</i>请输入用户名</p>
				</div>
			</div>

			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">vpn_key</i>
					<input id="password" name="password" type="password">
					<label for="password">密码</label>
				</div>
				<div>
					<p class="msg right"><i class="material-icons">warning</i>请输入密码</p>
				</div>
			</div>
		</form>
			<div class="col s12 center">
				<button id="login" class="btn waves-effect waves-light">登录</button>
<!--				<button type="reset" class="btn waves-effect waves-light">清空</button>-->
			</div>
</fieldset>

<?php
//公用尾部页面
require_once './Public/static/header/footer.php';
?>
