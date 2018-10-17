<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-06-04 17:04
 */

namespace App\Http\Services\Lottery;

use App\Http\Models\User\UserOrder;

trait OrderHandlers
{

    /**
     * @param array|string $ids
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrders($ids)
    {
        $ids = is_array($ids) ? $ids : array_filter(explode(',', $ids));

        $orders = UserOrder::getOrdersById($ids);

        UserOrder::withOrderTickets($orders);

        return $orders;
    }


    /**
     * @param $type
     * @param $startTime
     * @param $endTime
     * @return \Illuminate\Support\Collection
     */
    public function getOpeningAndWinningOrdersByPayTime($type, $startTime, $endTime) {
        return UserOrder::getOpeningAndWinningOrdersByPayTime($type, $startTime, $endTime);
    }


    /**
     * @param $type
     * @param array $items
     * @return array
     */
    public function getOrderTimes($type, array $items)
    {
        $startTime =  null;
        $endTime =  now();

        $collection = collect($items);

        switch ($type) {
            case UserOrder::TYPE_BASKETBALL:
                $startTime = $collection->sortBy(function($a, $b) {
                    return strtotime($a['beginning']) - strtotime($b['beginning']);
                })->first()['beginning'];
                $endTime = $collection->sortBy(function($a, $b) {
                    return strtotime($b['endtime']) - strtotime($a['endtime']);
                })->first()['endtime'];
                break;
            case UserOrder::TYPE_FOOTBALL:
                $startTime = $collection->last()['date'];
                $endTime = $collection->first()['endtime'];
                break;
            default:
        }

        return [strtotime($startTime), strtotime($endTime)];
    }
}
