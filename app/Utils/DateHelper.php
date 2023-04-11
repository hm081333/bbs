<?php

namespace App\Utils;

use App\Exceptions\Request\BadRequestException;
use Carbon\Carbon;

/**
 * 时间辅助类
 * Class DateHelper
 */
class DateHelper
{
    private $timeFormat = false;

    /**
     * 设置返回时间格式
     * @param $format
     * @return DateHelper
     */
    public function setTimeFormat($format = 'Y-m-d H:i:s')
    {
        $this->timeFormat = $format;
        return $this;
    }

    /**
     * 格式化时间
     * @param $time
     * @return false|string
     */
    private function parseTime($time)
    {
        return $this->timeFormat ? date($this->timeFormat, $time) : $time;
    }

    /**
     * 获取某一天的时间间隔
     * @param int $num 0:表示当天的时间，1：表示明天的时间，-表示昨天的时间
     * @return array
     */
    public function getDayTime($num = 0)
    {
        $now_day = strtotime(date('Y-m-d'));
        $begin_time = $now_day + $num * 86400;
        $end_time = $begin_time + 86400 - 1;

        return ['begin' => $this->parseTime($begin_time), 'end' => $this->parseTime($end_time)];
    }

    /**
     * 获取某一周的时间间隔
     * @param int $num 0:表示当周的时间
     * @return array
     */
    public function getWeekTime($num = 0)
    {
        $current_time = strtotime(date('Y-m-d'));
        $n = date('N', $current_time);//一周的星期几，星期一为1
        $begin_time = $current_time - 86400 * ($n - 1) + ($num) * 7 * 86400;
        $end_time = $begin_time + 7 * 86400 - 1;
        return ['begin' => $this->parseTime($begin_time), 'end' => $this->parseTime($end_time)];

    }

    /**
     * 获取某一月的时间间隔
     * @param int $num 0:表示当月的时间
     * @return array
     */
    public function getMonthTime($num = 0)
    {
        $end_time = strtotime(date('Y-m-01') . ($num + 1 < 0 ? ' ' : ' + ') . ($num + 1) . ' month') - 1;
        $begin_time = strtotime(date('Y-m-01') . ($num < 0 ? ' ' : ' + ') . $num . ' month');
        return ['begin' => $this->parseTime($begin_time), 'end' => $this->parseTime($end_time)];
    }

    /**
     * 指定相对某月前多少天或后多少天
     * @param int  $day_num   天偏移量
     * @param int  $month_num 月偏移量
     * @param bool $once_day  只输出指定的一天
     * @param bool $echo_date 打印日期
     * @return array
     */
    public function getMonthDayTime($day_num = -1, $month_num = 0, $once_day = false, $echo_date = false)
    {
        $result = [];
        if ($day_num == 0) {// 本月时间
            $result['begin'] = strtotime(date('Y-m-01') . ($month_num < 0 ? ' ' : ' + ') . $month_num . ' month');
            $result['end'] = strtotime(date('Y-m-01') . ($month_num + 1 < 0 ? ' ' : ' + ') . ($month_num + 1) . ' month') - 1;
            if ($echo_date) {
                $result['begin_date'] = date('Y-m-d H:i:s', $result['begin']);
                $result['end_date'] = date('Y-m-d H:i:s', $result['end']);
            }
            $result['begin'] = $this->parseTime($result['begin']);
            $result['end'] = $this->parseTime($result['end']);
        } else {// 倒数几天、顺数几天
            $month_time_str = ($day_num < 0 ? 'last ' : 'first ') . 'day of ' . ($month_num < 0 ? '' : '+') . $month_num . ' month';// 指定月的第一天或最后一天 表达式
            $month_date = date('Y-m-d', strtotime($month_time_str));// 时间短语
            if ($once_day) {
                $day_time_str = $month_date . ($day_num < 0 ? ' -' : ' +') . (abs($day_num) - 1) . ' day';
                $result['begin'] = strtotime($day_time_str);
                $result['end'] = $result['begin'] + 86400 - 1;
                if ($echo_date) {
                    $result['begin_date'] = date('Y-m-d H:i:s', $result['begin']);
                    $result['end_date'] = date('Y-m-d H:i:s', $result['end']);
                }
                $result['begin'] = $this->parseTime($result['begin']);
                $result['end'] = $this->parseTime($result['end']);
            } else {
                for (
                    $i = 0;
                    $i < abs($day_num);// 绝对值
                    $i++
                ) {
                    $day_time_str = $month_date . ($day_num < 0 ? ' -' : ' +') . $i . ' day';
                    $result[$i]['begin'] = strtotime($day_time_str);
                    $result[$i]['end'] = $result[$i]['begin'] + 86400 - 1;// 这个日期的结束时间
                    if ($echo_date) {
                        $result[$i]['begin_date'] = date('Y-m-d H:i:s', $result[$i]['begin']);
                        $result[$i]['end_date'] = date('Y-m-d H:i:s', $result[$i]['end']);
                    }
                    $result[$i]['begin'] = $this->parseTime($result[$i]['begin']);
                    $result[$i]['end'] = $this->parseTime($result[$i]['end']);
                }
            }
        }
        return $result;
    }

