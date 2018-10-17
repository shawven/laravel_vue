<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-06-04 17:04
 */

namespace App\Http\Services\Lottery;

use App\Http\Models\Jingcai\BasketballMatch;
use App\Http\Models\Jingcai\FootballMatch;
use App\Http\Models\User\UserOrder;
use Illuminate\Database\Eloquent\Collection;

trait ItemHandlers
{
    /**
     * @param $type
     * @param $offset
     * @param $limit
     * @return array
     */
    public function getItemsList($type, $offset = 0, $limit = 3)
    {
        switch ($type) {
            case UserOrder::TYPE_BASKETBALL:
                $items = $this->getLimitedBasketballMatches($offset, $limit);
                break;
            case UserOrder::TYPE_FOOTBALL:
                $items = $this->getLimitedFootballMatches($offset, $limit);
                break;
            default:
                $items = [];
        }

        return $items;
    }

    /**
     * @param $offset
     * @param $limit
     * @return array
     */
    private function getLimitedBasketballMatches($offset, $limit)
    {
        $idItems = BasketballMatch::getIdsGroupByDate($offset, $limit);

        $matchIds = $this->getFlatItems($idItems);


        $items = BasketballMatch::query()
            ->orderByDesc('date')
            ->whereKey($matchIds)
            ->get()
            ->all();

        $items = BasketballMatch::addOddsToMatches($items, $matchIds);
        $items = BasketballMatch::addResultToMatches($items);

        return $items;
    }

    /**
     * @param $offset
     * @param $limit
     * @return array
     */
    private function getLimitedFootballMatches($offset, $limit)
    {
        $idItems = FootballMatch::getIdsGroupByDate($offset, $limit);;

        $matchIds = $this->getFlatItems($idItems);

        $items = FootballMatch::query()
            ->orderByDesc('date')
            ->whereKey($matchIds)
            ->get()
            ->all();

        $items = FootballMatch::addOddsToMatches($items, $matchIds);

        return $items;
    }

    /**
     * @param $type
     * @param $orders
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getItemsByOrders($type, Collection $orders)
    {
        $itemIds = $orders->map(function($order) {
            /**@var UserOrder $order*/
            return $order->getOrderRelatedItemIds();
        })->flatten(2)->toArray();

        switch ($type) {
            case UserOrder::TYPE_BASKETBALL:
                $items = $this->getBasketballMatchesForSettleOrder($itemIds);
                break;
            case UserOrder::TYPE_FOOTBALL:
                $items = $this->getFootballMatchesForSettleOrder($itemIds);
                break;
            default:
                $items = new Collection([]);
        }

        return $items;
    }

    /**
     * @param array $ids
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getBasketballMatchesForSettleOrder(array $ids)
    {
        $items = BasketballMatch::query()->find($ids)->all();

        $items = BasketballMatch::addOddsToMatches($items, $ids);
        $items = BasketballMatch::addResultToMatches($items);

        return new Collection($items);
    }

    /**
     * @param array $ids
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getFootballMatchesForSettleOrder(array $ids)
    {
        $items = FootballMatch::query()->find($ids)->all();

        $items = FootballMatch::addOddsToMatches($items, $ids);

        return new Collection($items);
    }


    /**
     * @param $idStrings
     * @return array
     */
    private function getFlatItems($idStrings) {
        return explode(',',trim(implode(',', $idStrings),  ','));
    }

    /**
     * @param UserOrder $order
     * @param Collection $matches
     * @return Collection
     */
    public function getOrderRelatedMatchesResult(UserOrder $order, Collection $matches)
    {
        $ids = $order->bet
            ? ($order->bet['id'] ? explode(',', $order->bet['id']) : [])
            : [];

        return $matches->filter(function($match) use ($ids){
            return in_array($match->id, $ids);
        });
    }
}
