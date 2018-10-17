<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-07-31 14:57
 */

namespace App\Http\Models\Activity;

use App\Http\Models\BaseModel;
use App\Http\Models\User\User;

class ActivityRecord extends BaseModel
{
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    protected $table = 'activity_record';

    protected $fillable = [
        'id', 'user_id', 'activity_id', 'create_time', 'update_time',
    ];

    protected $joinModels = ['u' => User::class, 'a' => Activity::class];
}
