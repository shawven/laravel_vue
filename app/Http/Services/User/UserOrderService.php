<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-07-12 14:39
 */

namespace App\Http\Services\User;

use App\Http\Services\Lottery\LotteryService;

class UserOrderService
{
    public function analyze($id)
    {
        $lotteryService = app(LotteryService::class);

        $orders = $lotteryService->getOrders($id);

        $data = $lotteryService->detectSettlement($orders->first()->type, $orders);

        return $data;
    }
}
