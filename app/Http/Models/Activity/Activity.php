<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-07-31 14:57
 */

namespace App\Http\Models\Activity;

use App\Http\Models\BaseModel;

class Activity extends BaseModel
{
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    protected $dates = [
        'create_time',
        'updated_at',
        'start_time',
        'end_time',
    ];

    protected $table = 'activity';

    protected $fillable = [
        'id', 'name', 'desc', 'number', 'forever', 'amount', 'state', 'start_time', 'end_time', 'create_time', 'update_time',
    ];

    public function unlimited()
    {
        return $this->number == 0;
    }
}
