<?php if (!isset($_SESSION['user_id'])) : //如果用户未登录，显示错误信息?>
	<h3 class="center"><?php echo T('您未登陆,没有发帖权限！'); ?></h3>
	<p class="center"><?php echo T('对不起，请'); ?><br/>
		<a class="btn waves-effect waves-light" href="?service=User.register"><?php echo T('注册'); ?></a><br/>
		<?php echo T('或者'); ?><br/>
		<a class="btn waves-effect waves-light" href="?service=User.login"><?php echo T('登录'); ?></a>
	</p>
<?php else : //如果用户登录，显示输入表单 ?>
	<fieldset>
		<legend><?php echo T('新帖子'); ?></legend>
		<div class="row">
			<form id="Create_Topic" enctype="multipart/form-data" method="post" onsubmit="return false;"
				  class="col s12">
				<input name="service" value="Topic.create_Topic" type="hidden">
				<input name="action" value="post" type="hidden">
				<input type="hidden" value="0" name="sticky">
				<div class="input-field col s12">
					<input name="topic" type="text" id="topic" class="validate" length="100">
					<label for="topic"><?php echo T('标题'); ?></label>
				</div>


				<div class="input-field col s12">
					<textarea name="detail" id="detail" class="materialize-textarea validate" length="1000"></textarea>
					<label for="detail"><?php echo T('正文内容'); ?></label>
				</div>

				<div class="input-field col s12">
					<select name="class_id">
						<option disabled selected><?php echo T('请选择'); ?></option>
						<?php foreach ($class as $row) : ?>
							<option value="<?php /*echo $row['id']; */ ?>"><?php echo T($row['name']); ?></option>
						<?php endforeach; ?>
					</select>
					<label><?php echo T('课程选择'); ?></label>
				</div>

				<div class="file-field input-field col s12">
					<div class="btn">
						<span><?php echo T('上传图片'); ?></span>
						<input type="file" name="pics" id="pics" onchange="preview(this)">
					</div>
					<div class="file-path-wrapper">
						<input class="file-path validate" type="text">
					</div>
				</div>

				<div class="col s12 center">
					<i class="material-icons">expand_more</i>
					<b><?php echo T('上传图片预览'); ?></b>
					<i class="material-icons">expand_more</i>
				</div>

				<div id="preview" class="center"></div>

				<?php if ($_SESSION['user_auth'] == 1) : //如果是管理员，将显示“置顶”和“锁定”功能?>
					<div class="center col s12" style="margin: 15px 0px;">
						<div class="switch">
							<label>
								<b><?php echo T('不顶置'); ?></b>
								<input type="checkbox">
								<span class="lever"></span>
								<b><?php echo T('顶置'); ?></b>
							</label>
						</div>
					</div>
					<br/>
				<?php endif; ?>
				<div class="col s12 center">
					<button type="submit" name="submit"
							class="btn waves-effect waves-light"><?php echo T('立即发布'); ?></button>
					<button type="reset" name="reset" class="btn waves-effect waves-light">重新输入</button>
				</div>
			</form>
		</div>
	</fieldset>
<?php endif; ?>