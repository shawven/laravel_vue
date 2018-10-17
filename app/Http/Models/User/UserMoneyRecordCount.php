<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-06-18 17:43
 */

namespace App\Http\Models\User;

use App\Http\Models\BaseModel;

class UserMoneyRecordCount extends BaseModel
{
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    protected $table = 'user_money_record_count';
    protected $fillable = ['id', 'detail', 'count_data', 'date', 'create_time', 'update_time'];

    /**
     * 应该被转化为原生类型的属性
     *
     * @var array
     */
    protected $casts = [
        'detail' => 'json',
        'count_data' => 'json'
    ];
}
