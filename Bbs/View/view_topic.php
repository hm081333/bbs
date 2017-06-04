<?php
require_once './Public/static/header/header.php';
?>

<fieldset>
	<legend><?php echo T('正文') ?></legend>
	<table>
		<tr>
			<td>

			</td>
		</tr>
		<tr>
			<td class="center">
				<!--				<a class="btn-floating waves-effect waves-light" onclick="history.back();" style="float:left;margin-left: -10px;"><i class="material-icons">arrow_back</i></a>-->
				<h4><?php echo T('主题：' . $topic['topic']); ?></h4>
			</td>
		</tr>
		<tr>
			<td class="center">
				<?php echo T('用户：') ?><a href="?service=User.user_Info&user_id=<?php echo $topic['user_id']; ?>">
					<?php echo $topic['name']; ?></a><br/><?php echo $topic['datetime']; ?>
			</td>
		</tr>
		<tr>
			<td class="cyan lighten-3">
				<?php
				//输出整理好的内容
				echo T(nl2br(htmlspecialchars($topic['detail'])));
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
					if (empty($reply['total'])) :
						echo T('暂无回复！');
					else :
					foreach ($reply['rows'] as $key => $row) : ?>

					<dt>
						<?php echo T('用户：') ?><a href="?service=User.user_Info&user_id=<?php echo $row['user_id']; ?>"><?php echo $row['reply_name']; ?></a>
						<?php echo $row['reply_datetime']; ?>
					</dt>
					<dd>
						<?php
						//输出整理好的内容
						echo T(nl2br(htmlspecialchars($row['reply_detail'])));
						echo '</br>';
						?>

						<?php if (!empty($row['reply_pics'])) : ?>
							<img class="materialboxed" width="30%" src="./Public/static/upload/<?php echo $row['reply_pics'] ?>">
						<?php endif; ?>
					</dd>
					<?php endforeach; endif; ?>
				</dl>
			</td>
		</tr>
	</table>

	<!--内容回复表单，开始-->


	<div class="replyText"><?php
		//判断用户是否已经注册
		if (!isset($_SESSION['user_name'])) {
			?>
			<p class="center">
				<a class="btn waves-effect waves-light" href="?service=User.register"><?php echo T('注册') ?></a><br/>
				<?php echo T('或') ?><br/>
				<a class="btn waves-effect waves-light" href="?service=User.login"><?php echo T('登录') ?></a><br/>
				<?php echo T('进行评论') ?>
			</p>
			<?php

		} else {
		?>

		<table class="blue lighten-5">
			<form enctype="multipart/form-data" method="post">
				<input name="topic_id" type="hidden" value="<?php echo $topic['id']; ?>">
				<input name="user_id" type="hidden" value="<?php echo $_SESSION['user_id']; ?>">
				<tr>
					<td>
						<div class="input-field">
							<textarea name="reply_detail" class="materialize-textarea validate"></textarea>
							<label for="reply_detail"><?php echo T('回帖内容') ?></label>
							<!--<textarea class="coolscrollbar" name="reply_detail" cols="80" rows="5"></textarea>-->
					</td>
				</tr>
				<tr>
					<td>
						<div class="file-field input-field">
							<div class="btn waves-effect waves-light">
								<span><?php echo T('上传图片') ?></span>
								<input type="file" name="reply_pics" onchange="preview(this)">
							</div>
							<div class="file-path-wrapper">
								<input class="file-path validate" type="text">
							</div>
							<div id="preview" class="center"></div>
						</div>
					</td>
				</tr>
			</form>
			<tr>
				<td class="center">
					<button id="reply" class="btn waves-effect waves-light"><?php echo T('回复该帖') ?></button>
				</td>
			</tr>
		</table>

	</div>
	<br>
	<!--内容回复表单，结束-->

	<?php
	//如果是管理员用户，则输出“置顶”、“锁定”和“删除”按钮
	if ($_SESSION['user_auth'] == 1) {
		?>
		<!--管理员操作表单，开始-->
		<div class="center">
			<p><?php echo T('管理员操作') ?></p>


			<!--显示置顶操作按钮-->
			<?php if ($topic['sticky'] == 0) : ?>
				<button onclick="stick_topic(<?php echo $topic['id']; ?>)" class="btn waves-effect waves-light"><?php echo T('置顶该贴') ?>
				</button>
			<?php else : ?>
				<button onclick="unstick_topic(<?php echo $topic['id']; ?>)" class="btn waves-effect waves-light"><?php echo T('取消置顶') ?>
				</button>
			<?php endif; ?>
			<button onclick="delete_topic(<?php echo $topic['id']; ?>)" class="btn waves-effect waves-light"><?php echo T('删除帖子') ?>
			</button>
		</div>
		<!--管理员操作表单，结束-->
		<?php

	}
	}

	?>
</fieldset>

<script>
	$("#reply").click(function () {
		$.ajax({
			type: 'POST',
			url: '?service=Reply.add_Reply',
			data: new FormData($('form')[0]),
			processData: false,
			contentType: false,
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
