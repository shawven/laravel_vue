<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-07-13 10:05
 */

namespace App\Http\Services\Lottery;

trait Calculable
{
    /**
     * @param $odds
     * @param $multiple
     * @return array
     */
    public function calculateMoney($odds, $multiple)
    {
        $money = $odds ? $this->roundOdds($odds) * 2 * $multiple : 0;

        if ($money > 1000) {
            return [$money * 0.2, $money * 0.8, $money];
        }

        return [0, $money, $money];
    }

    /**
     * 赔率计算，保留三位小数（小数点第三位的值为 0|5， 第三位四舍五入0|5）
     *
     * @param $odds
     * @return float
     */
    public function roundOdds($odds) {
        if (strpos($odds, '.') === false ) {
            return $odds;
        }

        [$integer, $decimal] = explode('.', $odds);

        if (!is_null($decimal) && strlen($decimal) > 3) {
            $decimal = substr($decimal, 0, 3);
            $lastPosition = intval($decimal[2]);
            $decimal[2] =  $lastPosition >= 0 && $lastPosition < 5 ? 0 : 5;
        }

        return floatval("{$integer}.{$decimal}");
    }

    /**
     * @param $stages
     */
    private function calcPercent(&$stages)
    {
        foreach ($stages as &$stage) {
            $total = count($stage['orders']);
            $awardedTotal = array_reduce($stage['orders'], function($carry, $order) {
                return $order->isWin() ? $carry + 1 : $carry;
            });

            $stage['lottery']['total'] = $total;
            $stage['lottery']['settled'] = intval($awardedTotal);
            if ($total) {
                $stage['lottery']['percent'] = round($awardedTotal / $total * 100, 2);
            } else {
                $stage['lottery']['percent'] = 0;
            }
        }
    }
}
