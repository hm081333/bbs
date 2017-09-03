<fieldset>
	<legend><?php echo T('正文') ?></legend>
	<table>
		<tr>
			<td class="center">
				<h4><?php echo T($topic['topic']); ?></h4>
			</td>
		</tr>
		<tr>
			<td class="center">
				<?php echo T('用户：') ?><a href="?service=User.user_Info&user_id=<?php echo $topic['user_id']; ?>">
					<?php echo $topic['name']; ?></a><br/><?php echo date('Y-m-d H:i:s', $topic['add_time']); ?>
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
		<tr>
			<td class="lime lighten-4">
				<dl>
					<?php
					if (empty($reply['total'])) :
						echo T('暂无回复！');
					else :
						foreach ($reply['rows'] as $key => $row) : ?>

							<dt>
								<?php echo T('用户：') ?><a
										href="?service=User.user_Info&user_id=<?php echo $row['user_id']; ?>"><?php echo $row['reply_name']; ?></a>
								<?php echo date('Y-m-d H:i:s', $row['add_time']); ?>
							</dt>
							<dd>
								<?php
								//输出整理好的内容
								echo T(nl2br(htmlspecialchars($row['reply_detail'])));
								echo '</br>';
								?>

								<?php if (!empty($row['reply_pics'])) : ?>
									<img class="materialboxed" width="30%"
										 src="./Public/static/upload/<?php echo $row['reply_pics'] ?>">
								<?php endif; ?>
							</dd>
						<?php endforeach; endif; ?>
				</dl>
			</td>
		</tr>
	</table>

	<!--内容回复表单，开始-->

	<div class="replyText">
		<!--判断用户是否登陆-->
		<?php if (!isset($_SESSION['user_name'])) : ?>
			<p class="center">
				<a class="btn waves-effect waves-light" href="?service=User.register"><?php echo T('注册') ?></a><br/>
				<?php echo T('或') ?><br/>
				<a class="btn waves-effect waves-light" href="?service=User.login"><?php echo T('登录') ?></a><br/>
				<?php echo T('进行评论') ?>
			</p>
		<?php else: ?>

		<table class="blue lighten-5">
			<form id="Reply_Topic" enctype="multipart/form-data" method="post" onsubmit="return false;">
				<input name="service" value="Reply.add_Reply" type="hidden">
				<input name="topic_id" type="hidden" value="<?php echo $topic['id']; ?>">
				<input name="user_id" type="hidden" value="<?php echo $_SESSION['user_id']; ?>">
				<tr>
					<td>
						<div class="input-field">
							<textarea name="reply_detail" class="materialize-textarea validate"></textarea>
							<label for="reply_detail"><?php echo T('回帖内容') ?></label>
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
				<tr>
					<td class="center">
						<button type="submit" name="submit"
								class="btn waves-effect waves-light"><?php echo T('回复该帖') ?></button>
					</td>
				</tr>
			</form>
		</table>

	</div>
	<br>
	<!--内容回复表单，结束-->
	<!--如果是管理员用户，则输出“置顶”、“锁定”和“删除”按钮-->
	<?php if ($_SESSION['user_auth'] == 1) : ?>
		<!--管理员操作表单，开始-->
		<div class="center">
			<p><?php echo T('管理员操作') ?></p>
			<!--显示置顶操作按钮-->
			<?php if ($topic['sticky'] == 0) : ?>
				<button onclick="stick_topic(<?php echo $topic['id']; ?>)"
						class="btn waves-effect waves-light"><?php echo T('置顶该贴') ?>
				</button>
			<?php else : ?>
				<button onclick="unstick_topic(<?php echo $topic['id']; ?>)"
						class="btn waves-effect waves-light"><?php echo T('取消置顶') ?>
				</button>
			<?php endif; ?>
			<button onclick="delete_topic(<?php echo $topic['id']; ?>)"
					class="btn waves-effect waves-light"><?php echo T('删除帖子') ?>
			</button>
		</div>
		<!--管理员操作表单，结束-->
	<?php endif;
	endif; ?>
</fieldset>

<script>

</script>
