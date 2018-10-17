<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-07-02 13:44
 */

namespace App\Http\Services\Lottery;

use App\Exceptions\BizException;
use App\Http\Models\Jingcai\BasketballMatch;
use App\Http\Models\Jingcai\BasketballMatchResult;
use App\Http\Models\Jingcai\FootballMatch;
use App\Http\Models\User\BetInfo;
use App\Http\Models\User\UserOrder;
use Illuminate\Support\Collection;

trait Determinable
{
    /**
     * @param UserOrder $order
     * @param BetInfo $betInfo
     * @return bool
     */
    public function determineMatchResult(UserOrder $order, BetInfo $betInfo)
    {
        switch ($order->type) {
            case UserOrder::TYPE_BASKETBALL:
                return $this->determineBasketBallResult($betInfo);
            case UserOrder::TYPE_FOOTBALL:
                return $this->determineFootballResult($betInfo);
            default:
                throw new BizException("该订单：{$order->id}类型出错");
        }
    }

    /**
     * @param BetInfo $betInfo
     * @return bool
     */
    public function determineBasketBallResult(BetInfo $betInfo)
    {
        $guessResult = $betInfo->guess;
        $matchItem = $betInfo->item;
        $matchPlay = $betInfo->play;
        $result = null;

        [$awayScore, $homeScore] = array_map('intval', explode(':', $matchItem->result->Final_score));

        switch ($matchPlay) {
            case 'sf':
                $result = $homeScore > $awayScore ? 1 : 2;
                break;
            case 'rfsf':
                $result = $homeScore + intval($guessResult) > $awayScore ? 1 : 2;
                break;
            case 'dxf':
                $bool = (bool)preg_match('/|[大小](\d{3}\.5)|)/',  $matchItem->odds->dxf, $totalScores);
                if (!$bool) {
                    throw new BizException("竞彩篮球（胜负}）结果匹配失败{预计：{$guessResult}, 结果：{$matchItem->odds->dxf}}");
                }
                $result = $awayScore + $homeScore > $totalScores[0] ? 1 : 2;
                break;
            case 'sfc_k':
                $diffScore = $awayScore - $homeScore;
                $guessResult = $diffScore >= ($guessResult - 1) * 5 + 1 && $diffScore <= $guessResult * 5;
                $result = true;
                break;
            case 'sfc_z':
                $diffScore = $homeScore - $awayScore;
                $guessResult = $diffScore >= ($guessResult - 1) * 5 + 1 && $diffScore <= $guessResult * 5;
                $result = true;
                break;
            default:
                throw new BizException("篮球没有该玩法：{$matchPlay}");
        }

        return $guessResult == $result;
    }

    /**
     * @param BetInfo $betInfo
     * @return bool
     */
    public function determineFootballResult(BetInfo $betInfo)
    {
        $guessResult = $betInfo->guess;
        $matchItem = $betInfo->item;
        $matchPlay = $betInfo->play;
        $result = null;

        $bool = (bool)preg_match('/\((\d:\d)\)\s*(\d:\d)/', $matchItem->saiguo, $matches);

        if (!$bool) {
            throw new BizException("竞彩足球{$matchItem->id}赛果有误");
        }

        [$homeHalfSore, $awayHalfSore] = array_map('intval',explode(':', $matches[1]));
        [$homeTotalSore, $awayTotalSore] = array_map('intval',explode(':', $matches[2]));
        $halfDiffSore = $homeHalfSore - $awayHalfSore;
        $totalDiffSore = $homeTotalSore - $awayTotalSore;

        switch ($matchPlay) {
            case 'nspf':
                $result = $totalDiffSore >= 0
                    ? ($totalDiffSore > 0 ? 3 : 1)
                    : 0;
                break;
            case 'spf':
                $result = $totalDiffSore >= $matchItem->odds->rq
                    ? ($totalDiffSore > $matchItem->odds->rq ? 3 : 1)
                    : 0;
                break;
            case 'bf':
                $guessResult = str_replace(':', '', $guessResult);
                $result = $homeTotalSore . $awayTotalSore;
                break;
            case 'jqs':
                $result = $homeTotalSore + $awayTotalSore;
                break;
            case 'bqc':
                $guessResult = str_replace('_', '', $guessResult);
                $halfResult = $halfDiffSore >= 0
                    ? ($halfDiffSore > 0 ? 3 : 1)
                    : 0;
                $totalResult = $totalDiffSore >= 0
                    ? ($totalDiffSore > 0 ? 3 : 1)
                    : 0;
                $result = $halfResult . $totalResult;
                break;
            default:
                throw new BizException("足球没有该玩法：{$matchPlay}");
        }

        return $guessResult == $result;
    }
}
