<h3 class="center"><?php echo T('添加BDUSS') ?></h3>

<fieldset>
	<legend><?php echo T('添加BDUSS') ?></legend>
	<div class="row">
		<form id="add_bduss" method="post" onsubmit="return false;" class="col s12">
			<input name="service" value="Tieba.AddBdussAC" type="hidden">
			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">account_box</i>
					<input id="bduss" name="bduss" type="text">
					<label for="bduss"><?php echo T('BDUSS') ?></label>
					<!--5ETmNlcDJkWU1TaUY2QzRqLTBWRzl6bWx-b0YyaGllZHRiRjdQTGd2by1odTFaSVFBQUFBJCQAAAAAAAAAAAEAAAAbw9MHaG0wODEzMzMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAD75xVk--cVZUn-->
				</div>
				<div>
					<p class="msg right"><i class="material-icons">warning</i><?php echo T('BDUSS') ?></p>
				</div>
			</div>
			<div class="col s12 center">
				<button type="submit" name="submit" class="btn waves-effect waves-light"><?php echo T('提交') ?></button>
			</div>
		</form>
</fieldset>