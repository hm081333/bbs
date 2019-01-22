<?php
/**
 * Created by PhpStorm.
 * User: LYi-Ho
 * Date: 2019-01-08
 * Time: 18:51:57
 */

namespace Common\Domain;

/**
 * 探针 领域层
 * Class Tz
 * @package Common\Domain
 * @author  LYi-Ho 2019-01-08 18:51:57
 */
class Tz
{
    use Common;
    /**
     * 获取服务器实时数据
     */
    public static function getServerRealTimeData()
    {
        /* 服务器信息 Begin */
        $sysInfo = self::getSysInfo();
        /* 服务器信息 End */
        /* CPU使用状况 Begin */
        $stat1 = self::GetCoreInformation();
        sleep(1);
        $stat2 = self::GetCoreInformation();
        $data = self::GetCpuPercentages($stat1, $stat2);
        $total = [
            'user' => 0,
            'sys' => 0,
            'nice' => 0,
            'idle' => 0,
            'iowait' => 0,
            'irq' => 0,
            'softirq' => 0,
        ];
        foreach ($data as $item) {
            $total['user'] += $item['user'];
            $total['sys'] += $item['sys'];
            $total['nice'] += $item['nice'];
            $total['idle'] += $item['idle'];
            $total['iowait'] += $item['iowait'];
            $total['irq'] += $item['irq'];
            $total['softirq'] += $item['softirq'];
        }
        $total = array_map(function ($item) use ($data) {
            return $item / count($data);
        }, $total);
        /* CPU使用状况 End */
        /* 硬盘使用状况 Begin */
        $hdUsage = [
            'total' => round(@disk_total_space(".") / (1024 * 1024 * 1024), 3),// 总
            'free' => round(@disk_free_space(".") / (1024 * 1024 * 1024), 3),// 可用
            'percent' => 0,
        ];
        $hdUsage['used'] = $hdUsage['total'] - $hdUsage['free'];// 已用
        $hdUsage['percent'] = (floatval($hdUsage['total']) != 0) ? round($hdUsage['used'] / $hdUsage['total'] * 100, 2) : 0;// 使用率
        /* 硬盘使用状况 End */
        /* 内存使用状况 Begin */
        /* 物理内存 */
        $physicalMemory = [
            'total' => $sysInfo['memTotal'],// 总
            'used' => $sysInfo['memUsed'],// 已用
            'free' => $sysInfo['memFree'],// 可用
            'percent' => $sysInfo['memPercent'],// 使用率
        ];
        /* Cache化内存 */
        $cache = [
            'total' => $sysInfo['memCached'],
            'percent' => $sysInfo['memCachedPercent'],
            'buffers' => $sysInfo['memBuffers'],
        ];
        /* 真实内存 */
        $real = [
            'used' => $sysInfo['memRealUsed'],
            'free' => $sysInfo['memRealFree'],
            'percent' => $sysInfo['memRealPercent'],
        ];
        /* SWAP */
        $swap = [
            'total' => $sysInfo['swapTotal'],
            'used' => $sysInfo['swapUsed'],
            'free' => $sysInfo['swapFree'],
            'percent' => $sysInfo['swapPercent'],
        ];
        /* 内存使用状况 End */
        $return = [
            'serverTime' => date('Y-m-d H:i:s'),
            'serverHasRunTime' => $sysInfo['uptime'],
            'cpu' => $sysInfo['cpu'],
            'cpuUsage' => $total,
            'hdUsage' => $hdUsage,
            'memoryUsage' => [
                'physicalMemory' => $physicalMemory,
                'cache' => $sysInfo['memCached'] > 0 ? $cache : [],
                'real' => $sysInfo['memCached'] > 0 ? $real : [],
                'swap' => $sysInfo['swapTotal'] > 0 ? $swap : [],
            ],
            'loadAvg' => $sysInfo['loadAvg'],// 系统负载
        ];
        return $return;
    }

