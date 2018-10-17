<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-04-06 23:14
 */
namespace App\Http\Models\Auth;

use App\Http\Models\BaseModel;

class Role extends BaseModel
{
    /**
     * 超级管理员角名
     */
    const SUPER_ADMIN_ROLE = 1;

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    protected $table = 'admin_role';

    protected $fillable = ['id', 'name', 'desc', 'auth_id', 'creator', 'create_time', 'update_time'];
}
