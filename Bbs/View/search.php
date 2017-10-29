<?php require_once './Public/static/header/header.php'; ?>

	<h3 class="center"><?php echo T('搜索'); ?></h3>
	<fieldset>
		<legend><?php echo T('搜索'); ?></legend>
		<div class="row">
			<form id="search" method="post" onsubmit="return false;" class="col s12">
				<input type="hidden" name="service" value="public.search"/>
				<input type="hidden" name="action" value="post"/>
				<div class="col s12">
					<div class="input-field">
						<i class="material-icons prefix">label_outline</i>
						<input name="keyword" type="text" class="validate">
						<label for="keyword"><?php echo T('关键字'); ?></label>
					</div>
					<p>搜索条件：</p>
					<p>
						<input name="term" type="radio" id="topic" value="topic" class="with-gap" checked/>
						<label for="topic"><?php echo T('标题'); ?></label>
						<input name="term" type="radio" id="detail" value="detail" class="with-gap"/>
						<label for="detail"><?php echo T('正文内容'); ?></label>
					</p>
				</div>
				<div class="col s12 center">
					<button type="submit" name="submit"
							class="btn waves-effect waves-light"><?php echo T('搜索'); ?></button>
					<button type="reset" class="btn waves-effect waves-light"><?php echo T('重新输入'); ?></button>
				</div>
			</form>
		</div>
	</fieldset>

	<script>
		$('#search').submit(function ()//提交表单
		{
			$.ajax({
				type: 'POST',
				data: $("#search").serialize(),
				success: function (d) {
					if (d.ret == 200) {
						Materialize.toast(d.msg, 2000, 'rounded', function () {
							location.reload();
						});
					} else {
						Materialize.toast(d.msg, 2000, 'rounded');
					}
				}
			});
		});
	</script>

<?php require_once './Public/static/header/footer.php'; ?>