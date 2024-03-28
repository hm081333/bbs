<?php

namespace App\Utils\Juhe;

use App\Exceptions\Server\BaseServerException;
use App\Exceptions\Server\InternalServerErrorException;
use App\Utils\Tools;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * 万年历
 *
 * @desc    接口文档：https://www.juhe.cn/docs/api/id/177
 * @package Juhe
 */
class Calendar
{
    //region 接口调用
    /**
     * 获取当天的详细信息
     *
     * @param string $date 指定日期,格式为YYYY-MM-DD,如月份和日期小于10,则取个位,如:2012-1-1
     *
     * @return array
     * @throws InternalServerErrorException
     */
    public static function day(string $date): array
    {
        $date = Tools::timeToCarbon($date);
        return Utils::instance()->request('http://v.juhe.cn/calendar/day', ['date' => $date->format('Y-n-j')]);
    }

    /**
     * 获取近期假期
     *
     * @param string $year_month 指定月份,格式为YYYY-MM,如月份和日期小于10,则取个位,如:2012-1
     *
     * @return array
     * @throws InternalServerErrorException
     */
    public static function month(string $year_month): array
    {
        $year_month = Tools::timeToCarbon($year_month);
        return Utils::instance()->request('http://v.juhe.cn/calendar/month', ['year-month' => $year_month->format('Y-n')]);
    }

    /**
     * 获取当年的假期列表
     *
     * @param string|Carbon $year 指定年份,格式为YYYY,如:2015
     *
     * @return array
     * @throws InternalServerErrorException
     */
    public static function year(string|Carbon $year): array
    {
        return Utils::instance()->request('http://v.juhe.cn/calendar/year', ['year' => $year]);
    }
    //endregion

    /**
     * 根据年月获取最近假期日期列表
     *
     * @param string $year_month 指定月份,格式为YYYY-MM,如月份和日期小于10,则取个位,如:2012-1
     *
     * @return array
     * @throws InternalServerErrorException
     * @throws InvalidArgumentException
     */
    public static function getYearMonthHolidayList(string $year_month): array
    {
        return Cache::remember("holiday:{$year_month}", Tools::timeToCarbon($year_month)->startOfMonth()->addMonth(), function () use ($year_month) {
            $holiday_year_month_list = [];
            $year_month_holiday = static::month($year_month);
            $year_month_holiday_data = $year_month_holiday['data'];
            $holiday_array = $year_month_holiday_data['holiday_array'];
            foreach ($holiday_array as $holidays) {
                foreach ($holidays['list'] as $item) {
                    $holiday_year_month_list[Tools::timeToCarbon($item['date'])->timestamp] = ($item['status'] == 1);
                }
                unset($item);
            }
            unset($holidays);
            return $holiday_year_month_list;
        });
    }

    /**
     * 判断时间是否假期
     *
     * @param Carbon|int|float|string $time 待判断的时间
     *
     * @return false
     * @throws InternalServerErrorException
     * @throws InvalidArgumentException
     */
    public static function isHoliday(Carbon|int|float|string $time): bool
    {
        $time = Tools::timeToCarbon($time);
        if (!$time) return false;
        $holiday_year_month = $time->format('Y-n');
        $holiday_year_month_list = static::getYearMonthHolidayList($holiday_year_month);
        return isset($holiday_year_month_list[$time->timestamp]) && $holiday_year_month_list[$time->timestamp];
    }

    /**
     * 判断时间是否休息日
     *
     * @param Carbon|int|float|string $time 待判断的时间
     *
     * @return bool
     * @throws InternalServerErrorException
     * @throws InvalidArgumentException
     */
    public static function isRestDay(Carbon|int|float|string $time): bool
    {
        $time = Tools::timeToCarbon($time);
        if (!$time) return false;
        $holiday_year_month = $time->format('Y-n');
        $holiday_year_month_list = static::getYearMonthHolidayList($holiday_year_month);
        return isset($holiday_year_month_list[$time->timestamp]) ? $holiday_year_month_list[$time->timestamp] : $time->isWeekend();
    }
}
