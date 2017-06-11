<?php require_once './Public/static/header/header.php'; ?>

<h3 class="center"><?php echo T('编辑个人资料'); ?></h3>

<fieldset>
	<legend><?php echo T('个人资料'); ?></legend>
	<br/>
	<form id="edit_member" method="post" onsubmit="return false;">
		<input name="service" type="hidden" value="User.edit_Member">
		<input name="action" type="hidden" value="post">
		<input name="user_id" type="hidden" value="<?php echo $user['id']; ?>">
		<input name="secret" type="hidden" value="<?php echo $secret; ?>">
		<input name="check" type="hidden" value="0">
		<table width="100%">
			<tr>
				<td><?php echo T('登录用户:'); ?></td>
				<td><b><?php echo $user['user_name']; ?></b></td>
			</tr>
			<tr>
				<td width="15%"><?php echo T('更新密码:'); ?></td>
				<td width="85%"><input placeholder="<?php echo T('密码留空，将不被更新'); ?>" name="password" type="password">
				</td>
			</tr>
			<tr>
				<td><?php echo T('电子邮件:'); ?></td>
				<td><input name="email" type="text" value="<?php echo $user['email']; ?>" class="validate"></td>
			</tr>
			<tr>
				<td><?php echo T('真实姓名:'); ?></td>
				<td><input name="real_name" type="text" value="<?php echo $user['real_name']; ?>" class="validate"></td>
			</tr>
			<tr>
				<td colspan="2" class="center">
					<a class="modal-trigger waves-effect waves-light btn"
					   href="#google_Auth"><?php echo isset($user['secret']) ? T('修改谷歌身份验证') : T('添加谷歌身份验证'); ?></a><br/>
					<button type="submit" name="submit"
							class="btn waves-effect waves-light"><?php echo T('更新'); ?></button>
				</td>
			</tr>
		</table>
	</form>

	<!-- Modal Structure -->
	<div id="google_Auth" class="modal">
		<div class="modal-content" style="text-align: center;">
			<h4><?php echo isset($user['secret']) ? T('修改谷歌身份验证') : T('添加谷歌身份验证'); ?></h4>
			<p><?php echo T('一、手机下载谷歌身份验证器'); ?></p>
			<p><?php echo T('二、扫描二维码'); ?></p>
			<p><?php echo T('三、输入扫描后出现的验证码'); ?></p>
			<img src="<?php echo $qrCodeUrl; ?>"/>
			<input type="text" name="code" placeholder="<?php echo T('请输入扫描后出现的验证码'); ?>">

			<a onclick="check_google_auth()" class="btn waves-effect waves-light"><?php echo T('验证'); ?></a>
		</div>
	</div>
</fieldset>

<?php require_once './Public/static/header/footer.php'; ?>
