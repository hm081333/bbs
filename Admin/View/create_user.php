<?php require_once './Public/static/header/header_admin.php'; ?>

<!--<script src="./Public/static/js/reg.js"></script>-->

<h3 class="center"><?php echo T('添加用户'); ?></h3>

<fieldset>
	<legend><?php echo T('添加用户'); ?></legend>
	<div class="row">
		<form id="Register" method="post" onsubmit="return false;" class="col s12">
			<input name="service" value="User.register" type="hidden">
			<input name="action" value="post" type="hidden">
			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">account_box</i>
					<input id="username" name="username" type="text" class="validate">
					<label for="username"><?php echo T('用户名'); ?></label>
				</div>
			</div>

			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">vpn_key</i>
					<input id="password" name="password" type="password" class="validate">
					<label for="password"><?php echo T('密码'); ?></label>
				</div>
			</div>

			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">email</i>
					<input id="email" name="email" type="text">
					<label for="email"><?php echo T('邮箱'); ?></label>
				</div>
			</div>

			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">face</i>
					<input id="realname" name="realname" type="text">
					<label for="realname"><?php echo T('名字'); ?></label>
				</div>
			</div>

			<div class="center">
				<div class="switch">
					<label>
						<b><?php echo T('普通用户'); ?></b>
						<input type="checkbox" name="auth">
						<span class="lever"></span>
						<b><?php echo T('权限用户'); ?></b>
					</label>
				</div>
			</div>
			<div class="col s12 center">
				<button type="submit" name="submit"
						class="btn waves-effect waves-light"><?php echo T('添加用户'); ?></button>
				<button type="reset" class="btn waves-effect waves-light">重新输入</button>
			</div>
		</form>
</fieldset>

<?php require_once './Public/static/header/footer.php'; ?>
