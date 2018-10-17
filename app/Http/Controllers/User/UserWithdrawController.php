<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-05-30 18:02
 */

namespace App\Http\Controllers\User;

use App\Http\Common\ResponseUtils;
use App\Http\Controllers\Base\BaseRestController;
use App\Http\Models\User\UserWithdraw;
use App\Http\Services\User\UserWithdrawService;
use Illuminate\Http\Request;

class UserWithdrawController extends BaseRestController
{
    /**
     * @var UserWithdrawService
     */
    private $userWithdrawService;

    /**
     * UserWithdrawController constructor.
     * @param UserWithdrawService $userWithdrawService
     */
    public function __construct(UserWithdrawService $userWithdrawService)
    {
        $this->userWithdrawService = $userWithdrawService;
    }


    public function update(Request $request, ...$pathVariables)
    {
        if ($request->state == UserWithdraw::DRAW_SUCCESS) {
            $bool = $this->userWithdrawService->acceptWithdraw(end($pathVariables));
            if ($bool) {
                return ResponseUtils::ok('提现成功');
            }
            return ResponseUtils::unprocesableEntity('提现失败');
        }
        if ($request->state == UserWithdraw::DRAW_FAIL) {
            $bool = $this->userWithdrawService->rejectWithdraw($request, end($pathVariables));
            if ($bool) {
                return ResponseUtils::ok('拒绝成功');
            }
            return ResponseUtils::unprocesableEntity('拒绝失败');
        }

        return parent::update($request, ...$pathVariables);
    }

}
