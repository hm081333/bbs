<?php require_once './Public/static/header/header.php'; ?>
<!--<script src="./Public/static/js/login.js"></script>-->
<h3 class="center"><?php echo T('忘记密码'); ?></h3>

<fieldset>
	<legend>
		<?php
		if ($type == 0) : echo T('谷歌身份认证');
		elseif ($type == 1) : echo T('短信验证码');
		elseif ($type == 2) : echo T('邮件验证码');
		endif;
		?>
	</legend>
	<div class="row">
		<form id="forget" method="post" onsubmit="return false;" class="col s12">
			<input name="service" value="User.forget" type="hidden"/>
			<input name="type" value="<?php echo $type; ?>" type="hidden"/>
			<input name="action" value="post" type="hidden"/>
			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">account_box</i>
					<input id="user_name" name="user_name" type="text"/>
					<label for="user_name"><?php echo T('用户名'); ?></label>
				</div>
			</div>
			<?php if ($type == 1) : ?>
				<div class="col s12">
					<div class="input-field right">
						<a onclick="" class="btn-floating waves-effect waves-light"><i class="material-icons">send</i></a>
					</div>
					<div class="input-field" style="width: 83%;">
						<i class="material-icons prefix">smartphone</i>
						<input id="phone" name="phone" type="text"/>
						<label for="phone"><?php echo T('手机号码'); ?></label>
					</div>
				</div>
			<?php elseif ($type == 2) : ?>
				<div class="col s12">
					<div class="input-field right">
						<a onclick="" class="btn-floating waves-effect waves-light"><i class="material-icons">send</i></a>
					</div>
					<div class="input-field" style="width: 83%;">
						<i class="material-icons prefix">email</i>
						<input id="email" name="email" type="text"/>
						<label for="email"><?php echo T('邮箱地址'); ?></label>
					</div>
				</div>
			<?php endif; ?>
			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">verified_user</i>
					<input id="code" name="code" type="text"/>
					<label for="code"><?php echo T('验证码'); ?></label>
				</div>
			</div>
			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">vpn_key</i>
					<input id="password" name="password" type="password"/>
					<label for="password"><?php echo T('新密码'); ?></label>
				</div>
			</div>
			<div class="col s12 center">
				<button type="submit" name="submit" class="btn waves-effect waves-light"><?php echo T('确定') ?></button>
				<!--<button type="reset" class="btn waves-effect waves-light">清空</button>-->
			</div>
		</form>
</fieldset>

<?php require_once './Public/static/header/footer.php'; ?>
