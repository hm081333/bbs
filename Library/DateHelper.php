<?php

namespace Library;

/**
 * 时间辅助类
 * Class DateHelper
 * @author LYi-Ho 2018-12-18 18:17:55
 */
class DateHelper
{


    /**
     * 获取某一天的时间间隔
     * @param int $num 0:表示当天的时间，1：表示明天的时间，-表示昨天的时间
     * @return array
     */
    public static function getDayTime($num = 0)
    {
        $now_day = strtotime(date('Y-m-d'));
        $begin_time = $now_day + $num * 86400;
        $end_time = $begin_time + 86400 - 1;

        return ['begin' => $begin_time, 'end' => $end_time];
    }

    /**
     * 获取某一月的时间间隔
     * @param int $num 0:表示当月的时间
     * @return array
     */
    public static function getMonthTime($num = 0)
    {
        $end_time = strtotime(date('Y-m-01') . ($num + 1 < 0 ? ' ' : ' + ') . ($num + 1) . ' month') - 1;
        $begin_time = strtotime(date('Y-m-01') . ($num < 0 ? ' ' : ' + ') . $num . ' month');
        return ['begin' => $begin_time, 'end' => $end_time];
    }

    /**
     * 指定相对某月前多少天或后多少天
     * @param int  $day_num   天偏移量
     * @param int  $month_num 月偏移量
     * @param bool $once_day  只输出指定的一天
     * @param bool $echo_date 打印日期
     * @return array
     */
    public static function getMonthDayTime($day_num = -1, $month_num = 0, $once_day = false, $echo_date = false)
    {
        $result = [];
        if ($day_num == 0) {// 本月时间
            $result['begin'] = strtotime(date('Y-m-01') . ($month_num < 0 ? ' ' : ' + ') . $month_num . ' month');
            $result['end'] = strtotime(date('Y-m-01') . ($month_num + 1 < 0 ? ' ' : ' + ') . ($month_num + 1) . ' month') - 1;
            if ($echo_date) {
                $result['begin_date'] = date('Y-m-d H:i:s', $result['begin']);
                $result['end_date'] = date('Y-m-d H:i:s', $result['end']);
            }
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
                }
            }
        }
        return $result;
    }

    /**
     * 获取某一周的时间间隔
     * @param int $num 0:表示当周的时间
     * @return array
     */
    public static function getWeekTime($num = 0)
    {
        $current_time = strtotime(date('Y-m-d'));
        $n = date('N', $current_time);//一周的星期几，星期一为1
        $return_arr['begin'] = $current_time - 86400 * ($n - 1) + ($num) * 7 * 86400;
        $return_arr['end'] = $return_arr['begin'] + 7 * 86400 - 1;
        return $return_arr;

    }

    /**
     * 获取本周时间
     * @return array
     */
    public static function getCurrentWeekDayTime()
    {
        $current_time = strtotime(date('Y-m-d'));
        $begin = $current_time - 86400 * (date('N', $current_time) - 1);
        for ($i = 0; $i < 7; $i++) {
            $week[] = $begin + 86400 * $i;
        }
        return $week;
    }

}
