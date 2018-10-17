<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-06-08 18:26
 */

namespace App\Http\Services\User;

use App\Http\Helpers\Statistic\Period;
use App\Http\Helpers\Statistic\Statistical;
use App\Http\Models\Activity\Activity;
use App\Http\Models\Activity\ActivityRecord;
use App\Http\Models\User\UserMoneyRecord;
use App\Http\Models\User\UserMoneyRecordCount;
use App\Http\Models\User\UserOrder;
use App\Http\Models\User\UserWithdraw;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class UserMoneyRecordService
{
    use Statistical;

    private $payTypes = [
        UserMoneyRecord::TYPE_BONUS,
        UserMoneyRecord::TYPE_DRAW,
        UserMoneyRecord::TYPE_BUY,
        UserMoneyRecord::TYPE_RECHARGE,
    ];

    private $payModes =  [
        UserMoneyRecord::MODE_ALIPAY,
        UserMoneyRecord::MODE_WXPAY,
        UserMoneyRecord::MODE_WALLET,
        UserMoneyRecord::MODE_HANDSEL
    ];

    /**
     * @param Request $request
     * @return array
     */
    public function countMoneyRecords(Request $request)
    {
        $todayData = $this->obtainMoneyRecords(Period::TODAY);

        $historyItems = $this->getUserMoneyRecordCountList($request->startTime, $request->endTime);
        $historyData  = $this->convertHistoryData($historyItems);

        return [
            'today' => $todayData,
            'history' => $historyData
        ];
    }

    /**
     * @param null $period
     * @return array
     */
    public function obtainMoneyRecords($period = null)
    {
        $data = [];

        foreach($this->payTypes as $typeValue) {
            $temp = [];
            $filters = [];

            switch ($typeValue) {
                // 购彩统计
                case UserMoneyRecord::TYPE_BUY:
                    foreach (['total_money', 'handsel', 'draw_money'] as $type) {
                        $temp[$type]['total'] = $this->sumMoneyByUserOrders([], $period, $type);

                        foreach ($this->payModes as $modeValue) {
                            $filters['payway'] = $modeValue;
                            $temp[$type]['parts'][$modeValue] = $this->sumMoneyByUserOrders($filters, $period, $type);
                        }
                    }
                    $temp = $this->rebuildData($temp);
                    break;
                // 充值统计
                case UserMoneyRecord::TYPE_RECHARGE:
                    $filters['typename'] = UserMoneyRecord::USER_RECHARGE;
                    $temp['total'] = $this->sumMoneyByUserMoneyRecords($filters, $period, 'money');

                    foreach ($this->payModes as $modeValue) {
                        // 排除钱包充值
                        if ($modeValue == UserMoneyRecord::MODE_WALLET) continue;

                        $filters['mode'] = $modeValue;

                        if ($modeValue == UserMoneyRecord::MODE_HANDSEL) {
                            $temp['parts'][$modeValue] = $this->sumCaijinByActivityRecords($period);
                            $temp['total'] = sprintf('%.2f', $temp['total'] + $temp['parts'][$modeValue]);
                        } else {
                            $temp['parts'][$modeValue] = $this->sumMoneyByUserMoneyRecords($filters, $period, 'money');
                        }
                    }
                    break;
                // 提现统计
                case UserMoneyRecord::TYPE_DRAW:
                    $temp['total'] = $this->sumMoneyWithdraws($filters, $period);
                    break;
                // 返奖统计
                case UserMoneyRecord::TYPE_BONUS:
                    $filters['type'] = $typeValue;
                    $temp['total'] = $this->sumMoneyByUserMoneyRecords($filters, $period, 'money');
                    break;
            }

            $data[$typeValue] = $temp;
        }

        return $data;
    }

    /**
     * @param $startTime
     * @param $endTime
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]
     *  |\Illuminate\Support\Collection
     */
    public function getUserMoneyRecordCountList($startTime, $endTime)
    {
        if ($startTime) {
            $startTime = Carbon::createFromTimestamp(strtotime($startTime))->toDateString();
        }
        if ($endTime) {
            $endTime = Carbon::createFromTimestamp(strtotime($endTime))->toDateString();
        }

        $query = UserMoneyRecordCount::query();

        if ($startTime) {
            if ($endTime) {
                $query->whereBetween('date', [$startTime, $endTime]);
            } else {
                $query->where('date', ">=", $startTime);
            }
        } else if ($endTime) {
            $query->where('date', "<=", $endTime);
        } else {
            $query->whereBetween('date', $this->getPeriodsDate(Period::YESTERDAY));
        }

        return $query->orderByDesc('id')->get();
    }

    /**
     * @param Collection $items
     * @return mixed
     */
    public function convertHistoryData(Collection $items)
    {
        $initial = $items->pop();

        $data = $items->reduce(function($carry, $item) {
            $this->addToEachOther($carry, $item->count_data);
            return $carry;
        }, $initial->count_data);

        return $data;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function rebuildData($data)
    {
        $buyData = $data;
        $parts = [];

        array_walk($data['total_money']['parts'], function($value, $key) use ($buyData, &$parts) {
            $parts[$key]['total'] = $value;
            $parts[$key]['parts']['handsel'] = $buyData['handsel']['parts'][$key];
            $parts[$key]['parts']['draw_money'] = $buyData['draw_money']['parts'][$key];
        });

        $newBuyData['total'] = $buyData['total_money']['total'];
        $newBuyData['parts'] = $parts;

        $data = $newBuyData;

        return $data;
    }

    /**
     * 遍历相加
     *
     * @param $carry
     * @param $subject
     */
    public function addToEachOther(&$carry, $subject)
    {
        foreach ($carry as $key => &$value) {
            if (!isset($subject[$key])) {
                continue;
            }
            if (is_array($value)) {
                $this->addToEachOther($value, $subject[$key]);
            } else {
                $value += $subject[$key];
            }
        }
    }


    /**
     * @param Request $request
     * @return bool
     */
    public function hasFilters(Request $request)
    {
        return isset($request->startTime) || isset($request->endTime);
    }

    /**
     * 对某个时间段的数据求和
     *
     * @param array $filters
     * @param $period
     * @param string $column
     * @return mixed
     */
    public function sumMoneyByUserMoneyRecords(array $filters, $period, $column)
    {
        $query = UserMoneyRecord::query();
        $this->addWheres($query, $filters);
        $this->addWhereTime($query, 'addtime', is_array($period) ? $period : $this->getPeriodsTimestamp($period));

        return sprintf('%.2f',($query->sum($column)));
    }

    /**
     * 下单成功的订单金额求和
     *
     * @param array $filters
     * @param $period
     * @param string $column
     * @return mixed
     */
    public function sumMoneyByUserOrders(array $filters, $period, $column)
    {
        $query = UserOrder::query();
        $query->where('state', '!=', UserOrder::OPENING_LOTTERY);
        $query->where('to_draw', '=', UserOrder::TICKET_SUCCESS);

        $this->addWheres($query, $filters);
        $this->addWhereTime($query, 'updatetime', is_array($period) ? $period : $this->getPeriodsTimestamp($period));

        return sprintf('%.2f', $query->sum($column));
    }

    /**
     * 提现求和
     *
     * @param array $filters
     * @param $period
     * @return float|int
     */
    public function sumMoneyWithdraws(array $filters, $period)
    {
        $filters['state'] = UserWithdraw::DRAW_SUCCESS;

        $query = UserWithdraw::query();
        $query->where('state', UserWithdraw::DRAW_SUCCESS);

        $this->addWheres($query, $filters);
        $this->addWhereTime($query, 'updatetime', is_array($period) ? $period : $this->getPeriodsTimestamp($period));

        return sprintf('%.2f',$query->sum('money'));
    }

    /**
     * @param $period
     * @return int|string
     */
    public function sumCaijinByActivityRecords($period)
    {
        $query = ActivityRecord::query();
        $activityRecordTable = $query->getModel()->getTable();
        $activityTable = Activity::query()->getModel()->getTable();

        $query->leftjoin($activityTable, "$activityTable.id", '=', "$activityRecordTable.activity_id");
        $this->addWhereTime($query, 'create_time', is_array($period) ? $period : $this->getPeriodsDateTime($period));

        return sprintf('%.2f',$query->sum("$activityTable.amount"));
    }


    /**
     * @param Builder $query
     * @param array $wheres
     */
    public function addWheres(Builder $query, array $wheres)
    {
        $tableName = $query->getModel()->getTable();

        $conditions = [];

        foreach($wheres as $key =>$value) {
            $conditions["$tableName.$key"] = $value;
        }

        $query->where($conditions);
    }

    /**
     * @param Builder $query
     * @param $field
     * @param array $times
     */
    public function addWhereTime(Builder $query, $field, array $times)
    {
        [$start, $end] = $times;

        $now = Carbon::now()->getTimestamp();

        $end = $end > $now ? $now : $end;

        $tableName = $query->getModel()->getTable();

        $query->whereBetween("$tableName.$field", [$start, $end]);
    }
}
