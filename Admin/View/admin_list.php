<?php require_once './Public/static/header/header_admin.php'; ?>

	<h3 class="center"><?php echo T('管理管理员账号'); ?></h3>
	<fieldset>
	<legend><?php echo T('管理管理员'); ?></legend>
	<table>
		<thead>
		<th width="25%"><?php echo T('管理员'); ?>
		</th>
		<th><?php echo T('权限'); ?></th>
		<th><?php echo T('更改密码'); ?></th>
		<th width="5%"><?php echo T('操作'); ?></th>
		</thead>
		<tbody>
		<?php foreach ($rows as $key => $row) : ?>
			<tr>
				<td>
					<?php echo $row['username']; ?>
				</td>
				<td>
					<p>
						<input name="auth<?php echo $row['id']; ?>" type="checkbox" class="filled-in"
							   id="auth<?php echo $row['id']; ?>" <?php echo $row['auth'] == 1 ? 'checked' : ''; ?> />
						<label for="auth<?php echo $row['id']; ?>"></label>
					</p>
				</td>
				<td>
					<input id="password<?php echo $row['id']; ?>"
						   placeholder="<?php T('密码留空，将不被更新'); ?>" name="<?php echo 'password' . $row['id']; ?>"
						   type="password">
				</td>
				<td>
					<button onclick="update_admin(<?php echo $row['id']; ?>)"
							class="btn-floating waves-effect waves-light">
						<i class="material-icons">edit</i>
					</button>
					<button onclick="delete_admin(<?php echo $row['id']; ?>)"
							class="btn-floating waves-effect waves-light">
						<i class="material-icons">delete</i>
					</button>
				</td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td colspan="5">
				<?php //上一页
				if ($page > 1) {
					?>
					<a class="btn waves-effect waves-light"
					   href="?service=User.admin_list&page=<?php echo($page - 1) ?>"><i
								class="material-icons">arrow_back</i></a>
					<?php
				} else {
					?>
					<a class="disabled btn waves-effect waves-light"><i class="material-icons">arrow_back</i></a>
					<?php
				}//后一页
				if (($page * each_page) < $total) {
					?>
					<a class="btn waves-effect waves-light"
					   href="?service=User.admin_list&page=<?php echo($page + 1) ?>"><i
								class="material-icons">arrow_forward</i></a>
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

<?php require_once './Public/static/header/footer.php'; ?>