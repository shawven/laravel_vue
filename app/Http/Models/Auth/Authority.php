<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-04-06 23:12
 */
namespace App\Http\Models\Auth;

use App\Http\Models\BaseModel;

class Authority extends BaseModel
{
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    protected $table = 'admin_auth';

    protected $fillable = [
        'id', 'title', 'name', 'path', 'resource', 'icon', 'component', 'type', 'parent_id', 'level', 'status', 'sort',
        'create_time', 'update_time',
    ];

    /**
     * @param $id
     * @return array
     */
    public static function getChildrenIds($id)
    {
        return static::query()->where('parent_id', $id)->select('id')->pluck('id')->toArray();
    }
}
