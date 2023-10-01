<?php

namespace App\Utils\Juhe;

use App\Exceptions\Server\Exception;
use App\Exceptions\Server\InternalServerErrorException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * 万年历
 * @desc 接口文档：https://www.juhe.cn/docs/api/id/177
 * @package Juhe
 */
class Calendar
{
    /**
     * 获取当天的详细信息
     * @param string $date 指定日期,格式为YYYY-MM-DD,如月份和日期小于10,则取个位,如:2012-1-1
     * @return array
     * @throws InternalServerErrorException
     */
    public static function day(string $date): array
    {
        return Utils::instance()->request('http://v.juhe.cn/calendar/day', ['date' => $date]);
    }

    /**
     * 获取当年的假期列表
     * @param string $year 指定年份,格式为YYYY,如:2015
     * @return array
     * @throws InternalServerErrorException
     */
    public static function year(string $year): array
    {
        return Utils::instance()->request('http://v.juhe.cn/calendar/year', ['year' => $year]);
    }

    /**
     * 判断时间是否假期
     * @param $time
     * @return false
     * @throws Exception
     * @throws InternalServerErrorException
     * @throws InvalidArgumentException
     */
    public static function isHoliday($time): bool
    {
        $time = empty($time) ? false : ($time instanceof \Carbon\Carbon ? $time : (filter_var($time, FILTER_VALIDATE_INT) !== false ? (strlen((string)$time) === 10 ? Carbon::createFromTimestamp($time) : throw new Exception('时间戳格式错误')) : Carbon::parse($time)));
        if (!$time) return false;
        $holiday_year_month = $time->format('Y-n');
        $holiday_year_month_list = Cache::get("holiday:{$holiday_year_month}", null);
        if ($holiday_year_month_list === null) {
            $holiday_year_month_list = [];
            $year_month_holiday = static::month($holiday_year_month);
            $year_month_holiday_data = $year_month_holiday['data'];
            $holiday_array = $year_month_holiday_data['holiday_array'];
            foreach ($holiday_array as $holidays) {
                foreach ($holidays['list'] as $item) {
                    $holiday_year_month_list[Carbon::parse($item['date'])->timestamp] = ($item['status'] == 1);
                }
                unset($item);
            }
            unset($holidays);
            Cache::set("holiday:{$holiday_year_month}", $holiday_year_month_list);
        }
        return isset($holiday_year_month_list[$time->timestamp]) && $holiday_year_month_list[$time->timestamp];
    }

    /**
     * 获取近期假期
     * @param string $year_month 指定月份,格式为YYYY-MM,如月份和日期小于10,则取个位,如:2012-1
     * @return array
     * @throws InternalServerErrorException
     */
    public static function month(string $year_month): array
    {
        return Utils::instance()->request('http://v.juhe.cn/calendar/month', ['year-month' => $year_month]);
    }
}
