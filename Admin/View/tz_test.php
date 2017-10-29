<style>
	table {
		table-layout: fixed;
	}

	.suduk {
		margin: 0px;
		padding: 0;
	}

	.sudu {
		padding: 0;
		background: #5dafd1;
	}
</style>
<ul class="tabs">
	<li class="tab"><a href="#test1">服务器性能检测</a></li>
	<li class="tab"><a href="#test2">网络速度测试</a></li>
	<li class="tab"><a href="#test3">数据库连接检测</a></li>
	<li class="tab"><a href="#test4">函数检测</a></li>
	<li class="tab"><a href="#test5">邮件发送检测</a></li>
</ul>

<!--服务器性能检测-->
<div id="test1" class="col s12">
	<div class="col s12">
		<div class="col s3">
			参照对象
			CPU信息
		</div>
		<div class="col s3">
			整数运算能力检测
			(1+1运算300万次)
		</div>
		<div class="col s3">
			浮点运算能力检测
			(圆周率开平方300万次)
		</div>
		<div class="col s3">
			数据I/O能力检测
			(读取10K文件1万次)
		</div>
	</div>
	<div class="col s12">
		<div class="col s3">
			4 x Xeon L5520 @ 2.27GHz
		</div>
		<div class="col s3">
			0.357秒
		</div>
		<div class="col s3">
			0.802秒
		</div>
		<div class="col s3">
			0.023秒
		</div>
	</div>
	<div class="col s12">
		<div class="col s3">
			8 x Xeon E5520 @ 2.27GHz
		</div>
		<div class="col s3">
			0.431秒
		</div>
		<div class="col s3">
			1.024秒
		</div>
		<div class="col s3">
			0.034秒
		</div>
	</div>
	<div class="col s12">
		<div class="col s3">
			4 x Core i7 920 @ 2.67GHz
		</div>
		<div class="col s3">
			0.421秒
		</div>
		<div class="col s3">
			1.003秒
		</div>
		<div class="col s3">
			0.038秒
		</div>
	</div>
	<div class="col s12">
		<div class="col s3">
			2 x Pentium4 3.00GHz
		</div>
		<div class="col s3">
			0.521秒
		</div>
		<div class="col s3">
			1.559秒
		</div>
		<div class="col s3">
			0.054秒
		</div>
	</div>
	<div class="col s12">
		<div class="col s3">
			2 x Core2Duo E4600 @ 2.40GHz
		</div>
		<div class="col s3">
			0.343秒
		</div>
		<div class="col s3">
			0.761秒
		</div>
		<div class="col s3">
			0.023秒
		</div>
	</div>
	<div class="col s12">
		<div class="col s3">
			4 x Xeon E5530 @ 2.40GHz
		</div>
		<div class="col s3">
			0.535秒
		</div>
		<div class="col s3">
			1.607秒
		</div>
		<div class="col s3">
			0.058秒
		</div>
	</div>
	<div class="col s12">
		<div class="col s3">
			本台服务器
		</div>
		<div class="col s3">
			<span id="pInt">未测试</span>
			<br/>
			<button class="btn waves-effect waves-light ajax" data-service="Tz.DoTest" data-type="pInt">整型测试</button>
		</div>
		<div class="col s3">
			<span id="pFloat">未测试</span>
			<br/>
			<button class="btn waves-effect waves-light ajax" data-service="Tz.DoTest" data-type="pFloat">浮点测试</button>
		</div>
		<div class="col s3">
			<span id="pIo">未测试</span>
			<br/>
			<button class="btn waves-effect waves-light ajax" data-service="Tz.DoTest" data-type="pIo">IO测试</button>
		</div>
	</div>
</div>

