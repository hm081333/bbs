<?php
require_once './Public/static/header/header.php';
?>

<h3 class="center"><?php echo $class['name']; ?>区</h3>

<table>
	<thead>
	<tr class="teal darken-3">
		<th width="44%">帖子</th>
		<th width="12%">访问</th>
		<th width="12%">回复</th>
		<th width="32%">发表日期</th>
	</tr>
	</thead>

	<?php
	//循环输出输出记录列表
	foreach ($rows as $row)
	{
	?>

	<tbody>
	<tr class="green accent-1">
		<td>

			<?php
			//如果是“置顶”的记录
			if ($row['sticky'] == "1") {
				?><i class="material-icons">stars</i><?php
			}
			?>
			<a href="?service=Topic.topic&topic_id=<?php echo $row['id']; ?>"><?php echo $row['topic']; ?></a><br/><?php echo '发帖者: ' . $row['name'] ?>
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
	<?php
	} //退出while循环
	?>


	<tr>
		<td colspan="4">
			<?php
			//上一页
			if ($page > 1) {
				?>
				<a class="btn waves-effect waves-light" href="?service=Topic.topic_List&class_id=<?php echo $class['id']; ?>&page=<?php echo($page - 1) ?>"><i
							class="material-icons">arrow_back</i></a>
				<?php

			} else {
				?>
				<a class="disabled btn waves-effect waves-light"><i class="material-icons">arrow_back</i></a>
				<?php

			}
			//后一页
			if (($page * each_page) < $total) {
				?>
				<a class="btn waves-effect waves-light" href="?service=Topic.topic_List&class_id=<?php echo $class['id']; ?>&page=<?php echo($page + 1) ?>"><i
							class="material-icons">arrow_forward</i></a>
				<?php

			} else {
				?>
				<a class="disabled btn waves-effect waves-light"><i class="material-icons">arrow_forward</i></a>
				<?php

			}
			?>

			<a class="btn right waves-effect waves-light" onClick="location.href='?service=Topic.create_Topic'">发帖</a>
		</td>
	</tr>
	</tbody>
</table>

<?php
//公用尾部页面
require_once './Public/static/header/footer.php';
?>
