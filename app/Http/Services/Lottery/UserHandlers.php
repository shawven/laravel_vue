<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-06-04 17:04
 */

namespace App\Http\Services\Lottery;


use App\Http\Models\User\User;
use Illuminate\Support\Collection;

trait UserHandlers
{

    /**
     * @param $orders
     *
     * @return array|mixed
     */
    public function getUsersByOrder(Collection $orders)
    {
        if ($orders->isEmpty()) return [];

        $users = User::query()->find($this->getUserIds($orders));

        $this->crossInfo($users, $orders);

        return $users;
    }

    /**
     * @param Collection $users
     * @param $userId
     * @return mixed
     */
    private function getUserByOrderFormUsers(Collection $users, $userId)
    {
        return $users->get($users->search(function($user) use ($userId){
            return $user->id == $userId;
        }));
    }

    /**
     * @param Collection $orders
     * @return array
     */
    private function getUserIds(Collection $orders)
    {
        return array_unique(array_column($orders->toArray(), 'user_id'));
    }

    /**
     * @param Collection $users
     * @param Collection $orders
     */
    private function crossInfo(Collection $users, Collection $orders)
    {
        $users->transform(function($user) use ($orders){
            $callback = function($order) use ($user) {
                if ($order->user_id === $user->id) {
                    $order->userInfo = $user->toArray();
                    return $order;
                }
                return null;
            };

            $user->orders = $orders->map($callback)->filter()->values()->all();
            return $user;
        });
    }
}
