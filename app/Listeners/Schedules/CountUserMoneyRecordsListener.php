<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-06-17 14:25
 */

namespace App\Listeners\Schedules;

use App\Http\Helpers\Statistic\Period;
use App\Http\Helpers\Statistic\Statistical;
use App\Http\Models\User\UserMoneyRecord;
use App\Http\Models\User\UserMoneyRecordCount;
use App\Http\Models\UserBalanceRecord;
use App\Http\Services\User\UserMoneyRecordService;

class CountUserMoneyRecordsListener
{
    use Statistical;

    /**
     * @return UserBalanceRecord|\Illuminate\Database\Eloquent\Model
     */
    public function count()
    {
        $yesterdayMoneyRecord = $this->countMoneyRecords();

        $yesterdayMoneyData = $this->obtainMoneyData($yesterdayMoneyRecord);

        $oldUserBalanceRecord = $this->getYesterdayLastUserBalanceRecord();

        $newUserBalanceRecord = $this->saveUserBalanceRecord($oldUserBalanceRecord, $yesterdayMoneyData);

        $this->saveUserMoneyRecordCount($yesterdayMoneyRecord);

        return $newUserBalanceRecord;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     * |\Illuminate\Database\Query\Builder|null|object
     */
    public function getYesterdayLastUserBalanceRecord()
    {
        return UserBalanceRecord::query()
            ->whereBetween('create_time', $this->getPeriods(Period::YESTERDAY))
            ->latest('id')
            ->first();
    }

    public function saveUserBalanceRecord($oldRecord, $moneyData)
    {
        $record = $oldRecord;
        $yesterday = now()->subDay(1)->toDateString();
        [$rechargeData, $buyData, $bonusData, $drawData] = $moneyData;

        $rechargeTotal = $rechargeData['total'];
        if (floatval($rechargeTotal)) {
            $rechargeHandsel = $rechargeData['parts'][UserMoneyRecord::MODE_HANDSEL];
            $rechargeBalance = $rechargeTotal - $rechargeHandsel;
            $data = [
                'type' => UserBalanceRecord::TYPE_RECHARGE,
                'desc' => '充值',
                'initial_total' => $record['final_total'],
                'change_total' => $rechargeTotal,
                'final_total' => $record['final_total'] + $rechargeTotal,
                'initial_balance' => $record['final_balance'],
                'change_balance' => $rechargeBalance,
                'final_balance' => $record['final_balance'] + $rechargeBalance,

                'initial_handsel' => $record['final_handsel'],
                'change_handsel' => $rechargeHandsel,
                'final_handsel' => $record['final_handsel'] + $rechargeHandsel,
                'initial_draw_balance' => $record['final_draw_balance'],
                'change_draw_balance' => '0.00',
                'final_draw_balance' => $record['final_draw_balance'],
                'date' => $yesterday
            ];
            $record = UserBalanceRecord::create($data)->refresh();
        }

        if (floatval($buyData)) {
            $walletTotal = $buyData['parts'][UserMoneyRecord::MODE_WALLET]['total'];
            $drawBalanceTotal = $buyData['parts'][UserMoneyRecord::MODE_WALLET]['parts']['draw_money'];
            $handselTotal = $buyData['parts'][UserMoneyRecord::MODE_WALLET]['parts']['handsel'];
            $balanceTotal = '0.00';

            // 使用钱包支付，但彩金和可提现余额都为0的情况下，认为该笔支付扣的是充值余额
            if ($walletTotal > 0 && ($drawBalanceTotal + $handselTotal == 0)) {
                if ($record['final_balance'] < $walletTotal) {
                    $drawBalanceTotal = $walletTotal;
                } else {
                    $balanceTotal = $walletTotal;
                }

            }

            // 从支付宝、微信、彩金等支付方式中提取组合使用的可提现余额和彩金的
            array_walk($buyData['parts'], function($item, $key) use (&$walletTotal, &$drawBalanceTotal, &$handselTotal){
                if ($key == UserMoneyRecord::MODE_WALLET) return;

                $walletTotal = sprintf('%.2f', $walletTotal + $item['total']);
                $drawBalanceTotal = sprintf('%.2f', $drawBalanceTotal + $item['parts']['draw_money']);
                $handselTotal = sprintf('%.2f', $handselTotal + $item['parts']['handsel']);
            });

            $balanceTotal = (float)$balanceTotal
                ? $balanceTotal
                : sprintf('%.2f', $walletTotal - $drawBalanceTotal - $handselTotal);

            $data = [
                'type' => UserBalanceRecord::TYPE_BUY,
                'desc' => '购彩',
                'initial_total' => $record['final_total'],
                'change_total' => -$walletTotal,
                'final_total' => $record['final_total'] + (-$walletTotal),

                'initial_balance' => $record['final_balance'],
                'change_balance' => - $balanceTotal,
                'final_balance' => $record['final_balance'] + (-$balanceTotal),

                'initial_handsel' => $record['final_handsel'],
                'change_handsel' => -$handselTotal,
                'final_handsel' => $record['final_handsel'] + (-$handselTotal),

                'initial_draw_balance' => $record['final_draw_balance'],
                'change_draw_balance' => -$drawBalanceTotal,
                'final_draw_balance' => $record['final_draw_balance'] + (-$drawBalanceTotal),
                'date' => $yesterday
            ];
            $record = UserBalanceRecord::create($data)->refresh();
        }

        $bonusTotal = $bonusData['total'];
        if (floatval($bonusTotal)) {
            $data = [
                'type' => UserBalanceRecord::TYPE_BONUS,
                'desc' => '返奖',
                'initial_total' => $record['final_total'],
                'change_total' => $bonusTotal,
                'final_total' => $record['final_total'] + $bonusTotal,
                'initial_draw_balance' => $record['final_draw_balance'],
                'change_draw_balance' => $bonusTotal,
                'final_draw_balance' => $record['final_draw_balance'] + $bonusTotal,

                'initial_balance' => $record['final_balance'],
                'change_balance' => '0.00',
                'final_balance' => $record['final_balance'],
                'initial_handsel' => $record['final_handsel'],
                'change_handsel' => '0.00',
                'final_handsel' => $record['final_handsel'],
                'date' => $yesterday
            ];
            $record = UserBalanceRecord::create($data)->refresh();
        }

        $drawTotal = $drawData['total'];
        if (floatval($drawTotal)) {
            $data = [
                'type' => UserBalanceRecord::TYPE_DRAW,
                'desc' => '提现',
                'initial_total' => $record['final_total'],
                'change_total' => - $drawTotal,
                'final_total' => $record['final_total'] + (- $drawTotal),
                'initial_draw_balance' => $record['final_draw_balance'],
                'change_draw_balance' => -$drawTotal,
                'final_draw_balance' => $record['final_draw_balance'] + (-$drawTotal),

                'initial_balance' => $record['final_balance'],
                'change_balance' => '0.00',
                'final_balance' => $record['final_balance'],
                'initial_handsel' => $record['final_handsel'],
                'change_handsel' => '0.00',
                'final_handsel' => $record['final_handsel'],
                'date' => $yesterday
            ];
            $record = UserBalanceRecord::create($data)->refresh();
        }

        return $record;
    }

    /**
     * @param $moneyRecords
     * @return UserMoneyRecordCount|\Illuminate\Database\Eloquent\Model
     */
    public function saveUserMoneyRecordCount($moneyRecords)
    {
        return UserMoneyRecordCount::create([
            'date' => now()->subDay(1)->toDateString(),
            'count_data' => $moneyRecords,
        ]);
    }

    /**
     * @param null $period
     * @return array
     */
    public function countMoneyRecords($period = null)
    {
        $userMoneyRecordService = new UserMoneyRecordService;

        return $userMoneyRecordService->obtainMoneyRecords($period ?? Period::TODAY);
    }

    /**
     * @param $data
     * @return array
     */
    public function obtainMoneyData($data)
    {
        $recharge = $data[UserMoneyRecord::TYPE_RECHARGE];

        $buy = $data[UserMoneyRecord::TYPE_BUY];

        $bonus = $data[UserMoneyRecord::TYPE_BONUS];

        $draw = $data[UserMoneyRecord::TYPE_DRAW];

        return [$recharge, $buy, $bonus, $draw];
    }
}
