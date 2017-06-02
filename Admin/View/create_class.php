<?php require_once './Public/static/header/header_admin.php'; ?>

	<h3 class="center">添加课程分类</h3>

	<fieldset>
		<legend>Add Class</legend>
		<div class="row">
			<form id="add_Class" class="col s12">
				<input name="action" value="post" type="hidden">
				<input name="service" value="Class.create_Class" type="hidden">
				<div class="col s12">
					<div class="input-field">
						<i class="material-icons prefix">label_outline</i>
						<input name="name" type="text" class="validate">
						<label for="name">课程名</label>
					</div>
				</div>

				<div class="col s12">
					<div class="input-field">
						<i class="material-icons prefix">label_outline</i>
						<input name="tips" type="text" class="validate">
						<label for="tips">课程说明</label>
					</div>
				</div>
			</form>
			<div class="col s12 center">
				<button onclick="create_Class()" class="btn waves-effect waves-light">添加课程</button>
			</div>

	</fieldset>

<?php require_once './Public/static/header/footer.php'; ?>