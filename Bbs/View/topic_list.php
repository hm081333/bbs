<h3 class="center"><?php echo T($class['name'] . '区'); ?></h3>

<table>
	<thead>
	<tr class="teal darken-3">
		<th width="44%"><?php echo T('帖子'); ?></th>
		<th width="12%"><?php echo T('访问'); ?></th>
		<th width="12%"><?php echo T('回复'); ?></th>
		<th width="32%"><?php echo T('发表日期'); ?></th>
	</tr>
	</thead>

	<?php foreach ($rows

	as $row)://循环输出输出记录列表      ?>

	<tbody>
	<tr class="green accent-1">
		<td>

			<?php
			//如果是“置顶”的记录
			if ($row['sticky'] == "1") {
				?><i class="material-icons">stars</i><?php
			}
			?>
			<a href="?service=Topic.topic&topic_id=<?php echo $row['id']; ?>"><?php echo T($row['topic']); ?></a><br/><?php echo T('发帖者: ' . $row['name']) ?>
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
	</tr>
	<?php endforeach; //退出while循环?>
	</tbody>
</table>
<ul class="pagination">
	<?php if ($page > 1) : //上一页 ?>
		<li class="waves-effect">
			<a href="?service=Topic.topic_List&class_id=<?php echo $class['id']; ?>&page=<?php echo($page - 1); ?>">
				<i class="material-icons">chevron_left</i>
			</a>
		</li>
	<?php else: ?>
		<li class="disabled">
			<a href="#!">
				<i class="material-icons">chevron_left</i>
			</a>
		</li>
	<?php endif; ?>
	<?php $total_page = ceil($total / each_page); ?>
	<?php for ($i = 1; $i <= $total_page; $i++): ?>
		<?php if ($i == $page): ?>
			<li class="active">
			<a href="#!">
		<?php else: ?>
			<li class="waves-effect">
			<a href="?service=Topic.topic_List&class_id=<?php echo $class['id']; ?>&page=<?php echo $i; ?>">
		<?php endif; ?>
		<?php echo $i; ?>
		</a>
		</li>
	<?php endfor; ?>
	<?php if (($page * each_page) < $total) : //后一页 ?>
		<li class="waves-effect">
			<a href="?service=Topic.topic_List&class_id=<?php echo $class['id']; ?>&page=<?php echo($page + 1); ?>">
				<i class="material-icons">chevron_right</i>
			</a>
		</li>
	<?php else: ?>
		<li class="disabled">
			<a href="#!">
				<i class="material-icons">chevron_right</i>
			</a>
		</li>
	<?php endif; ?>
</ul>