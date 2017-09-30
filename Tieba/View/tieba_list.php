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
					<!--<th>--><?php //echo T('ID'); ?><!--</th>-->
					<th><?php echo T('贴吧'); ?></th>
					<th><?php echo T('状态'); ?></th>
					<th><?php echo T('忽略'); ?></th>
					<th><?php echo T('上次签到'); ?></th>
					<th<!-- style="width: 3.5rem;"-->><?php echo T('删除'); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php if (empty($item['tieba'])) : ?>
					<tr class="green accent-1">
						<td colspan="5" class="center">
							暂无
						</td>
					</tr>
				<?php else: ?>
					<?php foreach ($item['tieba'] as $index => $row) : ?>
						<tr class="green accent-1">
							<!--<td>
								<?php /*echo $row['fid']; */ ?>
							</td>-->
							<td>
								<span class="truncate tooltipped" data-position="bottom" data-delay="50"
									  data-tooltip="<?php echo T($row['tieba']); ?>">
									<?php echo T($row['tieba']); ?>
								</span>
							</td>
							<td>
								<?php if ($row['status'] == 0): ?>
									<span style="color: blue;">正常</span>
								<?php else: ?>
									<span style="color: red;">异常</span>
									<br/>
									<span style="color: red;"><?php echo $row['last_error']; ?></span>
								<?php endif; ?>
							</td>
							<td>
								<div class="switch">
									<label>
										<input id="no_sign<?php echo $row['id']; ?>"
											   onclick="no_sign(<?php echo $row['id']; ?>)"
											   type="checkbox" <?php echo $row['no'] ? 'checked' : ''; ?>>
										<span class="lever"></span>
									</label>
								</div>
							</td>
							<td>
								<?php echo date('Y-m-d H:i:s', $row['latest']); ?>
							</td>
							<td>
								<button onclick="sign_tieba(<?php echo $row['id']; ?>)" class="btn-floating waves-effect waves-light">
									<i class="material-icons">refresh</i>
								</button>
								<button onclick="delete_tieba(<?php echo $row['id']; ?>)" class="btn-floating waves-effect waves-light">
									<i class="material-icons">delete</i>
								</button>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				<tr>
					<td colspan="5" class="center">
						<button class="btn waves-effect waves-light" onclick="sign_baiduid(<?php echo $key; ?>)">
							签到当前贴吧
						</button>
						<button class="btn waves-effect waves-light" onclick="refresh_tieba(<?php echo $key; ?>)">
							刷新列表
						</button>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
	<?php endforeach; ?>


</fieldset>