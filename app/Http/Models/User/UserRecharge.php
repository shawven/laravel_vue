<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-05-04 11:19
 */

namespace App\Http\Models\User;

use App\Http\Models\BaseModel;

class UserRecharge extends BaseModel
{
    const CREATED_AT = 'addtime';
    const UPDATED_AT = 'updatetime';

    protected $dateFormat = 'U';
    protected $returnDateFormat = self::STANDARD_DATETIME_FORMAT;

    protected $table = 'user_recharge';
    protected $fillable = [
        'id', 'money', 'state', 'user_id', 'addtime', 'updatetime', 'payway',
    ];
}
