<?php require_once './Public/static/header/header_admin.php'; ?>

<!--<script src="./Public/static/js/reg.js"></script>-->

<h3 class="center">添加用户</h3>

<fieldset>
	<legend>Add User</legend>
	<div class="row">
		<form id="Register" class="col s12">
			<input name="action" value="post" type="hidden">
			<input name="service" value="User.register" type="hidden">
			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">account_box</i>
					<input id="username" name="username" type="text" class="validate">
					<label for="username">用户名</label>
				</div>
			</div>

			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">vpn_key</i>
					<input id="password" name="password" type="password" class="validate">
					<label for="password">密码</label>
				</div>
			</div>

			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">email</i>
					<input id="email" name="email" type="text">
					<label for="email">E-mail</label>
				</div>
			</div>

			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">face</i>
					<input id="realname" name="realname" type="text">
					<label for="realname">名字</label>
				</div>
			</div>

			<div class="center">
				<div class="switch">
					<label>
						<b>普通用户</b>
						<input type="checkbox" name="auth">
						<span class="lever"></span>
						<b>权限用户</b>
					</label>
				</div>
			</div>

		</form>

		<div class="col s12 center">
			<button onclick="register()" class="btn waves-effect waves-light">添加用户</button>
<!--			<button type="reset" class="btn waves-effect waves-light">重新输入</button>-->
		</div>

</fieldset>

<?php require_once './Public/static/header/footer.php'; ?>
