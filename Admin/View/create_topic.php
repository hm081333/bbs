<?php require_once './Public/static/header/header_admin.php'; ?>

<fieldset>
	<legend>新帖子</legend>
	<div class="row">
		<form id="Create_Topic" enctype="multipart/form-data" class="col s12">
			<input name="action" value="post" type="hidden">
			<input name="service" value="Topic.create_Topic" type="hidden">
			<div class="input-field col s12">
				<input name="topic" type="text" id="topic" class="validate" length="100">
				<label for="topic">标题</label>
			</div>


			<div class="input-field col s12">
				<textarea name="detail" id="detail" class="materialize-textarea validate" length="1000"></textarea>
				<label for="detail">正文内容</label>
			</div>


			<div class="file-field input-field col s8">
				<div class="btn">
					<span>上传图片</span>
					<input type="file" name="pics" id="pics" onchange="preview(this)">
				</div>
				<div class="file-path-wrapper">
					<input class="file-path validate" type="text">
				</div>
			</div>

			<label>课程选择</label>
			<select name="class_id" class="browser-default col s4">
				<option value="" disabled selected>请选择</option>
				<?php foreach ($class as $row) : ?>
					<option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
				<?php endforeach; ?>
			</select>

			<div class="col s12 center">
				<i class="material-icons">expand_more</i>
				<b>上传图片预览</b>
				<i class="material-icons">expand_more</i>
			</div>

			<div id="preview" class="center"></div>

			<input type="hidden" name="sticky">
			<div class="center col s12" style="margin: 15px 0px;">
				<div class="switch">
					<label>
						<b>不顶置</b>
						<input type="checkbox">
						<span class="lever"></span>
						<b>顶置</b>
					</label>
				</div>
			</div>
			<br/>

		</form>
		<div class="col s12 center">
			<button id="submit" class="btn waves-effect waves-light">立即发布</button>
			<!--				<button type="reset" name="reset" class="btn waves-effect waves-light">重新输入</button>-->
		</div>


	</div>
</fieldset>


<?php require_once './Public/static/header/footer.php'; ?>

<script>
	$('span[class="lever"]').click(function () {
		var sticky = '';
		if ($('input[type="checkbox"]')[0]['checked'] == false) {
			sticky = 1;
		} else {
			sticky = 0;
		}
		$('input[name="sticky"]').attr('value', sticky);
	});
	$("#submit").click(function () {
		$.ajax({
			type: 'POST',
			data: new FormData($('#Create_Topic')[0]),
			processData: false,
			contentType: false,
			success: function (d) {
				if (d.ret == 200) {
					Materialize.toast(d.msg, 2000, 'rounded', function () {
						// location.href='./';
						history.back();
					});
				} else {
					Materialize.toast(d.msg, 2000, 'rounded');
				}
			}
		});
	});
</script>