<!--网络速度测试-->
<div id="test2">
	<table class="bordered highlight">
		<tr>
			<td class="center">
				向客户端传送1000k字节数据&nbsp;&nbsp;&nbsp;&nbsp;带宽比例按理想值计算
			</td>
		</tr>
		<tr>
			<td width="81%" align="center">
				<table align="center" width="550" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td height="15" width="50">带宽</td>
						<td height="15" width="50">1M</td>
						<td height="15" width="50">2M</td>
						<td height="15" width="50">3M</td>
						<td height="15" width="50">4M</td>
						<td height="15" width="50">5M</td>
						<td height="15" width="50">6M</td>
						<td height="15" width="50">7M</td>
						<td height="15" width="50">8M</td>
						<td height="15" width="50">9M</td>
						<td height="15" width="50">10M</td>
					</tr>
					<tr>
						<td colspan="11" class="suduk">
							<table align="center" width="550" border="0" cellspacing="0" cellpadding="0" height="8" class="suduk">
								<tr>
									<td class="sudu" width="<?php
									if (preg_match("/[^\d-., ]/", $speed)) {
										echo "0";
									} else {
										echo 550 * ($speed / 11000);
									}
									?>">
									</td>
									<td class="suduk" width="<?php
									if (preg_match("/[^\d-., ]/", $speed)) {
										echo "550";
									} else {
										echo 550 - 550 * ($speed / 11000);
									}
									?>">
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<span id="pSpeed">未探测</span>
			</td>
		</tr>
		<tr>
			<td class="center">
				<button class="btn waves-effect waves-light ajax" data-service="Tz.DoTest" data-type="pSpeed">开始测试</button>
			</td>
		</tr>
	</table>
</div>

<!--数据库连接检测-->
<div id="test3" class="col s12">
	<form method="post" onsubmit="return false;">
		<input type="hidden" name="service" value="Tz.DoTest"/>
		<input type="hidden" name="type" value="pMySQL"/>
		<div class="input-field col s12">
			<input id="host" name="host" type="text" class="validate" value="localhost">
			<label for="host">地址</label>
		</div>
		<div class="input-field col s12">
			<input id="port" name="port" type="text" class="validate" value="3306">
			<label for="port">端口</label>
		</div>
		<div class="input-field col s12">
			<input id="login" name="login" type="text" class="validate">
			<label for="login">用户名</label>
		</div>
		<div class="input-field col s12">
			<input id="password" name="password" type="text" class="validate">
			<label for="password">密码</label>
		</div>
		<div class="col s12 center">
			<span id="pMySQL">未测试</span>
		</div>
		<div class="col s12 center">
			<button type="submit" name="submit" class="btn waves-effect waves-light">
				<?php echo T('MySQL检测'); ?>
			</button>
		</div>
	</form>
</div>

<!--函数检测-->
<div id="test4" class="col s12">
	<form method="post" onsubmit="return false;">
		<input type="hidden" name="service" value="Tz.DoTest"/>
		<input type="hidden" name="type" value="pFun"/>
		<div class="input-field col s12">
			<input id="funName" name="funName" type="text" class="validate">
			<label for="funName">请输入您要检测的函数</label>
		</div>
		<div class="col s12 center">
			<span id="pFun">未测试</span>
		</div>
		<div class="col s12 center">
			<button type="submit" name="submit" class="btn waves-effect waves-light">
				<?php echo T('函数检测'); ?>
			</button>
		</div>
	</form>
</div>

<!--邮件发送检测-->
<div id="test5" class="col s12">
	<form method="post" onsubmit="return false;">
		<input type="hidden" name="service" value="Tz.DoTest"/>
		<input type="hidden" name="type" value="pMail"/>
		<div class="input-field col s12">
			<input id="mailAdd" name="mailAdd" type="text" class="validate">
			<label for="mailAdd">请输入您要检测的邮件地址</label>
		</div>
		<div class="col s12 center">
			<span id="pMail">未测试</span>
		</div>
		<div class="col s12 center">
			<button type="submit" name="submit" class="btn waves-effect waves-light">
				<?php echo T('邮件检测'); ?>
			</button>
		</div>
	</form>
</div>


<script>
	$().ready(function () {
		$('.ajax').click(function () {
			var $this = $(this);
			var data = $this.data();
			var id_name = "#" + data.type;
			Ajax(data, function (d) {
				if (d.ret == 200) {
					$(id_name).html(d.data);
				} else {
					Materialize.toast(d.msg, 2000, 'rounded');
				}
			});
		});
		$('form').submit(function () {
			var $this = $(this);
			var data = $this.serialize();
			Ajax(data, function (d) {
				if (d.ret == 200) {
					$('#' + d.data['type']).html(d.data['result']);
				} else {
					Materialize.toast(d.msg, 2000, 'rounded');
				}
			});
		})
	})
</script>