    /**
     * 获取网络使用状况
     */
    public static function getNetworkUsage()
    {
        $usage = [];
        $net_info_file = "/proc/net/dev";
        if (!file_exists($net_info_file)) {
            return $usage;
        }
        //网卡流量
        $strs = @file($net_info_file);
        for ($i = 2; $i < count($strs); $i++) {
            preg_match_all("/([^\s]+):[\s]{0,}(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/", $strs[$i], $info);
            $usage[$info[1][0]] = [
                'input' => self::formatsize($info[2][0]),
                'inputSpeed' => $info[2][0],
                'out' => self::formatsize($info[10][0]),
                'outSpeed' => $info[10][0],
            ];
            // $usage['NetInput'][$i] = self::formatsize($info[2][0]);
            // $usage['NetInputSpeed'][$i] = $info[2][0];
            // $usage['NetOut'][$i] = self::formatsize($info[10][0]);
            // $usage['NetOutSpeed'][$i] = $info[10][0];
        }
        return $usage;
    }

    /**
     * 根据不同系统取得CPU相关信息
     */
    public static function getSysInfo()
    {
        // error_reporting(0); //抑制所有错误信息
        switch (PHP_OS) {
            case "Linux":
                $sysInfo = self::sys_linux();
                break;
            case "FreeBSD":
                $sysInfo = self::sys_freebsd();
                break;
            case "WINNT":
                $sysInfo = false;
                break;
            default:
                break;
        }
        return $sysInfo;
    }

