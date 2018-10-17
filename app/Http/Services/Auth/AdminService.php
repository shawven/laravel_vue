<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-05-13 22:07
 */

namespace App\Http\Services\Auth;

use App\Http\Common\Constants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminService
{
    /**
     * @var RoleService
     */
    private $roleService;

    /**
     * @var AuthorityService
     */
    private $authorityService;

    /**
     * AdminService constructor.
     * @param RoleService $roleService
     * @param AuthorityService $authorityService
     */
    public function __construct(RoleService $roleService, AuthorityService $authorityService)
    {
        $this->roleService = $roleService;
        $this->authorityService = $authorityService;
    }


    /**
     * @return array
     */
    public function getAuthorities()
    {
        $roleIdStr = session('user')->role_id;

        return $this->authorityService->getAllKindsRoutesByRoleIds(explode(',', $roleIdStr));
    }

    /**
     * @return array
     */
    public function getLatestAuthorities()
    {
        $this->authorityService->clearAuthorityCache();

        return $this->getAuthorities();
    }

    /**
     * @param array $admins
     * @return array
     */
    public function addRolesToAdmins(array $admins)
    {
        if (empty($admins)) return [];

        $roles = $this->roleService->selectAll();

        foreach ($admins as &$admin) {
            $admin->allRoles = $roles;
            $admin->roles = array_filter($roles, function($role) use ($admin){
               return $admin->role_id && (in_array($role['id'], explode(',', $admin->role_id)));
            });
        }

        return $admins;
    }

    /**
     * @param Request $request
     * @return false|string
     */
    public function uploadAvatar(Request $request)
    {
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $path = $request->file('avatar')->store('avatar', 'public');

            return config('filesystems.disks.public.url'). '/'. $path;
        }

        return '';
    }
}
