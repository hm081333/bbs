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
	<li class="tab"><a href="#test3">MySQL数据库连接检测</a></li>
	<li class="tab"><a href="#test4">函数检测</a></li>
	<li class="tab"><a href="#test5">邮件发送检测</a></li>
</ul>

<!--服务器性能检测-->
<div id="test1">
	<table class="bordered highlight">
		<tr align="center">
			<td width="19%">参照对象</td>
			<td width="17%">整数运算能力检测<br/>(1+1运算300万次)</td>
			<td width="17%">浮点运算能力检测<br/>(圆周率开平方300万次)</td>
			<td width="17%">数据I/O能力检测<br/>(读取10K文件1万次)</td>
			<td width="30%">CPU信息</td>
		</tr>
		<tr align="center">
			<td align="left">美国 LinodeVPS</td>
			<td>0.357秒</td>
			<td>0.802秒</td>
			<td>0.023秒</td>
			<td align="left">4 x Xeon L5520 @ 2.27GHz</td>
		</tr>
		<tr align="center">
			<td align="left">美国 PhotonVPS.com</td>
			<td>0.431秒</td>
			<td>1.024秒</td>
			<td>0.034秒</td>
			<td align="left">8 x Xeon E5520 @ 2.27GHz</td>
		</tr>
		<tr align="center">
			<td align="left">德国 SpaceRich.com</td>
			<td>0.421秒</td>
			<td>1.003秒</td>
			<td>0.038秒</td>
			<td align="left">4 x Core i7 920 @ 2.67GHz</td>
		</tr>
		<tr align="center">
			<td align="left">美国 RiZie.com</td>
			<td>0.521秒</td>
			<td>1.559秒</td>
			<td>0.054秒</td>
			<td align="left">2 x Pentium4 3.00GHz</td>
		</tr>
		<tr align="center">
			<td align="left">埃及 CitynetHost.com</a></td>
			<td>0.343秒</td>
			<td>0.761秒</td>
			<td>0.023秒</td>
			<td align="left">2 x Core2Duo E4600 @ 2.40GHz</td>
		</tr>
		<tr align="center">
			<td align="left">美国 IXwebhosting.com</td>
			<td>0.535秒</td>
			<td>1.607秒</td>
			<td>0.058秒</td>
			<td align="left">4 x Xeon E5530 @ 2.40GHz</td>
		</tr>
		<tr align="center">
			<td>本台服务器</td>
			<td>
				<span id="pInt">未测试</span>
				<br/>
				<button class="btn waves-effect waves-light ajax" data-service="Tz.DoTest" data-type="pInt">整型测试</button>
			</td>
			<td>
				<span id="pFloat">未测试</span>
				<br/>
				<button class="btn waves-effect waves-light ajax" data-service="Tz.DoTest" data-type="pFloat">浮点测试</button>
			</td>
			<td>
				<span id="pIo">未测试</span>
				<br/>
				<button class="btn waves-effect waves-light ajax" data-service="Tz.DoTest" data-type="pIo">IO测试</button>
			</td>
			<td></td>
		</tr>
	</table>
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
				<?php echo (isset($_GET['speed'])) ? "下载1000KB数据用时 <font color='#cc0000'>" . $_GET['speed'] . "</font> 毫秒，下载速度：" . "<font color='#cc0000'>" . $speed . "</font>" . " kb/s，需测试多次取平均值，超过10M直接看下载速度" : "<font color='#cc0000'>&nbsp;未探测&nbsp;</font>" ?>
			</td>
		</tr>
		<tr>
			<td class="center">
				<button class="btn waves-effect waves-light ajax" data-service="Tz.DoTest" data-type="Speed">开始测试</button>
			</td>
		</tr>
	</table>
</div>

<!--MySQL数据库连接检测-->
<div id="test3">
	<table class="bordered highlight">
		<tr>
			<td>
				地址：<input type="text" name="host" value="localhost"/>
				端口：<input type="text" name="port" value="3306"/>
				用户名：<input type="text" name="login"/>
				密码：<input type="password" name="password"/>
			</td>
		</tr>
		<tr>
			<td class="center">
				<input class="btn" type="submit" name="act" value="MySQL检测"/>
			</td>
		</tr>
	</table>
</div>

<!--函数检测-->
<div id="test4">
	<table class="bordered highlight centered">
		<tr>
			<td class="center">
				请输入您要检测的函数：

			</td>
		</tr>
		<tr>
			<td class="center">
				<input type="text" name="funName" size="50"/>
			</td>
		</tr>
		<tr>
			<td class="center">
				<input class="btn" type="submit" name="act" align="right" value="函数检测"/>
			</td>
		</tr>
	</table>
</div>

<!--邮件发送检测-->
<div id="test5">
	<table class="bordered highlight">
		<tr>
			<td class="center">
				请输入您要检测的邮件地址：
			</td>
		</tr>
		<tr>
			<td class="center">
				<input type="text" name="mailAdd" size="50"/>
			</td>
		</tr>
		<tr>
			<td class="center">
				<input class="btn" type="submit" name="act" value="邮件检测"/>
			</td>
		</tr>
	</table>
</div>


<script>
	$().ready(function () {
		$('.ajax').click(function () {
			var $this = $(this);
			var data = $this.data();
			console.log(data);
			var id_name = "#" + data.type;
			console.log(id_name);
			Ajax(data, function (d) {
				if (d.ret == 200) {
					$(id_name).html(d.data);
				} else {
					Materialize.toast(d.msg, 2000, 'rounded');
				}
			});
		})
	})
</script>