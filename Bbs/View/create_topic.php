<style>
	#detail {
		width: 100%;
		height: 500px;
	}
</style>
<fieldset>
	<legend><?php echo T('新帖子'); ?></legend>
	<div class="row">
		<div class="col s12">
			<ul class="tabs">
				<li class="tab col s6"><a class="active" href="#info">相关信息</a></li>
				<li class="tab col s6"><a href="#contants">内容</a></li>
			</ul>
		</div>
		<form id="Create_Topic" enctype="multipart/form-data" method="post" onsubmit="return false;" class="col s12">
			<input name="service" value="Topic.create_Topic" type="hidden">
			<input name="action" value="post" type="hidden">
			<div id="info" class="col s12">
				<div class="input-field col s12">
					<input name="topic" type="text" id="topic" class="validate" length="100">
					<label for="topic"><?php echo T('标题'); ?></label>
				</div>
				<!--<div class="input-field col s12">
					<textarea name="detail" id="detail" class="materialize-textarea validate" length="1000"></textarea>
					<label for="detail"><?php /*echo T('正文内容'); */ ?></label>
				</div>-->
				<div class="input-field col s12">
					<select name="class_id">
						<option disabled selected><?php echo T('请选择'); ?></option>
						<?php foreach ($class as $row) : ?>
							<option value="<?php echo $row['id']; ?>"><?php echo T($row['name']); ?></option>
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
								<input type="checkbox" name="sticky">
								<span class="lever"></span>
								<b><?php echo T('顶置'); ?></b>
							</label>
						</div>
					</div>
					<br/>
				<?php endif; ?>
			</div>
			<div id="contants" class="col s12">
				<script id="detail" type="text/plain">
				</script>
			</div>
			<div class="col s12 center">
				<button type="submit" name="submit"
				        class="btn waves-effect waves-light"><?php echo T('立即发布'); ?></button>
				<button type="reset" name="reset" class="btn waves-effect waves-light">重新输入</button>
			</div>
		</form>
	</div>
</fieldset>

<script type="text/javascript" charset="utf-8"
        src="<?php echo DI()->tool->staticPath('js/neditor/neditor.config.js'); ?>"></script>
<script type="text/javascript" charset="utf-8"
        src="<?php echo DI()->tool->staticPath('js/neditor/neditor.all.js'); ?>"></script>
<!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
<!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
<script type="text/javascript" charset="utf-8"
        src="<?php echo DI()->tool->staticPath('js/neditor/i18n/zh-cn/zh-cn.js'); ?>"></script>
<!-- 实例化编辑器 -->
<script type="text/javascript">
	//实例化编辑器
	//建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例

	var ue = UE.getEditor('detail');

	console.log(UEDITOR_CONFIG);
</script>