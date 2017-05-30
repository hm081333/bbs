<?php require_once './Public/static/header/header.php'; ?>

<h3 class="center">编辑个人资料</h3>

<fieldset>
	<legend>个人资料</legend>
	<br/>

	<table width="100%">
		<form id="edit_member">
			<input name="action" type="hidden" value="post">
			<input name="user_id" type="hidden" value="<?php echo $user['id']; ?>">
			<tr>
				<td>登录用户:</td>
				<td><b><?php echo $user['username']; ?></b></td>
			</tr>
			<tr>
				<td width="15%">更新密码:</td>
				<td width="85%"><input placeholder="密码留空，将不被更新" name="password" type="password"></td>
			</tr>
			<tr>
				<td>电子邮件:</td>
				<td><input name="email" type="text" value="<?php echo $user['email']; ?>" class="validate"></td>
			</tr>
			<tr>
				<td>真实姓名:</td>
				<td><input name="realname" type="text" value="<?php echo $user['realname']; ?>" class="validate"></td>
			</tr>
		</form>
		<tr>
			<td colspan="2" class="center">
				<button id="submit" class="btn waves-effect waves-light">更新</button>
			</td>
		</tr>
	</table>

</fieldset>

<script>
	$("#submit").click(function () {
		$.ajax({
			type: 'POST',
			url: '?service=User.edit_Member',
			data: $("#edit_member").serialize(),
			success: function (d) {
				if (d.ret == 200) {
					Materialize.toast(d.msg, 2000, 'rounded', function () {
						// location.href = './';
//						history.back();
						location.reload();
					});
				} else {
					Materialize.toast(d.msg, 2000, 'rounded');
				}
			}
		});
	});
</script>

<?php require_once './Public/static/header/footer.php'; ?>
