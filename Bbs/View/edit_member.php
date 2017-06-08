<?php require_once './Public/static/header/header.php'; ?>

<h3 class="center"><?php echo T('编辑个人资料'); ?></h3>

<fieldset>
	<legend><?php echo T('个人资料'); ?></legend>
	<br/>

	<table width="100%">
		<form id="edit_member" method="post" onsubmit="return false;">
			<input name="service" type="hidden" value="User.edit_Member">
			<input name="action" type="hidden" value="post">
			<input name="user_id" type="hidden" value="<?php echo $user['id']; ?>">
			<tr>
				<td><?php echo T('登录用户:'); ?></td>
				<td><b><?php echo $user['username']; ?></b></td>
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
				<td><input name="realname" type="text" value="<?php echo $user['realname']; ?>" class="validate"></td>
			</tr>
			<tr>
				<td colspan="2" class="center">
					<button type="submit" name="submit"
							class="btn waves-effect waves-light"><?php echo T('更新'); ?></button>
				</td>
			</tr>
		</form>
	</table>

</fieldset>

<?php require_once './Public/static/header/footer.php'; ?>
