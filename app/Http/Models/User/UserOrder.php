<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-05-01 22:33
 */

namespace App\Http\Models\User;

use App\Http\Models\BaseModel;
use http\Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserOrder extends BaseModel
{
    /**
     * @var int 开奖中
     */
    const OPENING_LOTTERY = 1;

    /**
     * @var int 彩票未中奖
     */
    const NO_WINNING_THE_LOTTERY = 2;

    /**
     * @var int 彩票中奖
     */
    const WINNING_THE_LOTTERY = 3;

    /**
     * @var int 支付成功
     */
    const PAY_TYPE_SUCCESS = 2;

    /**
     * @var int 出票成功
     */
    const TICKET_SUCCESS = 3;

    /**
     * @var string 竞彩足球
     */
    const TYPE_FOOTBALL = 'jczq';

    /**
     * @var string 竞彩篮球
     */
    const TYPE_BASKETBALL = 'jclq';

    const CREATED_AT = 'addtime';
    const UPDATED_AT = 'updatetime';

    protected $dateFormat = 'U';
    protected $returnDateFormat = self::STANDARD_DATETIME_FORMAT;

    protected $table = 'user_order';
    protected $fillable = [
        'id', 'user_id', 'order_id', 'sid', 'total_money', 'type', 'guoguan', 'beishu', 'play_method', 'payway',
        'paytime', 'state', 'addtime', 'updatetime', 'payTypeName', 'bonus', 'bet', 'to_draw',
    ];

    protected $joinModels = ['u' => User::class, 'uot' => UserOrderTicket::class];

    /**
     * 修复订单的更新时间
     */
    public static function repairPreviousUpdateTime()
    {
        $startOfTime = now()->subDay()->startOfDay()->getTimestamp();
        $endOfTime = now()->subDay()->endOfDay()->getTimestamp();

        $orders = UserOrder::query()
            ->whereBetween('addtime', [$startOfTime, $endOfTime])
            ->where('updatetime', '0')
            ->lockForUpdate()
            ->get();

        foreach ($orders as $order) {
            $order->updatetime = $order->addtime;
            $order->update();
        }
    }

    /**
     * @param array $ids
     * @return Collection
     */
    public static function getOrdersById(array $ids)
    {
        $orders = static::query()->selectRaw('*, IF(state = 3, 1, 0) as settled')->whereKey($ids)->get();

        return static::transformBet($orders);
    }

    /**
     * @param $type
     * @param $startTime
     * @param $endTime
     * @return \Illuminate\Support\Collection
     */
    public static function getOpeningLotteryOrdersByPayTime($type, $startTime, $endTime)
    {
        $query = static::query()->where('state', UserOrder::OPENING_LOTTERY);

        return static::getValidOrdersByTimes($query, $type, $startTime, $endTime);
    }

    /**
     * @param $type
     * @param $startTime
     * @param $endTime
     * @return \Illuminate\Support\Collection
     */
    public static function getOpeningAndWinningOrdersByPayTime($type, $startTime, $endTime)
    {
        $query = static::query()
            ->selectRaw('*, IF(state = 3, 1, 0) as settled')
            ->where('state', '!=', UserOrder::NO_WINNING_THE_LOTTERY);

        return static::getValidOrdersByTimes($query, $type, $startTime, $endTime);
    }

    /**
     * @param Builder $builder
     * @param $type
     * @param $startTime
     * @param $endTime
     * @return Collection
     */
    public static function getValidOrdersByTimes(Builder $builder, $type, $startTime, $endTime) {
        $orders = $builder
            ->where('type', $type)
            ->where('to_draw', UserOrder::TICKET_SUCCESS)
            ->whereBetween('paytime', [$startTime, $endTime])
            ->get();

        return static::transformBet($orders);
    }

    /**
     * @param Collection $orders
     * @return Collection
     */
    public static function withOrderTickets(Collection $orders)
    {
        $ids = $orders->pluck('id');

        $tickets = UserOrderTicket::query()->whereIn('obj_id', $ids)->get()->groupBy('obj_id')->all();

        $orders->transform(function($order) use ($tickets) {
            $order->tickets = isset($tickets[$order->id]) ? $tickets[$order->id] : new Collection();
            return $order;
        });

        return $orders;
    }

    /**
     * @param Collection $orders
     * @return Collection
     */
    public static function transformBet(Collection $orders)
    {
        $orders->transform(function($order) {
            /**@var UserOrder $order*/
            $order->unserializeBet();
            return $order;
        });

        return $orders;
    }

    /**
     * @return array
     */
    public function getOrderRelatedItemIds()
    {
        return explode(',', $this->bet['id']);
    }

    /**
     * 是否单关
     *
     * @return bool
     */
    public function isSinglePassage()
    {
        return $this->play_method == '1:1';
    }

    /**
     * 是否中奖
     *
     * @return bool
     */
    public function isWin()
    {
        return $this->state == static::WINNING_THE_LOTTERY;
    }

    /**
     * 是否中奖
     *
     * @return bool
     */
    public function isLose()
    {
        return $this->state == static::NO_WINNING_THE_LOTTERY;
    }

    /**
     * 反序列化投注
     */
    public function unserializeBet()
    {
        try {
            $this->bet = unserialize($this->bet);
        } catch (Exception $exception) {

        }
    }

}
