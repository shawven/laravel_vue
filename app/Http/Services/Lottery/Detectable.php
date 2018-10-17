<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-07-10 10:20
 */

namespace App\Http\Services\Lottery;

use App\Exceptions\BizException;
use App\Http\Models\User\BetInfo;
use App\Http\Models\User\UserOrder;
use App\Http\Models\User\UserOrderTicket;
use Exception;
use Illuminate\Database\Eloquent\Collection;

trait Detectable
{
    /**
     * @param UserOrder $order
     * @param Collection $matches
     * @return array
     */
    public function obtainOrderOdds(UserOrder $order, Collection $matches)
    {
        $totalOdds = 0;
        $actualBets = [];

        try {
            $ticketGuessesDetails = $this->obtainOrderTicketsGuessesDetails($order, $matches);
            // 遍历每张出票
            foreach ($ticketGuessesDetails as $ticketId => $guessesDetails) {

                [$ticketOdds, $describeItems] = $this->obtainTicketTotalOdds($order, $guessesDetails);

                // 多张出票的赔率相加
                $totalOdds += $ticketOdds;
                $actualBets[$ticketId] = $describeItems;
            }
        } catch (Exception $e) {

        } finally {
            $expectBets = $this->obtainOrderGuessesDetails($order, $matches);
        }

        return [$totalOdds, $actualBets, $expectBets];
    }


//    public function detect($ticketGuessesDetails, UserOrder $order, Collection $matches)
//    {
//        $play = $this->getOrderMatchesPlay($order->play_method, $matches->count());
//
//        $combinations = $this->getCombinationList($matches->all(), $play);
//
//        return 1;
//    }
//
//    public function getOrderMatchesPlay($playMethod, $count)
//    {
//        $plays = array_map(function ($item) {
//            $arr = explode(':', $item);
//            return $this->joinPlay($arr[1], $arr[0]) ;
//        },explode(',', $playMethod));
//
//        return $this->getPlayByCombinationList($count, $plays);
//    }

    /**
     * @param UserOrder $order
     * @param array $guessesDetails
     * @return array
     */
    public function obtainTicketTotalOdds(UserOrder $order, array $guessesDetails)
    {
        $describes = [];
        $totalOdds = 0;

        // 遍历组合（多关混合投注笛卡尔积组合）
        foreach ($guessesDetails as $combinationIndex => $details) {

            $combinationOdds = 1;

            // 遍历组合里的多场比赛
            /**@var BetInfo $betInfo */
            foreach ($details as $singleMatchDetailIndex => $betInfo) {
                $bool = null;
                $message = '';

                // 判断结果
                try {
                    $bool =  $this->determineMatchResult($order, $betInfo);
                } catch (Exception $e) {
                    $message = $e->getMessage();

                    if (!$betInfo->item->hasResult()) {
                        $message = "赛事ID：{$betInfo->item->id} 篮球比赛无结果";
                    }
                }

                $betInfo->type = $order->type;
                $betInfo->message = $message;
                $betInfo->win = $bool;

                $describes[$combinationIndex][$singleMatchDetailIndex] = $betInfo;

                if (!$bool) {
                    $combinationOdds = 0;
                } else {
                    // 混合过关组合后多关的赔率相乘
                    $combinationOdds *= floatval($betInfo->odds);
                }
            }

            // 赢取得有效的组合赔率相加
            if ($combinationOdds != 1) {
                $totalOdds += $combinationOdds;
            }
        }

        return [$totalOdds, $describes];
    }

    /**
     * 获取订单投注出票单的猜测信息
     *
     * @param $order
     * @param Collection $matches
     * @return array
     */
    public function obtainOrderTicketsGuessesDetails($order, Collection $matches)
    {
        /**@var Collection $tickets*/
        $tickets = $order->tickets;

        if ($tickets->isEmpty()) {
            throw new BizException("订单：{$order->id}无出票信息");
        }

        $guesses = [];

        foreach ($tickets as $ticket) {
            /**@var UserOrderTicket $ticket*/
            if ($ticket->isMixedPassage()) {
                $details = $this->getMixedPassageOrderTicketBetDetails($ticket, $order->bet, $matches);
            } else {
                $details = $this->getSinglePassageOrderTicketBetDetails($ticket, $order->bet, $matches);
            }

            $guesses[$ticket->id] = $details;
        }

        return $guesses;
    }

    /**
     * 获取订单投注的猜测信息
     *
     * @param $order
     * @param Collection $matches
     * @return array
     */
    public function obtainOrderGuessesDetails(UserOrder $order, Collection $matches)
    {
        return BetInfo::parseOrderBet($order->bet, $matches);
    }

    /**
     * ‘//’分隔符的是多关的比赛，多关比赛需要取所有比赛的组合集，判断每个赛事组合内的投注来确定奖金发放
     * 在混合投注的情况下，组合集内投注需要取笛卡尔积
     *
     * @param $ticket
     * @param $orderBet
     * @param Collection $matches
     * @return mixed
     */
    public function getMixedPassageOrderTicketBetDetails($ticket, $orderBet, Collection $matches)
    {
        $arrays = array_map(function($item) use ($orderBet, $matches) {
            return BetInfo::parseOrderTicketBet($item, $orderBet, $matches);
        }, explode('//', $ticket->Odds));

        // array_map 使维度增加了1，减少
        return $this->crossJoin(...collect($arrays)->flatten(1));
    }

    /**
     * 单关混合投注的情况下，每注都是独立的，把每注包装成一个赛事组合
     *
     * @param $ticket
     * @param $orderBet
     * @param Collection $matches
     * @return mixed
     */
    public function getSinglePassageOrderTicketBetDetails($ticket, $orderBet, Collection $matches)
    {
        $array = current(BetInfo::parseOrderTicketBet($ticket->Odds, $orderBet, $matches));

        return array_map(function($item) {
            return [$item];
        }, $array);
    }
}
