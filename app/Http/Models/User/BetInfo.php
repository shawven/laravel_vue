<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-07-31 10:47
 */

namespace App\Http\Models\User;

use App\Exceptions\BizException;
use Illuminate\Database\Eloquent\Collection;

class BetInfo
{
    public $item;

    public $play;

    public $guess;

    public $odds;

    public static function parseOrderBet($orderBet, Collection $matches)
    {
        $orderBetDetails = static::getOrderBetDetails($orderBet);

        $orderTicketBetDetails = [];

        foreach ($orderBetDetails as $matchSeqId => [$matchId, $betBeforeObject]) {
            $match = $matches->first(function($match) use ($matchId){
                return $match->id == $matchId;
            });

            if ($match == null) {
                throw new BizException('没有找到订单关联的赛事');
            }

            $betItems = explode('|', $betBeforeObject);

            foreach ($betItems as $item) {
                [$matchPlay, $guessAndOdds] = explode('-', $item, 2);
                [$guess, $odds] = explode('#', $guessAndOdds);

                $betInfo = new self();
                $betInfo->item = $match;
                $betInfo->play = $matchPlay;
                $betInfo->guess = $guess;
                $betInfo->odds = $odds;

                $orderTicketBetDetails[$matchSeqId][] = $betInfo;
            }
        }

        return $orderTicketBetDetails;
    }

    /**
     * 获取订单出票单的猜测投注详情
     *
     * @param $ticketBetItem
     * @param $orderBet
     * @param Collection $matches
     * @return array
     */
    public static function parseOrderTicketBet($ticketBetItem, $orderBet, Collection $matches)
    {
        $strings = explode(',', $ticketBetItem);

        $matchSeq = substr($strings[0], -3);

        $orderBetDetails = static::getOrderBetDetails($orderBet);

        [$match, $matchPlay] = static::getMatchByMatchGuess($orderBetDetails, $matches, $matchSeq);

        $guessAndOddsArray = explode('/', $strings[1]);

        $orderTicketBetDetails = [];
        foreach ($guessAndOddsArray as $guessAndOdds) {
            $odds = substr($guessAndOdds, strrpos($guessAndOdds, ':') + 1);
            $guess =str_replace(":$odds", '', $guessAndOdds);

            $betInfo = new self();
            $betInfo->item = $match;
            $betInfo->play = $matchPlay;
            $betInfo->guess = $guess;
            $betInfo->odds = explode('-', $odds)[0];

            $orderTicketBetDetails[$matchSeq][] = $betInfo;
        }

        return $orderTicketBetDetails;
    }

    /**
     * 获取投注时的期号ID、赛事ID和投注赔率
     *
     * @param array $orderBet
     * @return array
     */
    public static function getOrderBetDetails(array $orderBet)
    {
        // 期号id 去除第一个字符
        $matchSeqIds = array_map(function($id) {
            return substr($id, 1);
        }, explode(',', $orderBet['sid']));

        // 赛事id
        $matchIds = explode(',', $orderBet['id']);
        $betBeforeObjects = explode(',', $orderBet['bet']);

        $item = [];
        foreach ($matchIds as $index => $matchId) {
            $item[$matchSeqIds[$index]] = [$matchId, $betBeforeObjects[$index]];
        }

        return $item;
    }

    /**
     * 根据订单出票的猜测信息获取比赛数据
     *
     * @param array $orderBetDetails
     * @param Collection $matches
     * @param $matchSeq
     * @return mixed
     */
    public static function getMatchByMatchGuess($orderBetDetails, Collection $matches, $matchSeq)
    {
        foreach ($orderBetDetails as $matchSeqId => [$matchId, $betBeforeObject]) {
            if ($matchSeq == $matchSeqId) {

                $match = $matches->first(function($match) use ($matchId){
                    return $match->id == $matchId;
                });

                if ($match == null) {
                    throw new BizException('没有找到订单关联的赛事');
                }

                $matchPlay = explode('-', $betBeforeObject)[0];

                return [$match, $matchPlay];
            }
        }

        throw new BizException("出票赛事期号{$matchSeq} 有误");
    }

    public function toArray()
    {
        return [
            'play' => $this->play,
            'odds' => $this->odds,
            'guess' => $this->guess,
        ];
    }
}
