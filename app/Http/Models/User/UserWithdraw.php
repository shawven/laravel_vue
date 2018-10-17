<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-05-30 18:03
 */

namespace App\Http\Models\User;

use App\Http\Models\BaseModel;

class UserWithdraw extends BaseModel
{
    /**
     * @var int 审核确认
     */
    const AUDIT_CONFIRM = 0;

    /**
     * @var int 审核通过
     */
    const AUDIT_SUCCESS = 1;

    /**
     * @var int 审核拒绝
     */
    const AUDIT_REJECT = 2;

    /**
     * @var int 提现确认
     */
    const DRAW_CONFIRM = 0;

    /**
     * @var int 提现成功
     */
    const DRAW_SUCCESS = 1;

    /**
     * @var int 提现失败
     */
    const DRAW_FAIL = 2;

    const CREATED_AT = 'addtime';
    const UPDATED_AT = 'updatetime';

    protected $dateFormat = 'U';
    protected $returnDateFormat = self::STANDARD_DATETIME_FORMAT;

    protected $table = 'user_withdraw';
    protected $fillable = [
        'id', 'bank_id', 'user_id', 'money', 'state', 'addtime', 'updatetime',
    ];

    protected $joinModels = ['u' => User::class, 'ub' => UserBank::class];
}
