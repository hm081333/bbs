<style>
	table {
		table-layout: fixed;
	}
</style>

<ul class="tabs">
	<li class="tab"><a href="#p1">服务器参数</a></li>
	<li class="tab"><a href="#p2">服务器实时数据</a></li>
	<li class="tab"><a href="#p3">PHP已编译模块检测</a></li>
	<li class="tab"><a href="#p4">PHP相关参数</a></li>
	<li class="tab"><a href="#p5">组件支持</a></li>
</ul>

<!--
<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="">
</td>
-->

<!--服务器相关参数-->
<div id="p1" class="blue">
	<table class="bordered highlight">
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
	<table class="bordered highlight">
		<tr>
			<td>服务器当前时间</td>
			<td><span id="stime"><?php echo $stime; ?></span></td>
		</tr>
		<tr>
			<td>服务器已运行时间</td>
			<td><span id="uptime"><?php echo $uptime; ?></span></td>
		</tr>
		<tr>
			<td>CPU型号 [<?php echo $sysInfo['cpu']['num']; ?>核]</td>
			<td><?php echo $sysInfo['cpu']['model']; ?></td>
		</tr>
		<tr>
			<td>CPU使用状况</td>
			<td>
				<?php if ('/' == DIRECTORY_SEPARATOR) {
					echo $cpu_show . " | <a href='" . $phpSelf . "?act=cpu_percentage' target='_blank' class='static'>查看图表</a>";
				} else {
					echo "暂时只支持Linux系统";
				} ?>
			</td>
		</tr>
		<tr>
			<td>硬盘使用状况</td>
			<td>
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
			<td>
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
			<td class="w_number"><span id="loadAvg"><?php echo $load; ?></span></td>
		</tr>

	</table>
</div>

<!--PHP已编译模块检测-->
<div id="p3" class="green">
	<?php $able = get_loaded_extensions(); ?>
	<table class="bordered highlight centered">
		<?php foreach ($able as $key => $value) : ?>
			<?php if ($key == 0) : ?>
				<?php echo '<tr>' ?>
			<?php endif; ?>
			<?php if ($key != 0 && $key % 4 == 0) : ?>
				<?php echo '</tr><tr>' ?>
			<?php endif; ?>
			<?php echo '<td>', $value, '</td>'; ?>
		<?php endforeach; ?>
	</table>
</div>

