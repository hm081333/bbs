<?php require_once './Public/static/header/header_admin.php'; ?>

<h3 class="center"><?php echo T('添加管理员'); ?></h3>

<fieldset>
	<legend><?php echo T('添加管理员'); ?></legend>
	<div class="row">
		<form id="Register" method="post" onsubmit="return false;" class="col s12">
			<input name="service" value="User.create_admin" type="hidden">
			<input name="action" value="post" type="hidden">
			<!--<input name="reg" value="admin" type="hidden">-->
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

			<div class="center">
				<div class="switch">
					<label>
						<b><?php echo T('普通权限'); ?></b>
						<input type="checkbox" name="auth">
						<span class="lever"></span>
						<b><?php echo T('全权限'); ?></b>
					</label>
				</div>
			</div>
			<div class="col s12 center">
				<button type="submit" name="submit"
						class="btn waves-effect waves-light"><?php echo T('添加管理员'); ?></button>
			</div>
		</form>
</fieldset>

<?php require_once './Public/static/header/footer.php'; ?>
