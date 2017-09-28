<h3 class="center-align"><?php echo T('贴吧列表'); ?></h3>
<fieldset>
	<legend><?php echo T('贴吧列表') ?></legend>
	<ul class="tabs">
		<?php foreach ($tiebas as $key => $item): ?>
			<li class="tab"><a href="#tieba<?php echo $key; ?>"><?php echo $item['name']; ?></a></li>
		<?php endforeach; ?>
	</ul>


	<?php foreach ($tiebas as $key => $item): ?>
		<div id="tieba<?php echo $key; ?>">
			<table style="table-layout: fixed;">
				<thead>
				<tr class="teal darken-3">
					<th><?php echo T('ID'); ?></th>
					<th><?php echo T('贴吧'); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php if (empty($item['tieba'])) : ?>
					<tr class="green accent-1">
						<td>
							暂无
						</td>
						<td>
							暂无
						</td>
					</tr>
				<?php else: ?>
					<?php foreach ($item['tieba'] as $index => $row) : ?>
						<tr class="green accent-1">
							<td>
								<?php echo $row['fid']; ?>
							</td>
							<td>
							<span class="truncate tooltipped" data-position="bottom" data-delay="50"
								  data-tooltip="<?php echo T($row['tieba']); ?>">
								<?php echo T($row['tieba']); ?>
							</span>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				<tr>
					<td colspan="2">
						<button class="btn waves-effect waves-light right" onclick="refresh_tieba(<?php echo $key; ?>)">
							刷新
						</button>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
	<?php endforeach; ?>


</fieldset>