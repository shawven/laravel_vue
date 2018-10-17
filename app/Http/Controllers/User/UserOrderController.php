<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-05-01 22:33
 */

namespace App\Http\Controllers\User;

use App\Http\Common\ResponseUtils;
use App\Http\Controllers\Base\BaseRestController;
use App\Http\Models\User\UserOrder;
use App\Http\Services\User\UserOrderService;
use Illuminate\Database\Eloquent\Collection;

class UserOrderController extends BaseRestController
{
    /**
     * @var UserOrderService
     */
    private $userOrderService;

    /**
     * UserOrderController constructor.
     * @param UserOrderService $userOrderService
     */
    public function __construct(UserOrderService $userOrderService)
    {
        $this->userOrderService = $userOrderService;
    }


    /**
     * @param array $list
     * @return array
     */
    protected function transform(array $list)
    {
        $orders = new Collection($list);

        UserOrder::withOrderTickets($orders);

        UserOrder::transformBet($orders);

        return $orders->all();
    }

    public function analyze($id)
    {
        $data = $this->userOrderService->analyze($id);

        return ResponseUtils::ok('获取订单数据成功', $data);
    }
}
