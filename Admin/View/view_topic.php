<fieldset>
	<legend>正文</legend>
	<table>
		<tr>
			<td>

			</td>
		</tr>
		<tr>
			<td class="center">
				<a class="btn-floating waves-effect waves-light" onclick="history.back();"
				   style="float:left;margin-left: -10px;"><i class="material-icons">arrow_back</i></a>
				<h4><?php echo '主题：' . $topic['topic']; ?></h4>
			</td>
		</tr>
		<tr>
			<td class="center">
				用户<a href="?service=User.user_Info&user_id=<?php echo $topic['user_id']; ?>">
					<?php echo $topic['name']; ?></a><br/><?php echo date('Y-m-d H:i:s', $topic['add_time']); ?>
			</td>
		</tr>
		<tr>
			<td class="cyan lighten-3">
				<?php
				//输出整理好的内容
				echo nl2br(htmlspecialchars($topic['detail']));
				echo '</br>';
				?>

				<?php if (!empty($topic['pics'])): ?>
					<img class="materialboxed" width="40%" src="<?php echo DI()->tool->staticPath($topic['pics']); ?>">
				<?php endif; ?>
			</td>
		</tr>
		<!--<p>创建于<?php echo date('Y-m-d H:i:s', $topic['add_time']); ?></p>-->

		<tr>
			<td class="lime lighten-4">
				<dl>

					<?php
					if (empty($reply['total'])) :
						echo '暂无回复!';
					else :
						foreach ($reply['rows'] as $key => $row) : ?>

							<dt>
								用户：<a href="?service=User.user_Info&user_id=<?php echo $row['user_id']; ?>"><?php echo $row['reply_name']; ?></a>
								<?php echo date('Y-m-d H:i:s', $row['add_time']); ?>
							</dt>
							<dd>
								<?php
								//输出整理好的内容
								echo nl2br(htmlspecialchars($row['reply_detail']));
								echo '</br>';
								?>

								<?php if (!empty($row['reply_pics'])) : ?>
									<img class="materialboxed" width="30%"
										 src="<?php echo DI()->tool->staticPath($row['reply_pics']); ?>">
								<?php endif; ?>
							</dd>
						<?php endforeach; endif; ?>
				</dl>
			</td>
		</tr>
	</table>

	<!--内容回复表单，开始-->


	<div class="replyText"><?php if (!isset($_SESSION['admin_name'])) : ?>
			<p class="center">
				<a class="btn waves-effect waves-light" href="?service=User.register">注册</a><br/>
				或<br/>
				<a class="btn waves-effect waves-light" href="?service=User.login">登录</a><br/>
				进行评论
			</p>
		<?php else: ?>

		<table class="blue lighten-5">
			<form id="Reply_Topic" enctype="multipart/form-data" method="post" onsubmit="return false;">
				<input name="service" value="Reply.add_Reply" type="hidden">
				<input name="topic_id" type="hidden" value="<?php echo $topic['id']; ?>">
				<input name="user_id" type="hidden" value="<?php echo $_SESSION['admin_id']; ?>">
				<tr>
					<td>
						<div class="input-field">
							<textarea name="reply_detail" class="materialize-textarea validate"></textarea>
							<label for="reply_detail">回帖内容</label>
					</td>
				</tr>
				<tr>
					<td>
						<div class="file-field input-field">
							<div class="btn waves-effect waves-light">
								<span>上传图片</span>
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

	<!--管理员操作表单，开始-->
	<div class="center">
		<p>管理员操作</p>


		<!--显示置顶操作按钮-->
		<?php if ($topic['sticky'] == 0) : ?>
			<button onclick="stick_topic(<?php echo $topic['id']; ?>)" class="btn waves-effect waves-light">置顶该贴
			</button>
		<?php else : ?>
			<button onclick="unstick_topic(<?php echo $topic['id']; ?>)" class="btn waves-effect waves-light">取消置顶
			</button>
		<?php endif; ?>
		<button onclick="delete_topic(<?php echo $topic['id']; ?>)" class="btn waves-effect waves-light">删除帖子
		</button>
	</div>
	<!--管理员操作表单，结束-->
	<?php endif; ?>
</fieldset>