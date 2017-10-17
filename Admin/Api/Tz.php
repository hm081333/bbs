<?php
/**
 * Created by PhpStorm.
 * User: 123
 * Date: 2017/9/8
 * Time: 21:36
 */

class Api_Tz extends PhalApi_Api
{
	public function getRules()
	{
		return array(
			'info' => array(),
			'test' => array(),
			'cpuPercentage' => array(),
			'doTest' => array(),
		);
	}

	public function phpInfo()
	{
		DI()->view->show('phpinfo');
	}

	public function info()
	{
		$sys_info = Domain_Tz::getSysInfo();
		$phpSelf = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
		$stat1 = Domain_Tz::GetCoreInformation();
		sleep(1);
		$stat2 = Domain_Tz::GetCoreInformation();
		$data = Domain_Tz::GetCpuPercentages($stat1, $stat2);
		$cpu_show = $data['cpu0']['user'] . "%us,  " . $data['cpu0']['sys'] . "%sy,  " . $data['cpu0']['nice'] . "%ni, " . $data['cpu0']['idle'] . "%id,  " . $data['cpu0']['iowait'] . "%wa,  " . $data['cpu0']['irq'] . "%irq,  " . $data['cpu0']['softirq'] . "%softirq";
		DI()->view->assign(array('sysInfo' => $sys_info['sysInfo'], 'sysReShow' => $sys_info['sysReShow'], 'phpSelf' => $phpSelf, 'cpu_show' => $cpu_show));
		DI()->view->show('tz');
	}

	public function getInfo()
	{
		//硬盘
		$dt = round(@disk_total_space(".") / (1024 * 1024 * 1024), 3); //总
		$df = round(@disk_free_space(".") / (1024 * 1024 * 1024), 3); //可用
		$du = $dt - $df; //已用
		$hdPercent = (floatval($dt) != 0) ? round($du / $dt * 100, 2) : 0;
		$sys_info = Domain_Tz::getSysInfo();
		$sysInfo = $sys_info['sysInfo'];
		//系统负载
		$load = $sysInfo['loadAvg'];
		//在线时间
		$uptime = $sysInfo['uptime'];
		//系统当前时间
		$stime = date('Y-m-d H:i:s', NOW_TIME);
		//判断内存如果小于1G，就显示M，否则显示G单位
		if ($sysInfo['memTotal'] < 1024) {
			$memTotal = $sysInfo['memTotal'] . " M";
			$mt = $sysInfo['memTotal'] . " M";
			$mu = $sysInfo['memUsed'] . " M";
			$mf = $sysInfo['memFree'] . " M";
			$mc = $sysInfo['memCached'] . " M";    //cache化内存
			$mb = $sysInfo['memBuffers'] . " M";    //缓冲
			$st = $sysInfo['swapTotal'] . " M";
			$su = $sysInfo['swapUsed'] . " M";
			$sf = $sysInfo['swapFree'] . " M";
			$swapPercent = $sysInfo['swapPercent'];
			$memRealUsed = $sysInfo['memRealUsed'] . " M"; //真实内存使用
			$memRealFree = $sysInfo['memRealFree'] . " M"; //真实内存空闲
			$memRealPercent = $sysInfo['memRealPercent']; //真实内存使用比率
			$memPercent = $sysInfo['memPercent']; //内存总使用率
			$memCachedPercent = $sysInfo['memCachedPercent']; //cache内存使用率
		} else {
			$memTotal = round($sysInfo['memTotal'] / 1024, 3) . " G";
			$mt = round($sysInfo['memTotal'] / 1024, 3) . " G";
			$mu = round($sysInfo['memUsed'] / 1024, 3) . " G";
			$mf = round($sysInfo['memFree'] / 1024, 3) . " G";
			$mc = round($sysInfo['memCached'] / 1024, 3) . " G";
			$mb = round($sysInfo['memBuffers'] / 1024, 3) . " G";
			$st = round($sysInfo['swapTotal'] / 1024, 3) . " G";
			$su = round($sysInfo['swapUsed'] / 1024, 3) . " G";
			$sf = round($sysInfo['swapFree'] / 1024, 3) . " G";
			$swapPercent = $sysInfo['swapPercent'];
			$memRealUsed = round($sysInfo['memRealUsed'] / 1024, 3) . " G"; //真实内存使用
			$memRealFree = round($sysInfo['memRealFree'] / 1024, 3) . " G"; //真实内存空闲
			$memRealPercent = $sysInfo['memRealPercent']; //真实内存使用比率
			$memPercent = $sysInfo['memPercent']; //内存总使用率
			$memCachedPercent = $sysInfo['memCachedPercent']; //cache内存使用率
		}
		//网卡流量
		$strs = @file("/proc/net/dev");
		for ($i = 2; $i < count($strs); $i++) {
			preg_match_all("/([^\s]+):[\s]{0,}(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/", $strs[$i], $info);
			$NetOutSpeed[$i] = $info[10][0];
			$NetInputSpeed[$i] = $info[2][0];
			$NetInput[$i] = Domain_Tz::formatsize($info[2][0]);
			$NetOut[$i] = Domain_Tz::formatsize($info[10][0]);
		}

		$stat1 = Domain_Tz::GetCoreInformation();
		sleep(1);
		$stat2 = Domain_Tz::GetCoreInformation();
		$data = Domain_Tz::GetCpuPercentages($stat1, $stat2);
		$cpu_show = $data['cpu0']['user'] . "%us,  " . $data['cpu0']['sys'] . "%sy,  " . $data['cpu0']['nice'] . "%ni, " . $data['cpu0']['idle'] . "%id,  " . $data['cpu0']['iowait'] . "%wa,  " . $data['cpu0']['irq'] . "%irq,  " . $data['cpu0']['softirq'] . "%softirq";

		$arr = array();
		$arr['cpu_show'] = $cpu_show;
		$arr['useSpace'] = $du;
		$arr['freeSpace'] = $df;
		$arr['hdPercent'] = $hdPercent;
		$arr['TotalMemory'] = $mt;
		$arr['UsedMemory'] = $mu;
		$arr['FreeMemory'] = $mf;
		$arr['CachedMemory'] = $mc;
		$arr['Buffers'] = $mb;
		$arr['TotalSwap'] = $st;
		$arr['swapUsed'] = $su;
		$arr['swapFree'] = $sf;
		$arr['loadAvg'] = $load;
		$arr['uptime'] = $uptime;
		//$arr['freetime'] = $freetime;
		$arr['stime'] = $stime;
		$arr['memRealPercent'] = $memRealPercent;
		$arr['memRealUsed'] = $memRealUsed;
		$arr['memRealFree'] = $memRealFree;
		$arr['memCachedPercent'] = $memCachedPercent;
		$arr['swapPercent'] = $swapPercent;
		$arr['NetOut2'] = $NetOut[2];
		$arr['NetOut3'] = $NetOut[3];
		$arr['NetOut4'] = $NetOut[4];
		$arr['NetOut5'] = $NetOut[5];
		$arr['NetOut6'] = $NetOut[6];
		$arr['NetOut7'] = $NetOut[7];
		$arr['NetOut8'] = $NetOut[8];
		$arr['NetOut9'] = $NetOut[9];
		$arr['NetOut10'] = $NetOut[10];
		$arr['NetInput2'] = $NetInput[2];
		$arr['NetInput3'] = $NetInput[3];
		$arr['NetInput4'] = $NetInput[4];
		$arr['NetInput5'] = $NetInput[5];
		$arr['NetInput6'] = $NetInput[6];
		$arr['NetInput7'] = $NetInput[7];
		$arr['NetInput8'] = $NetInput[8];
		$arr['NetInput9'] = $NetInput[9];
		$arr['NetInput10'] = $NetInput[10];
		$arr['NetOutSpeed2'] = $NetOutSpeed[2];
		$arr['NetOutSpeed3'] = $NetOutSpeed[3];
		$arr['NetOutSpeed4'] = $NetOutSpeed[4];
		$arr['NetOutSpeed5'] = $NetOutSpeed[5];
		$arr['NetInputSpeed2'] = $NetInputSpeed[2];
		$arr['NetInputSpeed3'] = $NetInputSpeed[3];
		$arr['NetInputSpeed4'] = $NetInputSpeed[4];
		$arr['NetInputSpeed5'] = $NetInputSpeed[5];
		$arr['memPercent'] = $memPercent . "%";
		$arr['barhdPercent'] = $arr['hdPercent'] . "%";
		$arr['barmemCachedPercent'] = $arr['memCachedPercent'] . "%";
		$arr['barmemRealPercent'] = $arr['memRealPercent'] . "%";
		$arr['barswapPercent'] = $arr['swapPercent'] . "%";
		return $arr;
	}

