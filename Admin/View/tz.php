<ul class="tabs">
	<li class="tab col s3"><a href="#p1">服务器参数</a></li>
	<li class="tab col s3"><a href="#p2">服务器实时数据</a></li>
	<li class="tab col s3"><a href="#p3">Test 3</a></li>
</ul>

<!--
<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="">
</td>
-->

<!--服务器相关参数-->
<div id="p1" class="blue">
	<table class="bordered highlight" style="table-layout: fixed;">
		<tr>
			<td width="35%">服务器域名<br/>IP地址</td>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="
				<?php echo @get_current_user(); ?> - <?php echo $_SERVER['SERVER_NAME']; ?>(<?php if ('/' == DIRECTORY_SEPARATOR) {
				echo $_SERVER['SERVER_ADDR'];
			} else {
				echo @gethostbyname($_SERVER['SERVER_NAME']);
			} ?>)&nbsp;&nbsp;你的IP地址是：<?php echo @$_SERVER['REMOTE_ADDR']; ?>">
				<?php echo @get_current_user(); ?> - <?php echo $_SERVER['SERVER_NAME']; ?>(<?php if ('/' == DIRECTORY_SEPARATOR) {
					echo $_SERVER['SERVER_ADDR'];
				} else {
					echo @gethostbyname($_SERVER['SERVER_NAME']);
				} ?>)&nbsp;&nbsp;你的IP地址是：<?php echo @$_SERVER['REMOTE_ADDR']; ?>
			</td>
		</tr>
		<tr>
			<td>服务器标识</td>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="
				<?php if ($sysInfo['win_n'] != '') {
				echo $sysInfo['win_n'];
			} else {
				echo @php_uname();
			}; ?>
					">
				<?php if ($sysInfo['win_n'] != '') {
					echo $sysInfo['win_n'];
				} else {
					echo @php_uname();
				}; ?>
			</td>
		</tr>
		<tr>
			<td>服务器操作系统</td>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="<?php $os = explode(" ", php_uname());
			echo $os[0]; ?> &nbsp;内核版本：<?php if ('/' == DIRECTORY_SEPARATOR) {
				echo $os[2];
			} else {
				echo $os[1];
			} ?>">
				<?php $os = explode(" ", php_uname());
				echo $os[0]; ?> &nbsp;内核版本：<?php if ('/' == DIRECTORY_SEPARATOR) {
					echo $os[2];
				} else {
					echo $os[1];
				} ?>
			</td>
		</tr>
		<tr>
			<td>服务器解译引擎</td>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="<?php echo $_SERVER['SERVER_SOFTWARE']; ?>">
				<?php echo $_SERVER['SERVER_SOFTWARE']; ?>
			</td>
		</tr>
		<tr>
			<td>服务器语言</td>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="<?php echo getenv("HTTP_ACCEPT_LANGUAGE"); ?>">
				<?php echo getenv("HTTP_ACCEPT_LANGUAGE"); ?>
			</td>
		</tr>
		<tr>
			<td>服务器端口</td>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="<?php echo $_SERVER['SERVER_PORT']; ?>">
				<?php echo $_SERVER['SERVER_PORT']; ?>
			</td>
		</tr>
		<tr>
			<td>服务器主机名</td>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="<?php if ('/' == DIRECTORY_SEPARATOR) {
				echo $os[1];
			} else {
				echo $os[2];
			} ?>">
				<?php if ('/' == DIRECTORY_SEPARATOR) {
					echo $os[1];
				} else {
					echo $os[2];
				} ?>
			</td>
		</tr>
		<tr>
			<td>绝对路径</td>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50"
				data-tooltip="<?php echo $_SERVER['DOCUMENT_ROOT'] ? str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) : str_replace('\\', '/', dirname(__FILE__)); ?>">
				<?php echo $_SERVER['DOCUMENT_ROOT'] ? str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) : str_replace('\\', '/', dirname(__FILE__)); ?>
			</td>
		</tr>
		<tr>
			<td>管理员邮箱</td>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="<?php echo $_SERVER['SERVER_ADMIN']; ?>">
				<?php echo $_SERVER['SERVER_ADMIN']; ?>
			</td>
		</tr>
		<tr>
			<td>探针路径</td>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50"
				data-tooltip="<?php echo str_replace('\\', '/', __FILE__) ? str_replace('\\', '/', __FILE__) : $_SERVER['SCRIPT_FILENAME']; ?>">
				<?php echo str_replace('\\', '/', __FILE__) ? str_replace('\\', '/', __FILE__) : $_SERVER['SCRIPT_FILENAME']; ?>
			</td>
		</tr>
	</table>
