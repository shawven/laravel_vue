<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-06-03 15:40
 */

namespace App\Http\Services\Lottery;

use App\Exceptions\BizException;
use App\Http\Helpers\Selector\Page;
use App\Http\Models\User\User;
use App\Http\Models\User\UserMoneyRecord;
use App\Http\Models\User\UserOrder;
use App\Notifications\BonusSettlementNotification;
use Closure;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LotteryService
{
    use Calculable, Combinable, Determinable, Detectable, ItemHandlers, OrderHandlers, UserHandlers;

    /**
     * @param Request $request
     * @return array
     */
    public function getLotteryStages(Request $request)
    {
        $page = new Page($request->page, $request->limit);

        $items = $this->getItemsList($request->type, $page->getOffset(), $page->getLimit());

        [$startTime, $endTime] = $this->getOrderTimes($request->type, $items);

        $orders = $this->getOpeningAndWinningOrdersByPayTime($request->type, $startTime, $endTime);

        $users = $this->getUsersByOrder($orders);

        return $this->mixin($items, $orders, $users);
    }


    /**
     * @param $type
     * @param $orderIds
     * @return array
     */
    public function analyzeOrdersSettlementInfo($type, $orderIds)
    {
        $orders = $this->getOrders($orderIds);

        return $this->detectSettlement($type, $orders);
    }


    /**
     * @param $type
     * @param $orderIds
     */
    public function settleOrders($type, $orderIds)
    {
        $orders = $this->getOrders($orderIds);

        $this->settledOrdersAndPayBonuses($type, $orders);
    }


    /**
     * @param $type
     * @param $orders
     * @return array
     */
    public function detectSettlement($type, Collection $orders)
    {
        $result = [];
        $errors = [];

        $successHandler = function($rawData, $moneyData, $actualBets, $expectBets) use (&$result) {
            $this->collectResult($result, $rawData, $moneyData, $actualBets, $expectBets);
        };

        $errorHandler = function($rawData, $errorMessage) use (&$errors) {
            [$user, $order] = $rawData;
            $this->collectErrors($errors, $order, $user, $errorMessage);
        };

        $this->handleOrders($type, $orders, $successHandler, $errorHandler);

        return ['errors' => $errors, 'success' => $result];
    }

    /**
     * @param $type
     * @param Collection $orders
     */
    public function settledOrdersAndPayBonuses($type, Collection $orders)
    {
        $errors = [];

        $errorHandler = function($rawData, $errorMessage) use (&$errors) {
            [$user, $order] = $rawData;
            $this->collectErrors($errors, $order, $user, $errorMessage);
        };

        $successHandler = function($rawData, $moneyData) use ($errorHandler) {
            [$user, $order] = $rawData;
            [$tax, $afterTaxMoney, $bonus] = $moneyData;

            if (!$bonus) {
                $this->markNoWining($order);
                return;
            }

            $bool = $this->settledOrderAndPayBonus($user, $order, $moneyData);
            if ($bool) {
                $time = date('Y年m月d日H:i', $order->addtime->getTimestamp());
                $message = "您{$time}的订单已中奖 {$afterTaxMoney} 元已派发。[疯狂彩票]";
                \Notification::send($order, new BonusSettlementNotification($message));
                return;
            }

            $errorHandler($rawData, '订单已结算或结算失败');
        };

        $this->handleOrders($type, $orders, $successHandler, $errorHandler);

        $totalErrors = count($errors);
        $orderCount = count($orders);
        if ($totalErrors == 0) return;

        if ($totalErrors == $orderCount) {
            throw new \RuntimeException('订单派奖失败：' . PHP_EOL . print_r($errors, true));
        } else {
            throw new \RuntimeException('订单派奖部分失败：' . PHP_EOL . print_r($errors, true));
        }
    }

    /**
     * @param $type
     * @param Collection $orders
     * @param Closure $successHandler
     * @param Closure $errorHandler
     */
    public function handleOrders($type, Collection $orders, Closure $successHandler, Closure $errorHandler)
    {
        $items = $this->getItemsByOrders($type, $orders);

        $users = $this->getUsersByOrder($orders);

        /** @var UserOrder $order */
        foreach ($orders as $order) {
            $user = $this->getUserByOrderFormUsers($users, $order->user_id);

            try {
                $relatedMatches = $this->getOrderRelatedMatchesResult($order, $items);

                if ($relatedMatches->isEmpty()) {
                    throw new BizException('获取不到订单的比赛信息');
                }

                $rawData = [$user, $order, $relatedMatches];
                [$odds, $actualBets, $expectBets] = $this->obtainOrderOdds($order, $items);
                $moneyData = $this->calculateMoney($odds, $order->beishu);

                $successHandler($rawData, $moneyData, $actualBets, $expectBets);
            } catch (Exception $e) {
                $errorHandler([$user, $order], $e->getMessage());
            }
        }
    }

    /**
     * @param array $result
     * @param $rawData
     * @param $moneyData
     * @param $actualBets
     * @param $expectBets
     */
    public function collectResult(array &$result, $rawData, $moneyData, $actualBets, $expectBets)
    {
        [$user, $order, $lotteryItems] = $rawData;
        [$tax, $afterTaxMoney, $bonus] = $moneyData;

        $betDetails = collect($expectBets)->flatten(2)->unique()->all();

        foreach ($lotteryItems as &$item) {
            $temp = [];

            foreach ($betDetails as $betInfo) {
                if ($betInfo->item->id == $item->id) {
                    $temp[] = $betInfo->toArray();
                }
            }

            $item->betDetails = $temp;
        }

        $result[] =  [
            'orderId' => $order->id,
            'userId' => $user->id,
            'nickname' => $user->usernick,
            'betPlay' => $order->play_method,
            'tax' => sprintf('%.2f', $tax),
            'money' => sprintf('%.2f', $afterTaxMoney),
            'multiple' => $order->beishu,
            'ticketsDesc' => $actualBets,
            'items' => $lotteryItems
        ];
    }

    /**
     * @param array $errors
     * @param UserOrder $order
     * @param User $user
     * @param $message
     */
    public function collectErrors(array &$errors, UserOrder $order, User $user, $message)
    {
        $errors[$order->id] = [
            'userId' => $order->user_id,
            'orderId' => $order->id,
            'nickname' => $user->usernick,
            'message' => $message,
        ];
    }

    /**
     * @param User $user
     * @param UserOrder $order
     * @param $moneyData
     * @return bool
     */
    public function settledOrderAndPayBonus(User $user, UserOrder $order, $moneyData)
    {
        try {
            return DB::transaction(function () use ($user, $order, $moneyData) {
                [$tax, $afterTaxMoney, $bonus] = $moneyData;

                /**@var UserOrder $latestOrder*/
                $latestOrder = UserOrder::findOrFail($order->id);

                if ($latestOrder->isWin()) {
                    return false;
                }

                $latestOrder->state = UserOrder::WINNING_THE_LOTTERY;
                $latestOrder->bonus = $bonus;
                $latestOrder->tax = $tax;
                $latestOrder->afterTaxBonus = $afterTaxMoney;
                $orderUpdated = $latestOrder->update();

                /**@var User $latestUser*/
                $latestUser = User::findOrFail($user->id);
                $userUpdated = $latestUser->addDrawBalanceAndWallet(floatval($user->draw_balance) + floatval($afterTaxMoney));

                $record = UserMoneyRecord::create([
                    'userId' => $user->id,
                    'obj_id' => $order->id,
                    'type' => 1,
                    'type_name' => '中奖—奖金',
                    'money' => floatval($afterTaxMoney),
                    'pre_total' => floatval($user->draw_balance),
                    'after_total' => floatval($user->draw_balance) + floatval($afterTaxMoney)
                ]);

                return !!$orderUpdated && !!$userUpdated && !!$record;
            });
        } catch (\Throwable $e) {
            throw new BizException("订单结算失败，用户ID: {$user->id} 订单ID: {$order->id}", $e);
        }
    }

    /**
     * @param UserOrder $order
     */
    public function markNoWining(UserOrder $order)
    {
        /**@var UserOrder $latestOrder*/
        $latestOrder = UserOrder::query()->find($order->id);

        if ($latestOrder->isLose()) {
            return ;
        }

        $latestOrder->state = UserOrder::NO_WINNING_THE_LOTTERY;
        $latestOrder->update();
    }

    /**
     * @param $items
     * @param $orders
     * @param $users
     * @return array
     */
    private function mixin($items, $orders, $users)
    {
        [$stages, $dateItems] = $this->separateStages($items);

        $nowStages = $this->mixinOrderAndUser($dateItems['now'], $stages['now'], $orders, $users);
        $pastStages = $this->mixinOrderAndUser($dateItems['past'], $stages['past'], $orders, $users);

        return ['now' => $nowStages, 'past' => $pastStages];
    }

    /**
     * @param $items
     * @return array
     */
    private function separateStages($items)
    {
        $nowTimeStamp = time();

        $dateItems = ['now' => [], 'past' => []];
        $stages = ['now' => [], 'past' => []];
        foreach ($items as $index => $item) {
            $date = $item['date'];
            if (strtotime($item['endtime']) >= $nowTimeStamp) {
                $dateItems['now'][$item['id']] = $date;
                $stages['now'][$date]['items'][] = $item;
                $stages['now'][$date]['users'] = [];
                $stages['now'][$date]['orders'] = [];
            } else {
                $dateItems['past'][$item['id']] = $date;
                $stages['past'][$date]['items'][] = $item;
                $stages['past'][$date]['users'] = [];
                $stages['past'][$date]['orders'] = [];
            }
        }

        return [$stages, $dateItems];
    }

    /**
     * @param $dateAndItemIds
     * @param $stages
     * @param $orders
     * @param $users
     * @return \ArrayObject
     */
    private function mixinOrderAndUser($dateAndItemIds, $stages, $orders, $users)
    {
        if (empty($stages)) return new \ArrayObject;

        $itemIds = array_keys($dateAndItemIds);

        foreach ($orders as $order) {
            /**@var UserOrder $order*/
            $orderRelatedItemIds = $order->getOrderRelatedItemIds();
            foreach ($orderRelatedItemIds as $orderItemId) {
                if (in_array($orderItemId, $itemIds)) {
                    $stage = &$stages[$dateAndItemIds[$orderItemId]];
                    $this->pushOrder($stage, $order);
                    $this->pushUser($stage, $this->getUserByOrderFormUsers($users, $order->user_id));
                    break;
                }
            }
        }

        $this->calcPercent($stages);

        return $stages;
    }

    /**
     * @param $stage
     * @param $order
     */
    private function pushOrder(&$stage, $order)
    {
        $stage['orders'][] = $order;
    }

    /**
     * @param $stage
     * @param $user
     */
    private function pushUser(&$stage, $user)
    {
        if (!in_array($user, $stage['users'])) {
            $stage['users'][] = $user;
        }
    }
}
