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
	</tbody>
</table>

<!--<div class="valign-wrapper">-->
<ul class="pagination">
	<?php if ($page > 1) : //上一页 ?>
		<li class="waves-effect">
			<a href="./?page=<?php echo($page - 1); ?>">
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
			<a href="./?page=<?php echo $i; ?>">
		<?php endif; ?>
		<?php echo $i; ?>
		</a>
		</li>
	<?php endfor; ?>
	<?php if (($page * each_page) < $total) : //后一页 ?>
		<li class="waves-effect">
			<a href="./?page=<?php echo($page + 1); ?>">
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
<!--</div>-->
