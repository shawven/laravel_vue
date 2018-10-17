<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-06-08 17:21
 */

namespace App\Http\Helpers\Statistic;

use Carbon\Carbon;

trait Statistical
{
    /**
     * @param Carbon $startDay
     * @param callable $callable
     * @return array
     */
    public function everyDayOfTheWeek(Carbon $startDay, callable $callable)
    {
        $data = [];

        for ($i = 0; $i <= 6; $i ++ ) {
            $currentDay = $startDay;

            $startOfDay = $currentDay ->startOfDay()->getTimestamp();
            $endOfDay = $currentDay->endOfDay()->getTimestamp();

            $data[$i] = $callable($startOfDay, $endOfDay);

            $currentDay->addDay();
        }

        $startDay->subWeek(1)->startOfWeek();

        return $data;
    }

    /**
     * @param Carbon $startDay
     * @param callable $callable
     * @return array
     */
    public function everyDayOfTheMonth(Carbon $startDay, callable $callable)
    {
        $data = [];

        for ($i = 0; $i <= 6; $i ++ ) {
            $currentDay = $startDay;

            $startOfDay = $currentDay ->startOfDay()->getTimestamp();
            $endOfDay = $currentDay->endOfDay()->getTimestamp();

            $data[$i] = $callable($startOfDay, $endOfDay);

            $currentDay->addDay();
        }

        $startDay->subWeek(1)->startOfWeek();

        return $data;
    }

    /**
     * @param $period
     * @param Carbon|null $reference
     * @return array
     */
    private function getPeriodsTimestamp($period, Carbon $reference = null)
    {
        $periods = $this->getPeriods($period, $reference);

        if ($periods) {
            return [$periods[0]->getTimestamp(), $periods[1]->getTimestamp()];
        }

        return null;
    }

    /**
     * @param $period
     * @param Carbon|null $reference
     * @return array
     */
    private function getPeriodsDate($period, Carbon $reference = null)
    {
        $periods = $this->getPeriods($period, $reference);

        if ($periods) {
            return [$periods[0]->toDateString(), $periods[1]->toDateString()];
        }

        return null;
    }

    /**
     * @param $period
     * @param Carbon|null $reference
     * @return array
     */
    private function getPeriodsDateTime($period, Carbon $reference = null)
    {
        $periods = $this->getPeriods($period, $reference);

        if ($periods) {
            return [$periods[0]->toDateTimeString(), $periods[1]->toDateTimeString()];
        }

        return null;
    }

    /**
     * 获取时间段的时间
     *
     * @param $period
     * @param Carbon $reference
     * @return array|null
     */
    public function getPeriods($period, Carbon $reference = null)
    {
        $startTime = $reference ? clone $reference : Carbon::now();
        $endTime = $reference ? clone $reference : Carbon::now();

        switch ($period) {
            case Period::TODAY:
                $times = [$startTime->startOfDay(), $endTime->endOfDay()];
                break;
            case Period::YESTERDAY:
                $times = [$startTime->subDay(1)->startOfDay(), $endTime->subDay(1)->endOfDay()];
                break;
            case Period::THIS_WEEK:
                $times = [$startTime->startOfWeek(), $endTime->endOfWeek()];
                break;
            case Period::THIS_MONTH:
                $times = [$startTime->startOfMonth(), $endTime->endOfMonth()];
                break;
            case Period::THIS_QUARTER:
                $times = [$startTime->startOfQuarter(), $endTime->endOfQuarter()];
                break;
            case Period::THIS_YEAR:
                $times = [$startTime->startOfYear(), $endTime->endOfYear()];
                break;
            case Period::LAST_WEEK:
                $times = [$startTime->subWeek(1)->startOfWeek(), $endTime->subWeek(1)->endOfWeek()];
                break;
            case Period::LAST_MONTH:
                $times = [$startTime->subMonth(1)->startOfMonth(), $endTime->subMonth(1)->endOfMonth()];
                break;
            case Period::LAST_QUARTER:
                $times = [$startTime->subQuarter(1)->startOfQuarter(), $endTime->subQuarter(1)->endOfQuarter()];
                break;
            case Period::LAST_YEAR:
                $times = [$startTime->subYear(1)->startOfYear(), $endTime->subYear(1)->endOfYear()];
                break;
            default:
                return null;
        }

        return $times;
    }
}
