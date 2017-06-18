<?php require_once './Public/static/header/header_admin.php'; ?>

<h3 class="center"><?php echo T('配置短信宝') ?></h3>

<fieldset>
	<legend><?php echo T('短信设置') ?></legend>
	<div class="row">
		<div class="col s12" style="color: red; font-size: large; font-weight: bold;">
			<?php echo $smsbao_query[0] == 0 ? T('该账号剩余条数：') . $smsbao_query[2] : $smsbao_query['msg']; ?>
		</div>
	</div>
	<div class="row">
		<form id="config" method="post" onsubmit="return false;" class="col s12">
			<input name="action" value="post" type="hidden">
			<input name="service" value="Default.smsbao_config" type="hidden">
			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">account_box</i>
					<input id="user_name" name="username" type="text" value="<?php echo $smsbao['username']; ?>"/>
					<label for="user_name"><?php echo T('用户名') ?></label>
				</div>
			</div>
			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">vpn_key</i>
					<input id="password" name="password" type="password" value="<?php echo $smsbao['password']; ?>"/>
					<label for="password"><?php echo T('密码') ?></label>
				</div>
			</div>
			<div class="col s12 center">
				<button type="submit" name="submit" class="btn waves-effect waves-light"><?php echo T('提交') ?></button>
			</div>
		</form>
</fieldset>

<?php require_once './Public/static/header/footer.php'; ?>