    /**
     * 获取某一年的时间间隔
     * @param int $num 0:表示当周的时间
     * @return array
     */
    public function getYearTime($num = 0)
    {
        $end_time = strtotime(date('Y-01-01') . ($num + 1 < 0 ? ' ' : ' + ') . ($num + 1) . ' year') - 1;
        $begin_time = strtotime(date('Y-01-01') . ($num < 0 ? ' ' : ' + ') . $num . ' year');
        return ['begin' => $this->parseTime($begin_time), 'end' => $this->parseTime($end_time)];

    }

    /**
     * 获取本周时间
     * @return array
     */
    public function getCurrentWeekDayTime()
    {
        $current_time = strtotime(date('Y-m-d'));
        $begin = $current_time - 86400 * (date('N', $current_time) - 1);
        $weeks = [];
        for ($i = 0; $i < 7; $i++) {
            $week = $begin + 86400 * $i;
            $weeks[] = $this->parseTime($week);
        }
        return $weeks;
    }

    /**
     * 获取时间维度数组（时间间隔）
     * @param string            $time_dimension
     * @param int|string|Carbon $start_time
     * @param int|string|Carbon $end_time
     * @return array
     * @throws BadRequestException
     */
    public function getTimeDimension(string $time_dimension, $begin_time, $end_time)
    {
        //region 传入参数 转换时间戳
        $begin_time = $this->toTimestamp($begin_time);
        $end_time = $this->toTimestamp($end_time);
        //endregion
        $time_dimensions = [];
        switch ($time_dimension) {
            case 'day':
                $format = 'Y-m-d';
                $date_time_extend = '+1 day';
                break;
            case 'week':
                $format = 'Y-W';
                $date_time_extend = '+1 week';
                break;
            case 'month':
                $format = 'Y-m';
                $date_time_extend = '+1 month';
                break;
            case 'quarter':
                $format = 'Y';
                $date_time_extend = '+1 month';
                break;
            case 'year':
                $format = 'Y';
                $date_time_extend = '+1 year';
                break;
            default:
                throw new BadRequestException('异常请求');
        }
        $this->setTimeFormat($format);
        while ($begin_time <= $end_time) {
            $format_time = $this->parseTime($begin_time) . ($time_dimension == 'quarter' ? '-' . ceil(date('n', $begin_time) / 3) : '');
            $begin_time = strtotime(date('Y-m-d H:i:s', $begin_time) . ' ' . $date_time_extend);
            if (in_array($format_time, $time_dimensions)) continue;
            $time_dimensions[] = $format_time;
        }
        return $time_dimensions;
    }

    /**
     * 获取字符串对应的时间戳
     * @param int|string|Carbon $str
     * @return int
     */
    public function toTimestamp($data)
    {
        if (is_numeric($data)) {
            // 纯数字-时间戳
            return (int)substr($data, 0, 10);
        } else if ($data instanceof Carbon) {
            return $data->timestamp;
        } else {
            // 非纯数字-日期时间
            return strtotime($data);
        }
    }

}