<!--PHP相关参数-->
<div id="p4" class="cyan">
	<table class="bordered highlight">
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="PHP信息（phpinfo）">PHP信息（phpinfo）：</td>
			<td width="30%">
				<?php
				$disFuns = get_cfg_var("disable_functions");
				?>
				<?php echo (false !== preg_match("phpinfo", $disFuns)) ? '<font color="red">×</font>' : "<a href='$phpSelf?act=phpinfo' target='_blank'>PHPINFO</a>"; ?>
			</td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="PHP版本（php_version）">PHP版本（php_version）：</td>
			<td><?php echo PHP_VERSION; ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="PHP运行方式">PHP运行方式：</td>
			<td><?php echo strtoupper(php_sapi_name()); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="脚本占用最大内存（memory_limit）">脚本占用最大内存（memory_limit）：</td>
			<td><?php echo Domain_Tz::show("memory_limit"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="PHP安全模式（safe_mode）">PHP安全模式（safe_mode）：</td>
			<td><?php echo Domain_Tz::show("safe_mode"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="POST方法提交最大限制（post_max_size）">POST方法提交最大限制（post_max_size）：</td>
			<td><?php echo Domain_Tz::show("post_max_size"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="上传文件最大限制（upload_max_filesize）">上传文件最大限制（upload_max_filesize）：</td>
			<td><?php echo Domain_Tz::show("upload_max_filesize"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="浮点型数据显示的有效位数（precision）">浮点型数据显示的有效位数（precision）：</td>
			<td><?php echo Domain_Tz::show("precision"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="脚本超时时间（max_execution_time）">脚本超时时间（max_execution_time）：</td>
			<td><?php echo Domain_Tz::show("max_execution_time"); ?>秒</td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="socket超时时间（default_socket_timeout）">socket超时时间（default_socket_timeout）：</td>
			<td><?php echo Domain_Tz::show("default_socket_timeout"); ?>秒</td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="PHP页面根目录（doc_root）">PHP页面根目录（doc_root）：</td>
			<td><?php echo Domain_Tz::show("doc_root"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="用户根目录（user_dir）">用户根目录（user_dir）：</td>
			<td><?php echo Domain_Tz::show("user_dir"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="dl()函数（enable_dl）">dl()函数（enable_dl）：</td>
			<td><?php echo Domain_Tz::show("enable_dl"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="指定包含文件目录（include_path）">指定包含文件目录（include_path）：</td>
			<td><?php echo Domain_Tz::show("include_path"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="显示错误信息（display_errors）">显示错误信息（display_errors）：</td>
			<td><?php echo Domain_Tz::show("display_errors"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="自定义全局变量（register_globals）">自定义全局变量（register_globals）：</td>
			<td><?php echo Domain_Tz::show("register_globals"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="数据反斜杠转义（magic_quotes_gpc）">数据反斜杠转义（magic_quotes_gpc）：</td>
			<td><?php echo Domain_Tz::show("magic_quotes_gpc"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip='"&lt;?...?&gt;"短标签（short_open_tag）'>"&lt;?...?&gt;"短标签（short_open_tag）：</td>
			<td><?php echo Domain_Tz::show("short_open_tag"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip='"&lt;% %&gt;"ASP风格标记（asp_tags）'>"&lt;% %&gt;"ASP风格标记（asp_tags）：</td>
			<td><?php echo Domain_Tz::show("asp_tags"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="忽略重复错误信息（ignore_repeated_errors）">忽略重复错误信息（ignore_repeated_errors）：</td>
			<td><?php echo Domain_Tz::show("ignore_repeated_errors"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="忽略重复的错误源（ignore_repeated_source）">忽略重复的错误源（ignore_repeated_source）：</td>
			<td><?php echo Domain_Tz::show("ignore_repeated_source"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="报告内存泄漏（report_memleaks）">报告内存泄漏（report_memleaks）：</td>
			<td><?php echo Domain_Tz::show("report_memleaks"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="自动字符串转义（magic_quotes_gpc）">自动字符串转义（magic_quotes_gpc）：</td>
			<td><?php echo Domain_Tz::show("magic_quotes_gpc"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="外部字符串自动转义（magic_quotes_runtime）">外部字符串自动转义（magic_quotes_runtime）：</td>
			<td><?php echo Domain_Tz::show("magic_quotes_runtime"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="打开远程文件（allow_url_fopen）">打开远程文件（allow_url_fopen）：</td>
			<td><?php echo Domain_Tz::show("allow_url_fopen"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="声明argv和argc变量（register_argc_argv）">声明argv和argc变量（register_argc_argv）：</td>
			<td><?php echo Domain_Tz::show("register_argc_argv"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="Cookie 支持">Cookie 支持：</td>
			<td><?php echo isset($_COOKIE) ? '<font color="green">√</font>' : '<font color="red">×</font>'; ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="拼写检查（ASpell Library）">拼写检查（ASpell Library）：</td>
			<td><?php echo Domain_Tz::isfun("aspell_check_raw"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="高精度数学运算（BCMath）">高精度数学运算（BCMath）：</td>
			<td><?php echo Domain_Tz::isfun("bcadd"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="PREL相容语法（PCRE）">PREL相容语法（PCRE）：</td>
			<td><?php echo Domain_Tz::isfun("preg_match"); ?></td>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="PDF文档支持">PDF文档支持：</td>
			<td><?php echo Domain_Tz::isfun("pdf_close"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="SNMP网络管理协议">SNMP网络管理协议：</td>
			<td><?php echo Domain_Tz::isfun("snmpget"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="VMailMgr邮件处理">VMailMgr邮件处理：</td>
			<td><?php echo Domain_Tz::isfun("vm_adduser"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="Curl支持">Curl支持：</td>
			<td><?php echo Domain_Tz::isfun("curl_init"); ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="SMTP支持">SMTP支持：</td>
			<td><?php echo get_cfg_var("SMTP") ? '<font color="green">√</font>' : '<font color="red">×</font>'; ?></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="SMTP地址">SMTP地址：</td>
			<td><?php echo get_cfg_var("SMTP") ? get_cfg_var("SMTP") : '<font color="red">×</font>'; ?></td>
		</tr>

		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="默认支持函数（enable_functions）">默认支持函数（enable_functions）：</td>
			<td><a href='<?php echo $phpSelf; ?>?act=Function' target='_blank' class='static'>查看</a></td>
		</tr>
		<tr>
			<td class="truncate tooltipped" data-position="bottom" data-delay="50" data-tooltip="被禁用的函数（disable_functions）">被禁用的函数（disable_functions）：</td>
			<td><a href='#' target='_blank' class='static'>查看</a></td>
			<!--<td>
				<?php
			/*				$disFuns = get_cfg_var("disable_functions");
							if (empty($disFuns)) {
								echo '<font color=red>×</font>';
							} else {
								$disFuns_array = explode(',', $disFuns);
								foreach ($disFuns_array as $key => $value) {
									if ($key != 0 && $key % 5 == 0) {
										echo '<br />';
									}
									echo "$value&nbsp;&nbsp;";
								}
							}
							*/ ?>
			</td>-->
		</tr>

	</table>
</div>

<!--组件支持-->
<div id="p5" class="light-blue">
	<table class="bordered highlight">
		<tr>
			<td width="32%">FTP支持：</td>
			<td width="18%"><?php echo Domain_Tz::isfun("ftp_login"); ?></td>
			<td width="32%">XML解析支持：</td>
			<td width="18%"><?php echo Domain_Tz::isfun("xml_set_object"); ?></td>
		</tr>
		<tr>
			<td>Session支持：</td>
			<td><?php echo Domain_Tz::isfun("session_start"); ?></td>
			<td>Socket支持：</td>
			<td><?php echo Domain_Tz::isfun("socket_accept"); ?></td>
		</tr>
		<tr>
			<td>Calendar支持</td>
			<td><?php echo Domain_Tz::isfun('cal_days_in_month'); ?>
			</td>
			<td>允许URL打开文件：</td>
			<td><?php echo Domain_Tz::show("allow_url_fopen"); ?></td>
		</tr>
		<tr>
			<td>GD库支持：</td>
			<td>
				<?php
				if (function_exists('gd_info')) {
					$gd_info = @gd_info();
					echo $gd_info["GD Version"];
				} else {
					echo '<font color="red">×</font>';
				}
				?></td>
			<td>压缩文件支持(Zlib)：</td>
			<td><?php echo Domain_Tz::isfun("gzclose"); ?></td>
		</tr>
		<tr>
			<td>IMAP电子邮件系统函数库：</td>
			<td><?php echo Domain_Tz::isfun("imap_close"); ?></td>
			<td>历法运算函数库：</td>
			<td><?php echo Domain_Tz::isfun("JDToGregorian"); ?></td>
		</tr>
		<tr>
			<td>正则表达式函数库：</td>
			<td><?php echo Domain_Tz::isfun("preg_match"); ?></td>
			<td>WDDX支持：</td>
			<td><?php echo Domain_Tz::isfun("wddx_add_vars"); ?></td>
		</tr>
		<tr>
			<td>Iconv编码转换：</td>
			<td><?php echo Domain_Tz::isfun("iconv"); ?></td>
			<td>mbstring：</td>
			<td><?php echo Domain_Tz::isfun("mb_eregi"); ?></td>
		</tr>
		<tr>
			<td>高精度数学运算：</td>
			<td><?php echo Domain_Tz::isfun("bcadd"); ?></td>
			<td>LDAP目录协议：</td>
			<td><?php echo Domain_Tz::isfun("ldap_close"); ?></td>
		</tr>
		<tr>
			<td>MCrypt加密处理：</td>
			<td><?php echo Domain_Tz::isfun("mcrypt_encrypt"); ?></td>
			<td>哈稀计算：</td>
			<td><?php echo Domain_Tz::isfun("mhash_count"); ?></td>
		</tr>
	</table>
</div>