    /**
     * linux系统探测
     * @return bool
     */
    public static function sys_linux()
    {
        // CPU
        if (false === ($str = @file("/proc/cpuinfo"))) {
            return false;
        }
        $str = implode("", $str);
        @preg_match_all("/model\s+name\s{0,}\:+\s{0,}([\w\s\)\(\@.-]+)([\r\n]+)/s", $str, $model);
        @preg_match_all("/cpu\s+MHz\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $mhz);
        @preg_match_all("/cache\s+size\s{0,}\:+\s{0,}([\d\.]+\s{0,}[A-Z]+[\r\n]+)/", $str, $cache);
        @preg_match_all("/bogomips\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $bogomips);
        if (false !== is_array($model[1])) {
            // $res['cpu']['num'] = sizeof($model[1]);
            /*for ($i = 0; $i < $res['cpu']['num']; $i++) {
                $res['cpu']['model'][] = $model[1][$i] . '&nbsp;(' . $mhz[1][$i] . ')';
                $res['cpu']['mhz'][] = $mhz[1][$i];
                $res['cpu']['cache'][] = $cache[1][$i];
                $res['cpu']['bogomips'][] = $bogomips[1][$i];
            }*/
            /*if ($res['cpu']['num'] == 1) {
                $x1 = '';
            } else {
                $x1 = ' ×' . $res['cpu']['num'];
            }
            $mhz[1][0] = ' | 频率:' . $mhz[1][0];
            $cache[1][0] = ' | 二级缓存:' . $cache[1][0];
            $bogomips[1][0] = ' | Bogomips:' . $bogomips[1][0];
            $res['cpu']['model'][] = $model[1][0] . $mhz[1][0] . $cache[1][0] . $bogomips[1][0] . $x1;
            if (false !== is_array($res['cpu']['model'])) $res['cpu']['model'] = implode("<br />", $res['cpu']['model']);
            if (false !== is_array($res['cpu']['mhz'])) $res['cpu']['mhz'] = implode("<br />", $res['cpu']['mhz']);
            if (false !== is_array($res['cpu']['cache'])) $res['cpu']['cache'] = implode("<br />", $res['cpu']['cache']);
            if (false !== is_array($res['cpu']['bogomips'])) $res['cpu']['bogomips'] = implode("<br />", $res['cpu']['bogomips']);*/

            $res['cpu'] = [];// CPU列表
            for ($i = 0; $i < sizeof($model[1]); $i++) {
                $this_cpu = [// 当前遍历到的CPU信息
                    'model' => trim($model[1][$i]),// 型号
                    'num' => 1,// 数量
                    'mhz' => trim($mhz[1][$i]),// 主频
                    'cache' => trim($cache[1][$i]),// 缓存
                    'bogomips' => trim($bogomips[1][$i]),// mips
                ];
                if (!in_array($this_cpu, $res['cpu'])) {// 该CPU信息不存在CPU列表中
                    $res['cpu'][] = $this_cpu;// 添加该CPU信息到列表
                } else {
                    $res['cpu'][array_search($this_cpu, $res['cpu'])]['num'] += 1;// CPU核心数+1
                }
            }
        }
        // NETWORK
        // UPTIME
        if (false === ($str = @file("/proc/uptime"))) {
            return false;
        }
        $str = explode(" ", implode("", $str));
        $str = trim($str[0]);
        $min = $str / 60;
        $hours = $min / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));
        if ($days !== 0) {
            $res['uptime'] = $days . "天";
        }
        if ($hours !== 0) {
            $res['uptime'] .= $hours . "小时";
        }
        $res['uptime'] .= $min . "分钟";
        // MEMORY
        if (false === ($str = @file("/proc/meminfo"))) {
            return false;
        }
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
        if (false === ($str = @file("/proc/loadavg"))) {
            return false;
        }
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
        if (false === ($res['cpu']['num'] = self::get_key("hw.ncpu"))) {
            return false;
        }
        $res['cpu']['model'] = self::get_key("hw.model");
        //LOAD AVG
        if (false === ($res['loadAvg'] = self::get_key("vm.loadavg"))) {
            return false;
        }
        //UPTIME
        if (false === ($buf = self::get_key("kern.boottime"))) {
            return false;
        }
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
        if (false === ($buf = self::get_key("hw.physmem"))) {
            return false;
        }
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
        $path = ['/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin'];
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
     * @return string
     */
    public static function show($varName)
    {
        // $result = get_cfg_var($varName);
        // if ($result && !intval($result)) {
        //     return $result;
        // }
        switch ($result = get_cfg_var($varName)) {
            case 0:
                // return '<font color="red">×</font>';
                return false;
                break;
            case 1:
                // return '<font color="green">√</font>';
                return true;
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
        if (!$funName || trim($funName) == '' || preg_match('~[^a-z0-9\_]+~i', $funName, $tmp)) {
            return '错误';
        }
        // return (false !== function_exists($funName)) ? '<font color="green">√</font>' : '<font color="red">×</font>';
        return (function_exists($funName) !== false) ? true : false;
    }

    /**
     * 检测类支持
     * @param string $clsName
     * @return string
     */
    public static function iscls($clsName = '')
    {
        if (!$clsName || trim($clsName) == '' || preg_match('~[^a-z0-9\_]+~i', $clsName, $tmp)) {
            return '错误';
        }
        // return (false !== class_exists($clsName)) ? '<font color="green">√</font>' : '<font color="red">×</font>';
        return (class_exists($clsName) !== false) ? true : false;
    }

    /**
     * CPU核心详情
     * @return array
     */
    public static function GetCoreInformation()
    {
        $stat_file = '/proc/stat';
        $cores = [];
        if (file_exists($stat_file)) {
            $data = file($stat_file);
        } else {
            return $cores;
        }
        foreach ($data as $line) {
            if (preg_match('/^cpu[0-9]/', $line)) {
                $info = explode(' ', $line);
                $cores[] = ['user' => $info[1], 'nice' => $info[2], 'sys' => $info[3], 'idle' => $info[4], 'iowait' => $info[5], 'irq' => $info[6], 'softirq' => $info[7]];
            }
        }
        return $cores;
    }

    /**
     * CPU使用率详情
     * @param $stat1
     * @param $stat2
     * @return array|bool
     */
    public static function GetCpuPercentages($stat1, $stat2)
    {
        if (count($stat1) !== count($stat2)) {
            return false;
        }
        $cpus = [];
        for ($i = 0, $l = count($stat1); $i < $l; $i++) {
            $dif = [];
            $dif['user'] = $stat2[$i]['user'] - $stat1[$i]['user'];
            $dif['nice'] = $stat2[$i]['nice'] - $stat1[$i]['nice'];
            $dif['sys'] = $stat2[$i]['sys'] - $stat1[$i]['sys'];
            $dif['idle'] = $stat2[$i]['idle'] - $stat1[$i]['idle'];
            $dif['iowait'] = $stat2[$i]['iowait'] - $stat1[$i]['iowait'];
            $dif['irq'] = $stat2[$i]['irq'] - $stat1[$i]['irq'];
            $dif['softirq'] = $stat2[$i]['softirq'] - $stat1[$i]['softirq'];
            $total = array_sum($dif);
            $cpu = [];
            foreach ($dif as $x => $y) {
                $cpu[$x] = round($y / $total * 100, 2);
            }
            $cpus['cpu' . $i] = $cpu;
        }
        return $cpus;
    }

    /**
     * CPU使用率详情
     * @param $stat1
     * @param $stat2
     * @return array|void
     */
    public static function GetCpuPercentages1($stat1, $stat2)
    {
        if (count($stat1) !== count($stat2)) {
            return;
        }
        $cpus = [];
        for ($i = 0, $l = count($stat1); $i < $l; $i++) {
            $dif = [];
            $dif['User'] = $stat2[$i]['user'] - $stat1[$i]['user'];
            $dif['Nice'] = $stat2[$i]['nice'] - $stat1[$i]['nice'];
            $dif['Sys'] = $stat2[$i]['sys'] - $stat1[$i]['sys'];
            $dif['Idle'] = $stat2[$i]['idle'] - $stat1[$i]['idle'];
            $dif['Iowait'] = $stat2[$i]['iowait'] - $stat1[$i]['iowait'];
            $dif['Irq'] = $stat2[$i]['irq'] - $stat1[$i]['irq'];
            $dif['Softirq'] = $stat2[$i]['softirq'] - $stat1[$i]['softirq'];
            $total = array_sum($dif);
            $cpu = [];
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
        $danwei = [' B ', ' K ', ' M ', ' G ', ' T '];
        $allsize = [];
        $fsize = '';
        $i = 0;
        for ($i = 0; $i < 5; $i++) {
            if (floor($size / pow(1024, $i)) == 0) {
                break;
            }
        }
        for ($l = $i - 1; $l >= 0; $l--) {
            $allsize1[$l] = floor($size / pow(1024, $l));
            $allsize[$l] = $allsize1[$l] - ($allsize1[$l + 1] ?? 0) * 1024;
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
        $test_file = \Common\server_path('static/test');
        if (!file_exists($test_file)) {
            $w_file = fopen($test_file, "w") or die("Unable to open file!");
            for ($i = 0; $i <= 102400; $i++) {
                $txt = "0101010101";
                fwrite($w_file, $txt);
            }
            fclose($w_file);
        }
        $fp = @fopen($test_file, "r");
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

    /**
     * @return string
     */
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
        return "-> Test finished in $deltat seconds. Your speed is " . round($kb / $deltat, 3) . "Kb/s";
    }

    /**
     * @param $data
     * @return string
     */
    public static function testMySQLi($data)
    {
        if (function_exists("mysqli_close")) {
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

    /**
     * @param $data
     * @return string
     */
    public static function testFun($data)
    {
        if (!$data['funName'] || trim($data['funName']) == '' || preg_match('~[^a-z0-9\_]+~i', $data['funName'], $tmp)) {
            return T('函数名错误');
        }
        if (function_exists($data['funName'])) {
            return '服务器支持函数  ' . $data['funName'];
        } else {
            return '服务器不支持函数  ' . $data['funName'];
        }
    }

    /**
     * @param $data
     * @return string
     */
    public static function testMail($data)
    {
        $mailRe = '发送';
        if ($_SERVER['SERVER_PORT'] == 80) {
            $mailContent = "http://" . $_SERVER['SERVER_NAME'] . ($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);
        } else {
            $mailContent = "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . ($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);
        }
        $mailRe .= (false !== @mail($_POST["mailAdd"], $mailContent, "This is a test mail!")) ? '成功' : '失败';
        return $mailRe;
    }


}
