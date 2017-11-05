<h3 class="center-align"><?php echo T('音乐搜索器'); ?></h3>
<style>
	label {
		padding-left: 30px !important;
		padding-right: 10px !important;
	}
</style>
<div class="row" style="max-width: 900px;">
	<form class="col s12" onsubmit="return false">
		<div class="row">
			<div class="input-field col s12">
				<input style="text-align: center!important; font-size: 1.5rem!important;" placeholder="例如: 不要说话 陈奕迅"
					   id="music_input" type="text" class="validate" required>
			</div>
		</div>
		<div class="row">
			<div class="col s12">
				<p style="text-align: center!important;">
					<input name="music_type" type="radio" id="netease" value="netease" class="with-gap"/>
					<label for="netease">网易</label>
					<input name="music_type" type="radio" id="qq" value="qq" class="with-gap"/>
					<label for="qq">ＱＱ</label>
					<input name="music_type" type="radio" id="kugou" value="kugou" class="with-gap"/>
					<label for="kugou">酷狗</label>
					<input name="music_type" type="radio" id="kuwo" value="kuwo" class="with-gap"/>
					<label for="kuwo">酷我</label>
					<input name="music_type" type="radio" id="xiami" value="xiami" class="with-gap"/>
					<label for="xiami">虾米</label>
					<input name="music_type" type="radio" id="baidu" value="baidu" class="with-gap"/>
					<label for="baidu">百度</label>
					<input name="music_type" type="radio" id="1ting" value="1ting" class="with-gap"/>
					<label for="1ting">一听</label>
					<input name="music_type" type="radio" id="migu" value="migu" class="with-gap"/>
					<label for="migu">咪咕</label>
					<input name="music_type" type="radio" id="lizhi" value="lizhi" class="with-gap"/>
					<label for="lizhi">荔枝</label>
					<input name="music_type" type="radio" id="qingting" value="qingting" class="with-gap"/>
					<label for="qingting">蜻蜓</label>
					<input name="music_type" type="radio" id="ximalaya" value="ximalaya" class="with-gap"/>
					<label for="ximalaya">喜马拉雅</label>
					<input name="music_type" type="radio" id="5singyc" value="5singyc" class="with-gap"/>
					<label for="5singyc">5sing 原创</label>
					<input name="music_type" type="radio" id="5singfc" value="5singfc" class="with-gap"/>
					<label for="5singfc">5sing 翻唱</label>
					<input name="music_type" type="radio" id="soundcloud" value="soundcloud" class="with-gap"/>
					<label for="soundcloud">SoundCloud</label>
				</p>
			</div>
		</div>
	</form>
</div>

<!--<audio controls="controls" class="">
	<source src="http://m10.music.126.net/20171105104717/b94f06007207a4823be44e21fe4a162a/ymusic/3b7d/52f0/e023/d5e16a0ead8a5274827c20d06e2dcaeb.mp3"
			type="audio/mpeg">
</audio>-->

