<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2017/9/27
 * Time: 14:25
 */

class Domain_Tz
{
	/**
	 * 根据不同系统取得CPU相关信息
	 */
	public static function getSysInfo()
	{
		error_reporting(0); //抑制所有错误信息
		switch (PHP_OS) {
			case "Linux":
				$sysReShow = (false !== ($sysInfo = self::sys_linux())) ? "show" : "none";
				break;
			case "FreeBSD":
				$sysReShow = (false !== ($sysInfo = self::sys_freebsd())) ? "show" : "none";
				break;
			/*case "WINNT":
				$sysReShow = (false !== ($sysInfo = sys_windows())) ? "show" : "none";
				break;*/
			default:
				break;
		}
		return array('sysInfo' => $sysInfo, 'sysReShow' => $sysReShow);
	}

	/**
	 * linux系统探测
	 * @return bool
	 */
	public static function sys_linux()
	{
		// CPU
		if (false === ($str = @file("/proc/cpuinfo"))) return false;
		$str = implode("", $str);
		@preg_match_all("/model\s+name\s{0,}\:+\s{0,}([\w\s\)\(\@.-]+)([\r\n]+)/s", $str, $model);
		@preg_match_all("/cpu\s+MHz\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $mhz);
		@preg_match_all("/cache\s+size\s{0,}\:+\s{0,}([\d\.]+\s{0,}[A-Z]+[\r\n]+)/", $str, $cache);
		@preg_match_all("/bogomips\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $bogomips);
		if (false !== is_array($model[1])) {
			$res['cpu']['num'] = sizeof($model[1]);
			/*for ($i = 0; $i < $res['cpu']['num']; $i++) {
				$res['cpu']['model'][] = $model[1][$i] . '&nbsp;(' . $mhz[1][$i] . ')';
				$res['cpu']['mhz'][] = $mhz[1][$i];
				$res['cpu']['cache'][] = $cache[1][$i];
				$res['cpu']['bogomips'][] = $bogomips[1][$i];
			}*/
			if ($res['cpu']['num'] == 1)
				$x1 = '';
			else
				$x1 = ' ×' . $res['cpu']['num'];
			$mhz[1][0] = ' | 频率:' . $mhz[1][0];
			$cache[1][0] = ' | 二级缓存:' . $cache[1][0];
			$bogomips[1][0] = ' | Bogomips:' . $bogomips[1][0];
			$res['cpu']['model'][] = $model[1][0] . $mhz[1][0] . $cache[1][0] . $bogomips[1][0] . $x1;
			if (false !== is_array($res['cpu']['model'])) $res['cpu']['model'] = implode("<br />", $res['cpu']['model']);
			if (false !== is_array($res['cpu']['mhz'])) $res['cpu']['mhz'] = implode("<br />", $res['cpu']['mhz']);
			if (false !== is_array($res['cpu']['cache'])) $res['cpu']['cache'] = implode("<br />", $res['cpu']['cache']);
			if (false !== is_array($res['cpu']['bogomips'])) $res['cpu']['bogomips'] = implode("<br />", $res['cpu']['bogomips']);
		}
		// NETWORK
		// UPTIME
		if (false === ($str = @file("/proc/uptime"))) return false;
		$str = explode(" ", implode("", $str));
		$str = trim($str[0]);
		$min = $str / 60;
		$hours = $min / 60;
		$days = floor($hours / 24);
		$hours = floor($hours - ($days * 24));
		$min = floor($min - ($days * 60 * 24) - ($hours * 60));
		if ($days !== 0) $res['uptime'] = $days . "天";
		if ($hours !== 0) $res['uptime'] .= $hours . "小时";
		$res['uptime'] .= $min . "分钟";
		// MEMORY
		if (false === ($str = @file("/proc/meminfo"))) return false;
		$str = implode("", $str);
		preg_match_all("/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?Cached\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buf);
		preg_match_all("/Buffers\s{0,}\:+\s{0,}([\d\.]+)/s", $str, $buffers);
		$res['memTotal'] = round($buf[1][0] / 1024, 2);
		$res['memFree'] = round($buf[2][0] / 1024, 2);
		$res['memBuffers'] = round($buffers[1][0] / 1024, 2);
		$res['memCached'] = round($buf[3][0] / 1024, 2);
		$res['memUsed'] = $res['memTotal'] - $res['memFree'];
		$res['memPercent'] = (floatval($res['memTotal']) != 0) ? round($res['memUsed'] / $res['memTotal'] * 100, 2) : 0;
		$res['memRealUsed'] = $res['memTotal'] - $res['memFree'] - $res['memCached'] - $res['memBuffers']; //真实内存使用
		$res['memRealFree'] = $res['memTotal'] - $res['memRealUsed']; //真实空闲
		$res['memRealPercent'] = (floatval($res['memTotal']) != 0) ? round($res['memRealUsed'] / $res['memTotal'] * 100, 2) : 0; //真实内存使用率
		$res['memCachedPercent'] = (floatval($res['memCached']) != 0) ? round($res['memCached'] / $res['memTotal'] * 100, 2) : 0; //Cached内存使用率
		$res['swapTotal'] = round($buf[4][0] / 1024, 2);
		$res['swapFree'] = round($buf[5][0] / 1024, 2);
		$res['swapUsed'] = round($res['swapTotal'] - $res['swapFree'], 2);
		$res['swapPercent'] = (floatval($res['swapTotal']) != 0) ? round($res['swapUsed'] / $res['swapTotal'] * 100, 2) : 0;
		// LOAD AVG
		if (false === ($str = @file("/proc/loadavg"))) return false;
		$str = explode(" ", implode("", $str));
		$str = array_chunk($str, 4);
		$res['loadAvg'] = implode(" ", $str[0]);
		return $res;
	}

	/**
	 * FreeBSD系统探测
	 * @return bool
	 */
	public static function sys_freebsd()
	{
		//CPU
		if (false === ($res['cpu']['num'] = self::get_key("hw.ncpu"))) return false;
		$res['cpu']['model'] = self::get_key("hw.model");
		//LOAD AVG
		if (false === ($res['loadAvg'] = self::get_key("vm.loadavg"))) return false;
		//UPTIME
		if (false === ($buf = self::get_key("kern.boottime"))) return false;
		$buf = explode(' ', $buf);
		$sys_ticks = time() - intval($buf[3]);
		$min = $sys_ticks / 60;
		$hours = $min / 60;
		$days = floor($hours / 24);
		$hours = floor($hours - ($days * 24));
		$min = floor($min - ($days * 60 * 24) - ($hours * 60));
		if ($days !== 0) $res['uptime'] = $days . "天";
		if ($hours !== 0) $res['uptime'] .= $hours . "小时";
		$res['uptime'] .= $min . "分钟";
		//MEMORY
		if (false === ($buf = self::get_key("hw.physmem"))) return false;
		$res['memTotal'] = round($buf / 1024 / 1024, 2);
		$str = self::get_key("vm.vmtotal");
		preg_match_all("/\nVirtual Memory[\:\s]*\(Total[\:\s]*([\d]+)K[\,\s]*Active[\:\s]*([\d]+)K\)\n/i", $str, $buff, PREG_SET_ORDER);
		preg_match_all("/\nReal Memory[\:\s]*\(Total[\:\s]*([\d]+)K[\,\s]*Active[\:\s]*([\d]+)K\)\n/i", $str, $buf, PREG_SET_ORDER);
		$res['memRealUsed'] = round($buf[0][2] / 1024, 2);
		$res['memCached'] = round($buff[0][2] / 1024, 2);
		$res['memUsed'] = round($buf[0][1] / 1024, 2) + $res['memCached'];
		$res['memFree'] = $res['memTotal'] - $res['memUsed'];
		$res['memPercent'] = (floatval($res['memTotal']) != 0) ? round($res['memUsed'] / $res['memTotal'] * 100, 2) : 0;
		$res['memRealPercent'] = (floatval($res['memTotal']) != 0) ? round($res['memRealUsed'] / $res['memTotal'] * 100, 2) : 0;
		return $res;
	}

	/**
	 * 取得参数值 FreeBSD
	 * @param $keyName
	 * @return mixed
	 */
	public static function get_key($keyName)
	{
		return self::do_command('sysctl', "-n $keyName");
	}

	/**
	 * 确定执行文件位置 FreeBSD
	 * @param $commandName
	 * @return bool|string
	 */
	public static function find_command($commandName)
	{
		$path = array('/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin');
		foreach ($path as $p) {
			if (@is_executable("$p/$commandName")) return "$p/$commandName";
		}
		return false;
	}

	/**
	 * 执行系统命令 FreeBSD
	 * @param $commandName
	 * @param $args
	 * @return bool|string
	 */
	public static function do_command($commandName, $args)
	{
		$buffer = "";
		if (false === ($command = self::find_command($commandName))) return false;
		if ($fp = @popen("$command $args", 'r')) {
			while (!@feof($fp)) {
				$buffer .= @fgets($fp, 4096);
			}
			return trim($buffer);
		}
		return false;
	}

	/**
	 * 检测PHP设置参数
	 * @param $varName
	 */
	public static function show($varName)
	{
		switch ($result = get_cfg_var($varName)) {
			case 0:
				return '<font color="red">×</font>';
				break;
			case 1:
				return '<font color="green">√</font>';
				break;
			default:
				return $result;
				break;
		}
	}

	/**
	 * 检测函数支持
	 * @param string $funName
	 * @return string
	 */
	public static function isfun($funName = '')
	{
		if (!$funName || trim($funName) == '' || preg_match('~[^a-z0-9\_]+~i', $funName, $tmp)) return '错误';
		return (false !== function_exists($funName)) ? '<font color="green">√</font>' : '<font color="red">×</font>';
	}

	/**
	 * CPU核心详情
	 * @return array
	 */
	public static function GetCoreInformation()
	{
		$data = file('/proc/stat');
		$cores = array();
		foreach ($data as $line) {
			if (preg_match('/^cpu[0-9]/', $line)) {
				$info = explode(' ', $line);
				$cores[] = array('user' => $info[1], 'nice' => $info[2], 'sys' => $info[3], 'idle' => $info[4], 'iowait' => $info[5], 'irq' => $info[6], 'softirq' => $info[7]);
			}
		}
		return $cores;
	}

	/**
	 * CPU使用率详情
	 * @param $stat1
	 * @param $stat2
	 * @return array|void
	 */
	public static function GetCpuPercentages($stat1, $stat2)
	{
		if (count($stat1) !== count($stat2)) {
			return;
		}
		$cpus = array();
		for ($i = 0, $l = count($stat1); $i < $l; $i++) {
			$dif = array();
			$dif['user'] = $stat2[$i]['user'] - $stat1[$i]['user'];
			$dif['nice'] = $stat2[$i]['nice'] - $stat1[$i]['nice'];
			$dif['sys'] = $stat2[$i]['sys'] - $stat1[$i]['sys'];
			$dif['idle'] = $stat2[$i]['idle'] - $stat1[$i]['idle'];
			$dif['iowait'] = $stat2[$i]['iowait'] - $stat1[$i]['iowait'];
			$dif['irq'] = $stat2[$i]['irq'] - $stat1[$i]['irq'];
			$dif['softirq'] = $stat2[$i]['softirq'] - $stat1[$i]['softirq'];
			$total = array_sum($dif);
			$cpu = array();
			foreach ($dif as $x => $y) $cpu[$x] = round($y / $total * 100, 2);
			$cpus['cpu' . $i] = $cpu;
		}
		return $cpus;
	}

	/**
	 * 单位转换
	 * @param $size
	 * @return string
	 */
	public static function formatsize($size)
	{
		$danwei = array(' B ', ' K ', ' M ', ' G ', ' T ');
		$allsize = array();
		$fsize = '';
		$i = 0;
		for ($i = 0; $i < 5; $i++) {
			if (floor($size / pow(1024, $i)) == 0) {
				break;
			}
		}
		for ($l = $i - 1; $l >= 0; $l--) {
			$allsize1[$l] = floor($size / pow(1024, $l));
			$allsize[$l] = $allsize1[$l] - $allsize1[$l + 1] * 1024;
		}
		$len = count($allsize);
		for ($j = $len - 1; $j >= 0; $j--) {
			$fsize = $fsize . $allsize[$j] . $danwei[$j];
		}
		return $fsize;
	}

	/**
	 * 整数运算能力测试
	 * @return int|string
	 */
	public static function testInt()
	{
		$timeStart = gettimeofday();
		for ($i = 0; $i < 3000000; $i++) {
			$t = 1 + 1;
		}
		$timeEnd = gettimeofday();
		$time = ($timeEnd["usec"] - $timeStart["usec"]) / 1000000 + $timeEnd["sec"] - $timeStart["sec"];
		$time = round($time, 3) . "秒";
		return $time;
	}

	/**
	 * 浮点运算能力测试
	 * @return int|string
	 */
	public static function testFloat()
	{
		//得到圆周率值
		$t = pi();
		$timeStart = gettimeofday();
		for ($i = 0; $i < 3000000; $i++) {
			//开平方
			sqrt($t);
		}
		$timeEnd = gettimeofday();
		$time = ($timeEnd["usec"] - $timeStart["usec"]) / 1000000 + $timeEnd["sec"] - $timeStart["sec"];
		$time = round($time, 3) . "秒";
		return $time;
	}

	/**
	 * IO能力测试
	 * @return string
	 */
	public static function testIo()
	{
		$fp = @fopen(PHPSELF, "r");
		$timeStart = gettimeofday();
		for ($i = 0; $i < 10000; $i++) {
			@fread($fp, 10240);
			@rewind($fp);
		}
		$timeEnd = gettimeofday();
		@fclose($fp);
		$time = ($timeEnd["usec"] - $timeStart["usec"]) / 1000000 + $timeEnd["sec"] - $timeStart["sec"];
		$time = round($time, 3) . "秒";
		return $time;
	}

	public static function testSpeed()
	{
		$kb = 10240;
		echo "streaming $kb Kb...<!-";
		flush();
		$time = explode(" ", microtime());
		$start = $time[0] + $time[1];
		for ($x = 0; $x < $kb; $x++) {
			echo str_pad('', 1024, '.');
			flush();
		}
		$time = explode(" ", microtime());
		$finish = $time[0] + $time[1];
		$deltat = $finish - $start;
		echo "-> Test finished in $deltat seconds. Your speed is " . round($kb / $deltat, 3) . "Kb/s";
	}

	public static function testMySQLi($data)
	{
		if (function_exists("mysqli_close") == 1) {
			$link = @mysqli_connect($data['host'] . ":" . $data['port'], $data['login'], $data['password']);
			if ($link) {
				return T('连接到MySql数据库正常');
			} else {
				return T('无法连接到MySql数据库！');
			}
		} else {
			return T('服务器不支持MySQL数据库！');
		}
	}


}