</div>

<!--服务器实时数据-->
<div id="p2" class="red">
	<table>
		<tr>
			<td width="13%">服务器当前时间</td>
			<td width="37%"><span id="stime"><?php echo $stime; ?></span></td>
			<td width="13%">服务器已运行时间</td>
			<td width="37%" colspan="3"><span id="uptime"><?php echo $uptime; ?></span></td>
		</tr>
		<tr>
			<td width="13%">CPU型号 [<?php echo $sysInfo['cpu']['num']; ?>核]</td>
			<td width="87%" colspan="5"><?php echo $sysInfo['cpu']['model']; ?></td>
		</tr>
		<tr>
			<td>CPU使用状况</td>
			<td colspan="5"><?php if ('/' == DIRECTORY_SEPARATOR) {
					echo $cpu_show . " | <a href='" . $phpSelf . "?act=cpu_percentage' target='_blank' class='static'>查看图表</a>";
				} else {
					echo "暂时只支持Linux系统";
				} ?>
			</td>
		</tr>
		<tr>
			<td>硬盘使用状况</td>
			<td colspan="5">
				总空间 <?php echo $dt; ?>&nbsp;G，
				已用 <font color='#333333'><span id="useSpace"><?php echo $du; ?></span></font>&nbsp;G，
				空闲 <font color='#333333'><span id="freeSpace"><?php echo $df; ?></span></font>&nbsp;G，
				使用率 <span id="hdPercent"><?php echo $hdPercent; ?></span>%
				<div class="bar">
					<div id="barhdPercent" class="barli_black" style="width:<?php echo $hdPercent; ?>%">&nbsp;</div>
				</div>
			</td>
		</tr>
		<tr>
			<td>内存使用状况</td>
			<td colspan="5">
				<?php
				$tmp = array(
					'memTotal', 'memUsed', 'memFree', 'memPercent',
					'memCached', 'memRealPercent',
					'swapTotal', 'swapUsed', 'swapFree', 'swapPercent'
				);
				foreach ($tmp AS $v) {
					$sysInfo[$v] = $sysInfo[$v] ? $sysInfo[$v] : 0;
				}
				?>
				物理内存：共
				<font color='#CC0000'><?php echo $memTotal; ?> </font>
				, 已用
				<font color='#CC0000'><span id="UsedMemory"><?php echo $mu; ?></span></font>
				, 空闲
				<font color='#CC0000'><span id="FreeMemory"><?php echo $mf; ?></span></font>
				, 使用率
				<span id="memPercent"><?php echo $memPercent; ?></span>
				<div class="bar">
					<div id="barmemPercent" class="barli_green" style="width:<?php echo $memPercent ?>%">&nbsp;</div>
				</div>
				<?php
				//判断如果cache为0，不显示
				if ($sysInfo['memCached'] > 0) {
					?>
					Cache化内存为 <span id="CachedMemory"><?php echo $mc; ?></span>
					, 使用率
					<span id="memCachedPercent"><?php echo $memCachedPercent; ?></span>
					% | Buffers缓冲为  <span id="Buffers"><?php echo $mb; ?></span>
					<div class="bar">
						<div id="barmemCachedPercent" class="barli_blue" style="width:<?php echo $memCachedPercent ?>%">&nbsp;</div>
					</div>

					真实内存使用
					<span id="memRealUsed"><?php echo $memRealUsed; ?></span>
					, 真实内存空闲
					<span id="memRealFree"><?php echo $memRealFree; ?></span>
					, 使用率
					<span id="memRealPercent"><?php echo $memRealPercent; ?></span>
					%
					<div class="bar_1">
						<div id="barmemRealPercent" class="barli_1" style="width:<?php echo $memRealPercent ?>%">&nbsp;</div>
					</div>
					<?php
				}
				//判断如果SWAP区为0，不显示
				if ($sysInfo['swapTotal'] > 0) {
					?>
					SWAP区：共
					<?php echo $st; ?>
					, 已使用
					<span id="swapUsed"><?php echo $su; ?></span>
					, 空闲
					<span id="swapFree"><?php echo $sf; ?></span>
					, 使用率
					<span id="swapPercent"><?php echo $swapPercent; ?></span>
					%
					<div class="bar">
						<div id="barswapPercent" class="barli_red" style="width:<?php echo $swapPercent ?>%">&nbsp;</div>
					</div>

					<?php
				}
				?>
			</td>
		</tr>
		<tr>
			<td>系统平均负载</td>
			<td colspan="5" class="w_number"><span id="loadAvg"><?php echo $load; ?></span></td>
		</tr>

	</table>
</div>
<div id="p3" class="green">
</div>