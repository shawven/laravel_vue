<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-04-22 14:45
 */

namespace App\Http\Models\User;

use App\Http\Models\BaseModel;

class UserMoneyRecord extends BaseModel
{
    /**
     *  @var int 奖金进账
     */
    const TYPE_BONUS = 1;
    /**
     * @var int  提现
     */
    const TYPE_DRAW = 2;
    /**
     * @var int  购彩
     */
    const TYPE_BUY = 3;
    /**
     * @var int  购彩
     */
    const TYPE_RECHARGE = 4;
    /**
     * @var string 支付宝
     */
    const MODE_ALIPAY = 'ALIPAY';
    /**
     * @var string  微信
     */
    const MODE_WXPAY = 'WXPAY';
    /**
     * @var string  钱包
     */
    const MODE_WALLET = 'WALLET';

    /**
     * @var string  彩金
     */
    const MODE_HANDSEL = 'caijin';

    /**
     * @var string
     */
    const USER_RECHARGE = '充值—用户充值';

    const CREATED_AT = 'addtime';
    const UPDATED_AT = null;

    protected $dateFormat = 'U';
    protected $returnDateFormat = self::STANDARD_DATETIME_FORMAT;

    protected $table = 'user_moneyrecord';
    protected $fillable = [
        'id', 'userId', 'obj_id', 'type', 'typename', 'mode', 'money', 'pre_total', 'after_total', 'addtime'
    ];

    protected $joinModels = ['u' => User::class];
}
