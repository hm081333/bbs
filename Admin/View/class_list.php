<h3 class="center"><?php echo T('管理课程分类'); ?></h3>
<fieldset>
	<legend><?php echo T('管理课程'); ?></legend>
	<table>
		<thead>
		<th width="25%"><?php echo T('课程'); ?>
		</td>
		<th><?php echo T('课程说明'); ?></th>
		<th width="8%"><?php echo T('操作'); ?></th>
		</thead>
		<tbody>
		<?php foreach ($rows as $key => $row) : ?>
			<tr>
				<td>
					<input id="name<?php echo $row['id']; ?>" name="name<?php echo $row['id']; ?>" type="text"
						   value="<?php echo T($row['name']); ?>">
				</td>
				<td>
					<input id="tips<?php echo $row['id']; ?>" name="tips<?php echo $row['id']; ?>" type="text"
						   value="<?php echo T($row['tips']); ?>">
				</td>
				<td>
					<button onclick="update_Class(<?php echo $row['id']; ?>)"
							class="btn-floating waves-effect waves-light"><i class="material-icons">edit</i></button>

					<button onclick="delete_Class(<?php echo $row['id']; ?>)"
							class="btn-floating waves-effect waves-light"><i class="material-icons">delete</i>
					</button>
				</td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td colspan="3">
				<?php //上一页
				if ($page > 1) {
					?>
					<a class="btn waves-effect waves-light"
					   href="?service=Class.class_List&page=<?php echo($page - 1) ?>">
						<i class="material-icons">arrow_back</i></a>
					<?php
				} else {
					?>
					<a class="disabled btn waves-effect waves-light"><i class="material-icons">arrow_back</i></a>
					<?php
				}//后一页
				if (($page * each_page) < $total) {
					?>
					<a class="btn waves-effect waves-light"
					   href="?service=Class.class_List&page=<?php echo($page + 1) ?>">
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