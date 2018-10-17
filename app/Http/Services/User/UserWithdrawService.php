<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-06-21 10:44
 */

namespace App\Http\Services\User;

use App\Exceptions\BizException;
use App\Http\Models\User\User;
use App\Http\Models\User\UserMoneyRecord;
use App\Http\Models\User\UserWithdraw;
use App\Notifications\WithdrawNotification;
use Illuminate\Http\Request;

class UserWithdrawService
{
    /**
     * @param $withdrawId
     * @return mixed
     */
    public function acceptWithdraw($withdrawId)
    {
        $userWithdraw = UserWithdraw::query()->find($withdrawId);

        $user = User::query()->find($userWithdraw->user_id);

        try {
            $bool = \DB::transaction(function () use ($user, $userWithdraw) {
                $record = UserMoneyRecord::create([
                    'userId' => $user->id,
                    'obj_id' => $userWithdraw->id,
                    'type' => UserMoneyRecord::TYPE_DRAW,
                    'typename' => '提现—余额提现',
                    'money' => $userWithdraw->money,
                    'pre_total' => $user->draw_balance,
                    'after_total' => sprintf('%.2f', $user->draw_balance - $userWithdraw->money),
                ]);

                $userWithdraw->state = UserWithdraw::DRAW_SUCCESS;
                $bool = $userWithdraw->update();

                $user->withdraw_id = $userWithdraw->id;
                $user->withdraw_result = true;
                $user->money_record_id = $record->id;

                return !!$record && $bool ;
            });

            if ($bool) {
                $withdraw_time = date('Y年m月d日H:i', $userWithdraw->addtime->getTimestamp());
                $message = "您的账户于{$withdraw_time}发起提现{$userWithdraw->money}已到账。[疯狂彩票]";

                \Notification::send($user, new WithdrawNotification($message));
            }

            return $bool;
        } catch (\Throwable $e) {
            throw new BizException('提现失败！', $e);
        }
    }

    /**
     * @param Request $request
     * @param $withdrawId
     * @return mixed
     */
    public function rejectWithdraw(Request $request, $withdrawId)
    {
        $userWithdraw = UserWithdraw::query()->find($withdrawId);

        /**@var User $user*/
        $user = User::query()->find($userWithdraw->user_id);
        try {
            $bool = \DB::transaction(function () use ($user, $userWithdraw) {
                $userWithdraw->state = UserWithdraw::DRAW_FAIL;
                $bool1 = $userWithdraw->update();

                $bool2 = $user->addDrawBalanceAndWallet($userWithdraw->money);

                $user->withdraw_id = $userWithdraw->id;
                $user->withdraw_result = false;

                return $bool1 && $bool2 ;
            });

            if ($bool) {
                $withdraw_time = date('Y年m月d日H:i', $userWithdraw->addtime->getTimestamp());
                $message = "您的账户于{$withdraw_time}发起提现{$userWithdraw->money}被拒：{$request->reason}。[疯狂彩票]";

                \Notification::send($user, new WithdrawNotification($message));
            }

            return $bool;
        } catch (\Throwable $e) {
            throw new BizException('提现失败！', $e);
        }
    }
}
