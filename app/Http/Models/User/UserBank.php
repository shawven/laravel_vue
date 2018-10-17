<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-05-04 11:19
 */

namespace App\Http\Models\User;

use App\Http\Models\BaseModel;

class UserBank extends BaseModel
{
    const CREATED_AT = 'addtime';
    const UPDATED_AT = 'updatetime';

    protected $dateFormat = 'U';
    protected $returnDateFormat = self::STANDARD_DATETIME_FORMAT;

    protected $table = 'user_bank';
    protected $fillable = [
        'id', 'user_id', 'user_nick', 'bank_name', 'bank_card', 'bank_phone', 'state', 'addtime', 'updatetime'
    ];
}
