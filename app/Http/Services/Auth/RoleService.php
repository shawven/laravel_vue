<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-05-13 22:55
 */

namespace App\Http\Services\Auth;

use App\Http\Common\Constants;
use App\Http\Models\Auth\Role;

class RoleService
{
    /**
     * @return array
     */
    public function selectAll()
    {
        return Role::all()->toArray();
    }

    /**
     * @param $roleIds
     * @return array
     */
    public function getAuthorityIdsByRoleIds($roleIds)
    {
        if (empty($roleIds)) return [];

        $roles = Role::query()->whereIn('id', $roleIds)->get(['name', 'auth_id']);

        $AuthorityIds = [];

        foreach ($roles as $role) {
            array_push($AuthorityIds, ...explode(',', $role->auth_id));
        }

        return array_unique($AuthorityIds);
    }
}
