<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-06-21 22:06
 */

namespace App\Http\Models;

class UserBalanceRecord extends BaseModel
{
    /**
     *  @var int 充值
     */
    const TYPE_RECHARGE = 1;

    /**
     * @var int  购彩
     */
    const TYPE_BUY = 2;

    /**
     *  @var int 反奖
     */
    const TYPE_BONUS = 3;

    /**
     * @var int  提现
     */
    const TYPE_DRAW = 4;

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    protected $table = 'user_balance_records';
    protected $fillable = [
        'id', 'type', 'desc', 'initial_total', 'change_total', 'final_total', 'initial_balance', 'change_balance',
        'final_balance', 'initial_handsel', 'change_handsel', 'final_handsel', 'initial_draw_balance',
        'change_draw_balance', 'final_draw_balance', 'date', 'create_time', 'update_time'
    ];
}
