<h3 class="center"><?php echo T('南洋交流平台'); ?></h3>
<table>
	<thead>
	<tr class="teal darken-3">
		<th width="40%"><?php echo T('课程'); ?></th>
		<th><?php echo T('说明'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($rows as $key => $row) : ?>
		<tr class="green accent-1">
			<td>
				<i class="material-icons">label</i><a class="brown-text"
													  href="?service=Topic.topic_List&class_id=<?php echo $row['id']; ?>&page=1"><b><?php echo T($row['name']); ?></b></a>
			</td>
			<td>
				<?php echo T($row['tips']); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	<tr>
		<td colspan="2">

			<?php
			//上一页
			if ($page > 1) :
				?>
				<a class="btn waves-effect waves-light" href="./?page=<?php echo($page - 1) ?>">
					<i class="material-icons">arrow_back</i></a>
			<?php else: ?>
				<a class="disabled btn waves-effect waves-light"><i class="material-icons">arrow_back</i></a>
			<?php endif;
			//后一页
			if (($page * each_page) < $total) :
				?>
				<a class="btn waves-effect waves-light" href="./?page=<?php echo($page + 1) ?>">
					<i class="material-icons">arrow_forward</i></a>
			<?php else: ?>
				<a class="disabled btn waves-effect waves-light"><i class="material-icons">arrow_forward</i></a>
			<?php endif; ?>

			<a class="btn right waves-effect waves-light"
			   onClick="location.href='?service=Topic.create_Topic'"><?php echo T('发帖'); ?></a>

		</td>
	</tr>
	</tbody>
</table>