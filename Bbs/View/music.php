<h3 class="center-align"><?php echo T('音乐搜索器'); ?></h3>
<style>
	label {
		padding-left: 30px !important;
		padding-right: 10px !important;
	}
</style>
<div class="row">
	<form class="col s12" onsubmit="return false">
		<div class="row">
			<div class="input-field col s12">
				<input style="text-align: center!important; font-size: 1.5rem!important;" placeholder="例如: 不要说话 陈奕迅"
					   id="music_input" type="text" class="validate" required>
			</div>
		</div>
		<div class="row">
			<div class="input-field col s12">
				<select id="music_filter" name="music_filter">
					<option value="name">音乐名称</option>
					<option value="id">音乐 ID</option>
					<option value="url">音乐地址</option>
				</select>
				<label>搜索途径</label>
			</div>
		</div>
		<div class="row">
			<div class="col s12">
				<p style="text-align: center!important;">
					<?php $music_type_list = DI()->config->get('app.music_type_list'); ?>
					<?php foreach ($music_type_list as $key => $val): ?>
						<input name="music_type" type="radio" id="<?php echo $key; ?>" value="<?php echo $key; ?>"
							   class="with-gap"/>
						<label for="<?php echo $key; ?>"><?php echo $val; ?></label>
					<?php endforeach; ?>
				</p>
			</div>
		</div>
		<div class="row">
			<div class="col s12">
				<button class="btn-large waves-effect waves-light" type="submit" name="action"
						style="width: 100%; font-size: xx-large;">
					获取
					<i class="material-icons large">send</i>
				</button>
			</div>
		</div>
	</form>
</div>

<!--<audio controls="controls" class="">
	<source src="http://m10.music.126.net/20171105104717/b94f06007207a4823be44e21fe4a162a/ymusic/3b7d/52f0/e023/d5e16a0ead8a5274827c20d06e2dcaeb.mp3"
			type="audio/mpeg">
</audio>-->

<script>
	$().ready(function () {
		$('#music_filter').change(function () {
			$this = $(this);
			var filter = $this.val();
			var holder = {
				name: '例如: 不要说话 陈奕迅',
				id: '例如: 25906124',
				url: '例如: http://music.163.com/#/song?id=25906124',
				'pattern-name': '^.+$',
				'pattern-id': '^[\\w\\/\\|]+$',
				'pattern-url': '^https?:\\/\\/\\S+$'
			};
			$('#music_input').attr({
				placeholder: holder[filter]
			});
		})
	});
</script>
