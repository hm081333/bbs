<h3 class="center"><?php echo T('查看会员资料'); ?></h3>

<fieldset>
	<legend><?php echo T('个人资料') ?></legend>
	<table width="100%">
		<tr>
			<td width="30%"><?php echo T('会员：') ?></td>
			<td width="70%"><b><?php echo $user['info']['user_name']; ?></b></td>
		</tr>
		<tr>
			<td width="30%"><?php echo T('真实姓名：') ?></td>
			<td width="70%"><?php echo $user['info']['real_name']; ?></td>
		</tr>
		<tr>
			<td><?php echo T('电子邮件：') ?></td>
			<td><?php echo $user['info']['email']; ?></td>
		</tr>
		<tr>
			<td><?php echo T('发贴数量：') ?></td>
			<td><?php echo $user['topic_count']; ?></td>
		</tr>
		<tr>
			<td><?php echo T('回复数量：') ?></td>
			<td><?php echo $user['reply_count']; ?></td>
		</tr>
	</table>
</fieldset>