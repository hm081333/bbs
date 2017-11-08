<h3 class="center-align"><?php echo T('音乐搜索器'); ?></h3>
<style>
	label {
		padding-left: 30px !important;
		padding-right: 10px !important;
	}

	.music-tips blockquote {
		font-family: Menlo, Monaco, Consolas, "Courier New", monospace;
		font-size: 13px;
	}

	.music-tips code {
		color: #c7254e;
	}

	.music-tips p span {
		display: inline-block;
		min-width: 50px;
	}

	.music-tips p b {
		font-weight: 500;
		color: #c7254e;
	}

	#title {
		font-size: 16px;
		margin: 12px;
	}

	#artist {
		color: #666;
		font-size: 12px;
		margin: 12px;
	}

	.card-content {
		padding: 0 0 0 10px !important;
	}
</style>
<div class="row">
	<form class="col s12" id="music" onsubmit="return false" style="display: block;">
		<input type="hidden" name="service" value="Music.Search">
		<div class="row">
			<div class="input-field col s12">
				<input style="text-align: center!important; font-size: 1.5rem!important;" placeholder="例如: 不要说话 陈奕迅" value="不要说话"
					   id="music_input" name="music_input" type="text" class="validate" required>
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
						<input name="music_type" <?php echo $key == 'netease' ? 'checked' : ''; ?> type="radio" id="<?php echo $key; ?>" value="<?php echo $key; ?>"
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
		<!--<div class="music-tips" hidden>
			<h4>帮助：</h4>
			<p>
				<b>标红</b> 为 <strong>音乐 ID</strong>，<u>下划线</u> 表示 <strong>音乐地址</strong>
			</p>
			<blockquote>
				<p>蜻蜓 FM 的 音乐 ID 需要使用 <code>| (管道符)</code> 组合，例如 <code>158696|5266259</code></p>
			</blockquote>
			<p>
				<span>网易：</span><u>http://music.163.com/#/song?id=<b>25906124</b></u>
			</p>
			<p>
				<span>ＱＱ：</span><u>http://y.qq.com/n/yqq/song/<b>002B2EAA3brD5b</b>.html</u>
			</p>
			<p>
				<span>酷狗：</span><u>http://m.kugou.com/play/info/<b>08228af3cb404e8a4e7e9871bf543ff6</b></u>
			</p>
			<p>
				<span>酷我：</span><u>http://www.kuwo.cn/yinyue/<b>382425</b>/</u>
			</p>
			<p>
				<span>虾米：</span><u>http://www.xiami.com/song/<b>2113248</b></u>
			</p>
			<p>
				<span>百度：</span><u>http://music.baidu.com/song/<b>556113</b></u>
			</p>
			<p>
				<span>一听：</span><u>http://www.1ting.com/player/b6/player_<b>220513</b>.html</u>
			</p>
			<p>
				<span>咪咕：</span><u>http://music.migu.cn/#/song/<b>1002531572</b>/P7Z1Y1L1N1/3/001002C</u>
			</p>
			<p>
				<span>荔枝：</span><u>http://www.lizhi.fm/1947925/<b>2498707770886461446</b></u>
			</p>
			<p>
				<span>蜻蜓：</span><u>http://www.qingting.fm/channels/<b>158696</b>/programs/<b>5266259</b></u>
			</p>
			<p>
				<span>喜马拉雅：</span><u>http://www.ximalaya.com/51701370/sound/<b>24755731</b></u>
			</p>
			<p>
				<span>5sing 原创：</span><u>http://5sing.kugou.com/yc/<b>1089684</b>.html</u>
			</p>
			<p>
				<span>5sing 翻唱：</span><u>http://5sing.kugou.com/fc/<b>14369766</b>.html</u>
			</p>
			<p>
				<span>SoundCloud (ID)：</span><u>soundcloud://sounds:<b>197401418</b></u> (请查看源码)
			</p>
			<p>
				<span>SoundCloud (地址)：</span><u>https://soundcloud.com/user2953945/tr-n-d-ch-t-n-eason-chan-kh-ng</u>
			</p>
		</div>-->
	</form>
	<div id="music_show" class="col s12" style="display: none;">
		<div class="row">
			<div class="input-field col s12 m6">
				<i class="material-icons prefix">share</i>
				<input id="music-link" type="text" class="tooltipped" data-position="top" data-delay="50" data-tooltip="音乐地址" value="http://music.163.com/#/song?id=25906124">
				<label for="music-link">音乐地址</label>
			</div>
			<div class="input-field col s12 m6">
				<i class="material-icons prefix">music_video</i>
				<input id="music-src" type="text" class="tooltipped" data-position="top" data-delay="50" data-tooltip="音乐链接"
					   value="http://m10.music.126.net/20171108093908/d8f8d6ff247f15dc3464c981f8e7d34e/ymusic/363b/72ef/7661/0b373b6cdfc54e3022ef436c3ad58ec3.mp3">
				<label for="music-src">音乐链接</label>
			</div>
			<div class="input-field col s12 m6">
				<i class="material-icons prefix">label_outline</i>
				<input id="music-name" type="text" class="tooltipped" data-position="top" data-delay="50" data-tooltip="音乐名称" value="不要说话">
				<label for="music-name">音乐名称</label>
			</div>
			<div class="input-field col s12 m6">
				<i class="material-icons prefix">person_outline</i>
				<input id="music-author" type="text" class="tooltipped" data-position="top" data-delay="50" data-tooltip="音乐作者" value="陈奕迅">
				<label for="music-author">音乐作者</label>
			</div>
		</div>
		<div class="row">
			<div id="result" style="display: none;"></div>
			<div class="col s12">
				<div class="card horizontal">
					<div class="card-image" style="max-width: 8.5rem;">
						<img src="http://p1.music.126.net/halW3WTZk1t5rk_4RW9ZvA==/2295780278816932.jpg">
					</div>
					<div class="card-stacked">
						<div class="card-content">
							<p id="title">不要说话</p>
							<p id="artist">陈奕迅</p>
							<audio controls="controls" class="" style="width: 100%;">
								<source src="http://m10.music.126.net/20171108101009/25f8d290eedba30759cd2907402fea64/ymusic/363b/72ef/7661/0b373b6cdfc54e3022ef436c3ad58ec3.mp3"
										type="audio/mpeg">
							</audio>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!--<a data-artist="陈奕迅" data-title="不要说话" data-album="不要说话" data-info="" data-image="http://p1.music.126.net/halW3WTZk1t5rk_4RW9ZvA==/2295780278816932.jpg?param=100x100" data-link="http://music.163.com/#/song?id=25906124" data-src="http://m10.music.126.net/20171108101009/25f8d290eedba30759cd2907402fea64/ymusic/363b/72ef/7661/0b373b6cdfc54e3022ef436c3ad58ec3.mp3" data-type="audio/mpeg"></a>-->

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
			$('#music_input').attr({placeholder: holder[filter]});
		});
		$('#music').submit(function () {
			Ajax($("#music").serialize(), function (d) {
				if (d.ret == 200) {
					var dataArray = eval(d.data);
					$("#result").empty();
					for (var i = 0; i < dataArray.length; i++) {
						$("#result").append("<a data-artist='" + dataArray[i]["author"] + "' data-title='" + dataArray[i]["name"] + "' data-album='" + dataArray[i]["name"] + "' data-info='' data-image='" + dataArray[i]["pic"] + "' data-link='" + dataArray[i]["link"] + "' data-src='" + dataArray[i]["music"] + "' data-type='audio/mpeg'></a>");
					}
					alertMsg(d.msg, function () {
						console.log(d);
						$('#music_show').show();
						$('#music').hide();
					});
				} else {
					alertMsg(msg);
				}
			});
		});
	});
</script>