	public function cpuPercentage()
	{
		$stat1 = Domain_Tz::GetCoreInformation();
		sleep(1);
		$stat2 = Domain_Tz::GetCoreInformation();
		$data = Domain_Tz::GetCpuPercentages($stat1, $stat2);
		DI()->view->assign(array('data' => $data));
		DI()->view->show('cpu_percentage');
	}

	public function getCpuPercentage()
	{
		$stat1 = Domain_Tz::GetCoreInformation();
		sleep(1);
		$stat2 = Domain_Tz::GetCoreInformation();
		$data = Domain_Tz::GetCpuPercentages1($stat1, $stat2);
		if ($data) {
			return $data;
		} else {
			throw new PhalApi_Exception_Error('获取cpu使用率失败');
		}
	}

	public function test()
	{
		DI()->view->show('tz_test');
	}

	public function doTest()
	{
		$post = DI()->request->getAll();
		unset($post['service']);
		switch ($post['type']) {
			case 'pInt':
				return Domain_Tz::testInt();
				break;
			case 'pFloat':
				return Domain_Tz::testFloat();
				break;
			case 'pIo':
				return Domain_Tz::testIo();
				break;
			case 'pSpeed':
				return Domain_Tz::testSpeed();
				break;
			case 'pMySQL':
				return array('type' => $post['type'], 'result' => Domain_Tz::testMySQLi($post));
				break;
			case 'pFun':
				return array('type' => $post['type'], 'result' => Domain_Tz::testFun($post));
				break;
			case 'pMail':
				return array('type' => $post['type'], 'result' => Domain_Tz::testMail($post));
				break;
			default:
				throw new PhalApi_Exception(T('没有该测试'));
				break;
		}
	}


}