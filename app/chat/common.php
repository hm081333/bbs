<?php
// 这是系统自动生成的公共文件
use Library\DateHelper;

/**
 * @param int $time 时间戳
 */
function sortTime($time){
    // 最后消息时间
    if (date('Ymd', $time) == date('Ymd', time())) {
        // 当天
        $sort_time = date('A h:i', $time);
        $pat = ['AM', 'PM'];
        $string = ['上午', '下午'];
        $sort_time = str_replace($pat, $string, $sort_time);
    } else if ($time >= strtotime(date('Y-m-d') . ' -1 day')) {
        // 昨天
        $sort_time = '昨天';
    } else if ($time >= strtotime(date('Y-m-d') . ' -6 day')) {
        // 近一周
        $sort_time = DateHelper::getWeekName($time);
    } else {
        // 更早
        $sort_time = date('Y/n/j', $time);
    }
    return $sort_time;
}