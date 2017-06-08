<?php require_once './Public/static/header/header_admin.php'; ?>

	<h3 class="center"><?php echo T('管理帖子'); ?></h3>

	<table>
		<thead>
		<tr class="teal darken-3">
			<th width="31%"><?php echo T('帖子'); ?></th>
			<th width="15%"><?php echo T('课程'); ?></th>
			<th width="12%"><?php echo T('访问'); ?></th>
			<th width="12%"><?php echo T('回复'); ?></th>
			<th width="25%"><?php echo T('发表日期'); ?></th>
			<th width="5%"><?php echo T('操作'); ?></th>
		</tr>
		</thead>

		<?php foreach ($rows as $key => $row) : ?>

		<tbody>
		<tr class="green accent-1">
			<td>

				<?php
				//如果是“置顶”的记录
				if ($row['sticky'] == "1") {
					?><i class="material-icons">stars</i><?php
				}
				?>
				<a href="?service=Topic.topic&topic_id=<?php echo $row['id']; ?>"><?php echo T($row['topic']); ?></a><br/><?php echo T('发帖者: ') . $row['name'] ?>
			</td>
			<td>
				<?php
				echo T($row['class_name']);
				?>
			</td>
			<td>
				<?php
				echo $row['view'];  //浏览量
				?>
			</td>
			<td>
				<?php
				echo $row['reply'];  //回复量
				?>
			</td>
			<td>
				<?php
				echo $row['datetime'];  //日期
				?>
			</td>
			<td>
				<!--显示置顶操作按钮-->
				<?php if ($row['sticky'] == 0) : ?>
					<button onclick="stick_topic(<?php echo $row['id']; ?>)"
							class="btn-floating waves-effect waves-light"><i class="material-icons">publish</i></button>
				<?php else: ?>
					<button onclick="unstick_topic(<?php echo $row['id']; ?>)"
							class="btn-floating waves-effect waves-light"><i class="material-icons">stars</i></button>
				<?php endif; ?>
				<button onclick="delete_topic(<?php echo $row['id']; ?>)" class="btn-floating waves-effect waves-light">
					<i class="material-icons">delete</i></button>
			</td>
		</tr>
		<?php endforeach; ?>
		<tr>
			<td colspan="6">
				<?php //上一页
				if ($page > 1) {
					?>
					<a class="btn waves-effect waves-light"
					   href="?service=Topic.topic_List&page=<?php echo($page - 1) ?>"><i
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
					   href="?service=Topic.topic_List&page=<?php echo($page + 1) ?>"><i
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