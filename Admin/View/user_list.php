<h3 class="center"><?php echo T('管理用户') ?></h3>
<fieldset>
	<legend><?php echo T('管理用户') ?></legend>
	<table>
		<thead>
		<tr>
			<th><?php echo T('用户名') ?></th>
			<th><?php echo T('权限') ?></th>
			<th><?php echo T('邮箱') ?></th>
			<th><?php echo T('名字') ?></th>
			<th><?php echo T('更改密码') ?></th>
			<th width="5%"><?php echo T('操作') ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($rows as $key => $row) : ?>
			<tr>
				<td>
					<?php echo $row['user_name']; ?>
				</td>
				<td>
					<p>
						<input id="auth<?php echo $row['id']; ?>" name="auth<?php echo $row['id']; ?>" type="checkbox"
							   class="filled-in" <?php echo $row['auth'] == 1 ? 'checked' : ''; ?>/>
						<label for="auth<?php echo $row['id']; ?>"></label>
					</p>
				</td>
				<td>
					<input id="email<?php echo $row['id']; ?>" name="email<?php echo $row['id']; ?>" type="text"
						   value="<?php echo $row['email']; ?>">
				</td>
				<td>
					<input id="real_name<?php echo $row['id']; ?>" name="real_name<?php echo $row['id']; ?>" type="text"
						   value="<?php echo $row['real_name']; ?>">
				</td>
				<td>
					<input id="password<?php echo $row['id']; ?>" placeholder="密码留空，将不被更新"
						   name="password<?php echo $row['id']; ?>" type="password">
				</td>
				<td>
					<button onclick="update_user(<?php echo $row['id']; ?>)"
							class="btn-floating waves-effect waves-light"><i class="material-icons">edit</i></button>
					<button data-id="<?php echo $row['id']; ?>" data-service="User.delete_User"
							class="delete btn-floating waves-effect waves-light"><i class="material-icons">delete</i>
					</button>
				</td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td colspan="5">
				<?php //上一页
				if ($page > 1) {
					?>
					<a class="btn waves-effect waves-light" href="?page=<?php echo($page - 1) ?>">
						<i class="material-icons">arrow_back</i></a>
					<?php
				} else {
					?>
					<a class="disabled btn waves-effect waves-light"><i class="material-icons">arrow_back</i></a>
					<?php
				}//后一页
				if (($page * each_page) < $total) {
					?>
					<a class="btn waves-effect waves-light" href="?page=<?php echo($page + 1) ?>">
						<i class="material-icons">arrow_forward</i></a>
					<?php
				} else {
					?>
					<a class="disabled btn waves-effect waves-light"><i class="material-icons">arrow_forward</i></a>
					<?php
				}
				?>
			</td>
		</tr>
		</tbody>
	</table>
</fieldset>