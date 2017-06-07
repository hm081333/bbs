<?php require_once './Public/static/header/header.php'; ?>

	<!--<script src="./Public/static/js/reg.js"></script>-->

	<h3 class="center"><?php echo T('注册'); ?></h3>

	<fieldset>
		<legend><?php echo T('注册'); ?></legend>
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
					<div>
						<p class="msg right"><i class="material-icons">warning</i><?php echo T('请输入6-26个字符'); ?></p>
					</div>
				</div>

				<div class="col s12">
					<div class="input-field">
						<i class="material-icons prefix">vpn_key</i>
						<input id="password" name="password" type="password" class="validate">
						<label for="password"><?php echo T('密码'); ?></label>
					</div>
					<div>
						<p class="msg right"><?php echo T('密码长度不大于16'); ?></p>
					</div>
				</div>

				<div class="col s12">
					<div class="input-field">
						<i class="material-icons prefix">vpn_key</i>
						<input id="password" name="password" type="password" class="validate" disabled="">
						<label for="password"><?php echo T('确认密码'); ?></label>
					</div>
					<div>
						<p class="msg right"><?php echo T('两次密码应当一致'); ?></p>
					</div>
				</div>

				<div class="col s12">
					<div class="input-field">
						<i class="material-icons prefix">email</i>
						<input id="email" name="email" type="email" class="validate">
						<label for="email">E-mail</label>
					</div>
					<div>
						<p class="msg right"><i class="material-icons">warning</i><?php echo T('请输入6-26个字符'); ?></p>
					</div>
				</div>

				<div class="col s12">
					<div class="input-field">
						<i class="material-icons prefix">face</i>
						<input id="realname" name="realname" type="text" class="validate">
						<label for="realname"><?php echo T('名字'); ?></label>
					</div>
					<div>
						<p class="msg right"><?php echo T('昵称应当唯一'); ?></p>
					</div>
				</div>
				<div class="col s12">
					<div class="input-field">
						<i class="material-icons prefix">date_range</i>
						<input id="birthdate" name="birthdate" type="date" class="datepicker">
						<label for="birthdate"><?php echo T('生日'); ?></label>
					</div>
				</div>
				<div class="col s12 center">
					<button type="submit" name="submit" class="btn waves-effect waves-light"><?php echo T('提交注册'); ?></button>
					<!--				<button type="reset" class="btn waves-effect waves-light">重新输入</button>-->
				</div>
			</form>
	</fieldset>

	<script>
		$('.datepicker').pickadate({
			selectMonths: true, // Creates a dropdown to control month
			selectYears: 15 // Creates a dropdown of 15 years to control year
		});
	</script>

<?php require_once './Public/static/header/footer.php'; ?>