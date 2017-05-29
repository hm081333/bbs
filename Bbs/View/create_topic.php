<?php
require_once './Public/static/header/header.php';
?>

<?php if (!isset($_SESSION['user_id'])) : //如果用户未登录，显示错误信息?>

	<h3 class="center">您未登陆,没有发帖权限！</h3>
	<p class="center">对不起，请<br/>
		<a class="btn waves-effect waves-light" href="?service=User.register">注册</a><br/>
		或者<br/>
		<a class="btn waves-effect waves-light" href="?service=User.login">登录</a>
	</p>

<?php else : //如果用户登录，显示输入表单 ?>

	<fieldset>
		<legend>新帖子</legend>
		<div class="row">
			<form id="Create_Topic" enctype="multipart/form-data" method="post" action="Topic.create_Topic" class="col s12">
				<input name="action" value="post" type="hidden">
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

				<?php if ($_SESSION['user_auth'] == 1) : //如果是管理员，将显示“置顶”和“锁定”功能?>
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
				<?php endif; ?>
			</form>
			<div class="col s12 center">
				<button id="submit" class="btn waves-effect waves-light">立即发布</button>
<!--				<button type="reset" name="reset" class="btn waves-effect waves-light">重新输入</button>-->
			</div>


		</div>
	</fieldset>

<?php endif; ?>

<?php
//公用尾部页面
require_once './Public/static/header/footer.php';
?>

<script>
	$('span[class="lever"]').click(function(){
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
			url: '?service=Topic.create_Topic',
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
