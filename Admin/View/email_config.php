<?php require_once './Public/static/header/header_admin.php'; ?>

<h3 class="center"><?php echo T('配置邮箱') ?></h3>

<fieldset>
	<legend><?php echo T('邮箱设置') ?></legend>
	<div class="row">
		<form id="email_config" method="post" onsubmit="return false;" class="col s12">
			<input name="action" value="post" type="hidden">
			<input name="service" value="Default.email_config" type="hidden">
			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">account_box</i>
					<input id="host" name="host" type="text" value="<?php echo $email_config['host']; ?>"/>
					<label for="host"><?php echo T('SMTP服务器') ?></label>
				</div>
			</div>
			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">account_box</i>
					<input id="username" name="username" type="text" value="<?php echo $email_config['username']; ?>"/>
					<label for="username"><?php echo T('SMTP服务器用户名') ?></label>
				</div>
			</div>
			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">vpn_key</i>
					<input id="password" name="password" type="password" value="<?php echo $email_config['password']; ?>"/>
					<label for="password"><?php echo T('SMTP服务器密码') ?></label>
				</div>
			</div>
			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">account_box</i>
					<input id="from" name="from" type="text" value="<?php echo $email_config['from']; ?>"/>
					<label for="from"><?php echo T('设置发件人地址') ?></label>
				</div>
			</div>
			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">account_box</i>
					<input id="fromName" name="fromName" type="text" value="<?php echo $email_config['fromName']; ?>"/>
					<label for="fromName"><?php echo T('设置发件人名称') ?></label>
				</div>
			</div>
			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">account_box</i>
					<input id="sign" name="sign" type="text" value="<?php echo $email_config['sign']; ?>"/>
					<label for="sign"><?php echo T('设置邮件签名') ?></label>
				</div>
			</div>
			<div class="col s12 center">
				<button type="submit" name="submit" class="btn waves-effect waves-light"><?php echo T('提交') ?></button>
			</div>
		</form>
</fieldset>

<?php require_once './Public/static/header/footer.php'; ?>
