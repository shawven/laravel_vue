<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-06-13 17:35
 */

namespace App\Http\Models\User;

use App\Http\Models\BaseModel;

class UserChannel extends BaseModel
{
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    protected $table = 'user_channel';
    protected $fillable = ['id', 'mark', 'name', 'app_promotion_url', 'create_time', 'update_time'];
}
