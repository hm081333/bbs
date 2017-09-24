<h3 class="center"><?php echo T('系统备份还原') ?></h3>
<fieldset>
	<legend><?php echo T('系统备份还原') ?></legend>
	<div class="row">
		<div class="col s12 center">
			<ul class="collapsible" data-collapsible="accordion">
				<?php foreach ($files as $key => $file): ?>
					<?php if (is_array($file)): ?>
						<li>
							<div class="collapsible-header">
								<?php echo $key; ?>
							</div>
							<div class="collapsible-body" style="padding: 0;">
								<?php foreach ($file as $min): ?>
									<a restore data-name="<?php echo $key . '/' . $min; ?>" href="javascript:;"
									   style="display: block; padding: 0.6rem;">
										<?php echo $min; ?>
									</a>
								<?php endforeach; ?>
							</div>
						</li>
					<?php else: ?>
						<li style="height: 3rem; line-height: 3rem; padding: 0 1rem;">
							<a restore data-name="<?php echo $file; ?>" href="javascript:;">
								<?php echo $file; ?>
							</a>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="col s12 center">
			<button class="btn waves-effect waves-light modal-trigger" data-target="BackupModal">
				<?php echo T('备份'); ?>
			</button>
		</div>
</fieldset>
<!-- 备份数据库弹窗 -->
<div id="BackupModal" class="modal">
	<div class="modal-content center">
		<h4>备份数据库</h4>
		<p>备份说明</p>
		<form method="post" onsubmit="return false;">
			<input type="hidden" name="service" value="System.backup">
			<input type="hidden" name="action" value="post">
			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">vpn_key</i>
					<input id="password" name="password" type="password">
					<label for="password"><?php echo T('请输入密码') ?></label>
				</div>
			</div>
			<button type="submit" class="btn waves-effect waves-light"><?php echo T('确定'); ?></button>
			<button onclick="$('#BackupModal').modal('close')"
					class="btn waves-effect waves-light"><?php echo T('取消'); ?></button>
		</form>
	</div>
</div>

<div id="RestoreModal" class="modal">
	<div class="modal-content center">
		<h4>恢复数据</h4>
		<p>恢复说明</p>
		<form method="post" onsubmit="return false;">
			<input type="hidden" name="service" value="System.restore">
			<input type="hidden" name="name" value="">
			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">vpn_key</i>
					<input id="password" name="password" type="password">
					<label for="password"><?php echo T('请输入密码') ?></label>
				</div>
			</div>
			<button type="submit" class="btn waves-effect waves-light"><?php echo T('确定'); ?></button>
			<button type="reset" onclick="$('#RestoreModal').modal('close')"
					class="btn waves-effect waves-light"><?php echo T('取消'); ?></button>
		</form>
	</div>
</div>