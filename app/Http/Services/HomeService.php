<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-05-18 15:35
 */

namespace App\Http\Services;

use App\Http\Models\User\User;
use App\Http\Models\User\UserOrder;
use App\Http\Helpers\Statistic\Period;
use App\Http\Helpers\Statistic\Statistical;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class HomeService
{
    use Statistical;

    const SUM = 'sum';
    const COUNT = 'count';

    /**
     * @param $times
     * @return array
     */
    public function countOrders($times = [])
    {
        $order = new UserOrder();
        $wheres = ['type' => [UserOrder::TYPE_BASKETBALL, UserOrder::TYPE_FOOTBALL]];

        $hasFilterTimes = count(array_filter($times));
        if ($hasFilterTimes) {
            return $this->obtainStatisticalDataByPeriod($order, $wheres, $times);
        }

        $all = $this->obtainStatisticalDataByPeriod($order, $wheres);
        $today = $this->obtainStatisticalDataByPeriod($order, $wheres, Period::TODAY);
        $yesterday = $this->obtainStatisticalDataByPeriod($order, $wheres, Period::YESTERDAY);

        $thisWeek = $this->obtainStatisticalDataByWeekPeriod($order, $wheres, Period::THIS_WEEK,
            Carbon::now()->startOfWeek());
        $thisMonth = $this->obtainStatisticalDataByPeriod($order, $wheres, Period::THIS_MONTH);
        $thisQuarter = $this->obtainStatisticalDataByPeriod($order, $wheres, Period::THIS_QUARTER);
        $thisYear = $this->obtainStatisticalDataByPeriod($order, $wheres, Period::THIS_YEAR);

        $lastWeek =$this->obtainStatisticalDataByWeekPeriod($order, $wheres, Period::LAST_WEEK,
            Carbon::now()->subWeek(1)->startOfWeek());
        $lastMonth =$this->obtainStatisticalDataByPeriod($order, $wheres, Period::LAST_MONTH);
        $lastQuarter =$this->obtainStatisticalDataByPeriod($order, $wheres, Period::LAST_QUARTER);
        $lastYear =$this->obtainStatisticalDataByPeriod($order, $wheres, Period::LAST_YEAR);

        return [
            'all' => $all,
            'today' => $today,
            'yesterday' => $yesterday,
            'thisWeek' => $thisWeek,
            'thisMonth' => $thisMonth,
            'thisQuarter' => $thisQuarter,
            'thisYear' => $thisYear,
            'lastWeek' => $lastWeek,
            'lastMonth' => $lastMonth,
            'lastQuarter' => $lastQuarter,
            'lastYear' => $lastYear,
        ];
    }

    /**
     * @return array
     */
    public function countUsers()
    {
        $user = new User();

        $all = $this->count($user);
        $today = $this->count($user, [], ['addtime', $this->getPeriodsTimestamp(Period::TODAY)]);
        $yesterday = $this->count($user, [], ['addtime', $this->getPeriodsTimestamp(Period::YESTERDAY)]);
        $thisWeek = $this->count($user, [], ['addtime', $this->getPeriodsTimestamp(Period::THIS_WEEK)]);
        $thisMonth = $this->count($user, [], ['addtime', $this->getPeriodsTimestamp(Period::THIS_MONTH)]);
        $thisQuarter = $this->count($user, [], ['addtime', $this->getPeriodsTimestamp(Period::THIS_QUARTER)]);
        $thisYear = $this->count($user, [], ['addtime', $this->getPeriodsTimestamp(Period::THIS_YEAR)]);
        $lastWeek =  $this->count($user, [], ['addtime', $this->getPeriodsTimestamp(Period::LAST_WEEK)]);
        $lastMonth =  $this->count($user, [], ['addtime', $this->getPeriodsTimestamp(Period::LAST_MONTH)]);
        $lastQuarter =  $this->count($user, [], ['addtime', $this->getPeriodsTimestamp(Period::LAST_QUARTER)]);
        $lastYear =  $this->count($user, [], ['addtime', $this->getPeriodsTimestamp(Period::LAST_YEAR)]);

        return [
            'all' => $all,
            'today' => $today,
            'yesterday' => $yesterday,
            'thisWeek' => $thisWeek,
            'thisMonth' => $thisMonth,
            'thisQuarter' => $thisQuarter,
            'thisYear' => $thisYear,
            'lastWeek' => $lastWeek,
            'lastMonth' => $lastMonth,
            'lastQuarter' => $lastQuarter,
            'lastYear' => $lastYear,
        ];
    }


    /**
     * 获取某个时间段的统计数据
     *
     * @param Model $model
     * @param array $wheres
     * @param null $period
     * @return array
     */
    public function obtainStatisticalDataByPeriod(Model $model, array $wheres = [], $period = null)
    {
        $periodWhere = ['addtime', is_array($period) ? $period :$this->getPeriodsTimestamp($period)];

        $total = $this->count($model, [], $periodWhere);
        $totalMoney = $this->sum($model, [], $periodWhere, 'total_money');

        $category = [];
        foreach($wheres as $key => $values) {
            foreach ($values as $value) {
                $condition = [$key, $value];
                $category['orders'][$value] =  $this->count($model, $condition, $periodWhere);
                $category['totalMoney'][$value] = $this->sum($model, $condition, $periodWhere, 'total_money');
            }

        }

        return [
            'orders' => $total,
            'totalMoney' => $totalMoney,
            'category' => $category
        ];
    }

    /**
     * 获取一星期每一天的统计数据
     *
     * @param Model $model
     * @param array $wheres
     * @param array|string $period
     * @param Carbon $startDay
     * @return array
     */
    public function obtainStatisticalDataByWeekPeriod(Model $model, array $wheres, $period, Carbon $startDay)
    {
        $periodWhere = ['addtime', is_array($period) ? $period :$this->getPeriodsTimestamp($period)];

        $total = $this->count($model, [], $periodWhere);
        $totalMoney = $this->sum($model, [], $periodWhere, 'total_money');

        $category = [];

        foreach($wheres as $key => $values) {
            foreach ($values as $value) {
                $condition = [$key, $value];
                $category['orders'][$value] =  $this->count($model, $condition, $periodWhere);
                $category['totalMoney'][$value] = $this->sum($model, $condition, $periodWhere, 'total_money');
                $category['orders']['week'][$value] =  $this->countEveryDayOfTheWeek($model, $condition, $startDay);
                $category['totalMoney']['week'][$value] = $this->sumEveryDayOfTheWeek($model, $condition, $startDay, 'total_money');
            }

        }

        return [
            'orders' => $total,
            'totalMoney' => $totalMoney,
            'category' => $category
        ];
    }

    /**
     * 获取时间段数据总数
     *
     * @param Model $model
     * @param array $where
     * @param array $times
     * @param string $column
     * @return int
     */
    public function count(Model $model, array $where = [], array $times = [], $column= '*')
    {
        return $this->calculate(static::COUNT, $model, $where, $times, $column);
    }

    /**
     * 对某个时间段的数据求和
     *
     * @param Model $model
     * @param array $where
     * @param array $times
     * @param string $column
     * @return mixed
     */
    public function sum(Model $model, array $where = [], array $times=[], $column)
    {
        return $this->calculate(static::SUM, $model, $where,$times, $column);
    }


    /**
     * 统计一星期每一天的总记录数
     *
     * @param Model $model
     * @param array $where
     * @param Carbon $startDay
     * @param string $column
     * @return array
     */
    public function countEveryDayOfTheWeek(Model $model, array $where = [], Carbon $startDay, $column = '*')
    {
        return $this->obtainEveryDayDataOfTheWeek(static::COUNT, $model, $where, $startDay, $column);
    }

    /**
     * 对一星期每一天的数据求和
     *
     * @param Model $model
     * @param array $where
     * @param Carbon $startDay
     * @param $column
     * @return array
     */
    public function sumEveryDayOfTheWeek(Model $model, array $where = [], Carbon $startDay, $column)
    {
        return $this->obtainEveryDayDataOfTheWeek(static::SUM, $model, $where, $startDay, $column);
    }


    /**
     * 获取一星期每一天的数据
     *
     * @param string $action
     * @param Model $model
     * @param array $where
     * @param Carbon $startDay
     * @param $column
     * @return array
     */
    public function obtainEveryDayDataOfTheWeek($action, Model $model, array $where = [], Carbon $startDay, $column)
    {
        return $this->everyDayOfTheWeek($startDay,
            function($startOfDay, $endOfDay) use ($action, $where, $model, $column) {
                return $action == static::COUNT
                    ? $this->count($model, $where, ['addtime', [$startOfDay, $endOfDay]], $column)
                    : $this->sum($model, $where, ['addtime', [$startOfDay, $endOfDay]], $column);
            });
    }

    /**
     * 生成时间段的数据
     *
     * @param $action
     * @param Model $model
     * @param array $where
     * @param array $times
     * @param $column
     * @return int
     */
    public function calculate($action, Model $model, array $where = [], array $times=[], $column)
    {
        $builder = $model::query();

        if (count($where) == 2) {
            [$field, $value] = $where;
            $builder->where($field, $value);
        }

        if ($times && count($times) >= 2) {
            [$field, [$start, $end]] = $times;

            if ($start && $end) {
                $now = Carbon::now()->getTimestamp();

                if ($start > $now) {
                    return 0;
                }

                $end = $end > $now ? $now : $end;
                $builder->whereBetween($field, [$start, $end]);
            }
        }

        return $action == static::COUNT ? $builder->count($column) : floatval($builder->sum($column));
    }
}
