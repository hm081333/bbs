<?php
require_once './Public/static/header/header.php';
?>


<fieldset>
	<legend>正文</legend>
	<table>
		<tr>
			<td>

			</td>
		</tr>
		<tr>
			<td class="center">
				<!--<a class="btn-floating waves-effect waves-light" style="float:left;margin-left: -10px;"><i id="x" class="material-icons">arrow_back</i></a>-->
				<h4><?php echo '主题：' . $topic['topic']; ?></h4>
			</td>
		</tr>
		<tr>
			<td class="center">
				用户<a href="view_profile.php?id=<?php echo $topic['name']; ?>">
					<?php echo $topic['name']; ?></a><br/><?php echo $topic['datetime']; ?>
			</td>
		</tr>
		<tr>
			<td class="cyan lighten-3">
				<?php
				//输出整理好的内容
				echo nl2br(htmlspecialchars($topic['detail']));
				echo '</br>';
				?>

				<?php
				if (!empty($topic['pics'])) {
					?>
					<img class="materialboxed" width="40%" src="./Public/static/upload/<?php echo $topic['pics'] ?>">
					<?php

				} else {
					echo '';
				}
				?>
			</td>
		</tr>
		<!--<p>创建于<?php echo $topic['datetime']; ?></p>-->

		<tr>
			<td class="lime lighten-4">
				<dl>

						<?php
						if (empty($reply['total'])) {
							echo '暂无回复!';
						} else {

						foreach ($reply['rows'] as $key => $row) {
						?>

						<dt>
							<a href="view_profile.php?id=<?php echo $row['reply_name']; ?>">
								<?php echo '用户' . $row['reply_name']; ?>
							</a>
							<?php echo $row['reply_datetime']; ?>
						</dt>
						<dd>
						<?php
						//输出整理好的内容
						echo nl2br(htmlspecialchars($row['reply_detail']));

						echo '</br>'; ?>

						<?php
						if (!empty($row['reply_pics'])) {
							?>
							<img class="materialboxed" width="30%" src="<?php echo $row['reply_pics'] ?>">
							<?php

						} ?>


					</dd>


					<?php
					}}
					?>
				</dl>
			</td>
		</tr>
	</table>

	<!--内容回复表单，开始-->


	<div class="replyText"><?php
		//判断用户是否已经注册
		if (!isset($_SESSION['username'])) {
			?>
			<p class="center">
				<a class="btn waves-effect waves-light" href="create_user.php">注册</a><br/>
				或<br/>
				<a class="btn waves-effect waves-light" href="login.php">登录</a><br/>
				进行评论
			</p>
			<?php

		} else {
			?>
			<form enctype="multipart/form-data" method="post" action="add_reply.php">
				<input name="id" type="hidden" value="<?php echo $topic['id']; ?>">
				<table class="blue lighten-5">
					<tr>
						<td>
							<div class="input-field">
								<textarea name="reply_detail" id="reply_detail"
										  class="materialize-textarea validate"></textarea>
								<label for="reply_detail">回帖内容</label>
								<!--<textarea class="coolscrollbar" name="reply_detail" cols="80" rows="5"></textarea>-->
						</td>
					</tr>
					<tr>
						<td>
							<div class="file-field input-field">
								<div class="btn waves-effect waves-light">
									<span>上传图片</span>
									<input type="file" name="reply_pics" id="reply_pics" onchange="preview(this)">
								</div>
								<div class="file-path-wrapper">
									<input class="file-path validate" type="text">
								</div>
								<div id="preview" class="center"></div>
							</div>
						</td>
					</tr>
					<tr>
						<td class="center">
							<button type="submit" name="Submit" class="btn waves-effect waves-light">回复该帖</button>
						</td>
					</tr>
				</table>
			</form>
			</div>
	<br>
	<!--内容回复表单，结束-->

	<?php
	//如果是管理员用户，则输出“置顶”、“锁定”和“删除”按钮
	if ($_SESSION['user_auth'] == 1) {
		?>
		<!--管理员操作表单，开始-->
		<div class="center">
			<p>管理员操作</p>


			<!--显示置顶操作按钮-->
			<?php if ($topic['sticky'] == 0) {
				?>
				<form name="stick" method="post" action="stick_topic.php">
					<input type="hidden" name="id" value="<?php echo $topic['id']; ?>">
					<button type="submit" name="Submit" class="btn waves-effect waves-light">置顶该贴</button>
					<br/>
					将该贴置于顶端
				</form>
				<?php

			} else {
				?>
				<form name="unstick" method="post" action="unstick_topic.php">
					<input type="hidden" name="id" value="<?php echo $topic['id']; ?>">
					<button type="submit" name="Submit" class="btn waves-effect waves-light">取消置顶</button>
					<br/>
					取消该贴置顶
				</form>
				<?php

			} ?>

			<!--显示删除操作按钮-->
			<form name="delete" method="get" action="del_topic.php">
				<input type="hidden" name="id" value="<?php echo $topic['id']; ?>">
				<button type="submit" class="btn waves-effect waves-light">删除帖子</button>
				<br/>
				删除该帖与回复内容
			</form>
		</div>
		<!--管理员操作表单，结束-->
		<?php

	}}

	?>
</fieldset>

<?php include('./Public/static/header/footer.php'); ?>
