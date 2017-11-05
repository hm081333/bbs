<h3 class="center-align"><?php echo T('绑定BDUSS列表'); ?></h3>
<table style="table-layout: fixed;">
	<thead>
	<tr class="teal darken-3">
		<th><?php echo T('BDUSS'); ?></th>
		<th><?php echo T('贴吧用户名'); ?></th>
		<th><?php echo T('上次刷新时间'); ?></th>
		<th><?php echo T('操作'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($rows as $key => $row) : ?>
		<tr class="green accent-1">
			<td>
				<span class="truncate tooltipped" data-position="bottom" data-delay="50"
					  data-tooltip="<?php echo T($row['bduss']); ?>">
					<?php echo T($row['bduss']); ?>
				</span>
			</td>
			<td>
				<span class="truncate tooltipped" data-position="bottom" data-delay="50"
					  data-tooltip="<?php echo T($row['name']); ?>">
					<?php echo T($row['name']); ?>
				</span>
			</td>
			<td>
				<?php echo date('Y-m-d H:i:s', $row['refresh_time']); ?>
			</td>
			<td>
				<button data-id="<?php echo $row['id']; ?>" data-service="Tieba.DeleteBaiduId"
						class="delete btn-floating waves-effect waves-light">
					<i class="material-icons">delete</i>
				</button>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
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
