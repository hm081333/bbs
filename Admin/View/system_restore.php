<h3 class="center"><?php echo T('系统重置') ?></h3>

<fieldset>
	<legend><?php echo T('系统重置') ?></legend>
	<div class="row">
		<form id="Restore" method="post" onsubmit="return false;" class="col s12">
			<input name="action" value="post" type="hidden">
			<input name="service" value="System.restore" type="hidden">
			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">vpn_key</i>
					<input id="password" name="password" type="password">
					<label for="password"><?php echo T('请输入密码') ?></label>
				</div>
			</div>
			<div class="col s12 center">
				<button type="submit" name="submit" class="btn waves-effect waves-light"><?php echo T('确定') ?></button>
			</div>
		</form>
</fieldset>