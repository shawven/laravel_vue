<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-05-11 22:34
 */

namespace App\Http\Controllers\Auth;

use App\Http\Common\IpUtils;
use App\Http\Common\ResponseUtils;
use App\Http\Controllers\Base\BaseRestController;
use App\Http\Models\Auth\Admin;
use App\Http\Services\Auth\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends BaseRestController
{
    /**
     * @var AdminService
     */
    private $adminService;

    /**
     * AdminController constructor.
     * @param AdminService $adminService
     */
    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * @param array $list
     * @return array
     */
    public function transform(array $list)
    {
        return $this->adminService->addRolesToAdmins($list);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthorities() {
        $authorities = $this->adminService->getAuthorities();

        if (empty($authorities)) {
            return ResponseUtils::notFound('暂无数据');
        }

        return ResponseUtils::ok('获取权限信息成功', $authorities);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatestAuthorities() {
        $authorities = $this->adminService->getLatestAuthorities();

        if (empty($authorities)) {
            return ResponseUtils::notFound('暂无数据');
        }

        return ResponseUtils::ok('获取权限信息成功', $authorities);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $admin = Admin::createAccount($request->all());

        if ($admin != null) {
            return ResponseUtils::created("账号添加成功", $admin);
        }
        return ResponseUtils::unprocesableEntity("账号添加成功", $admin);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        return $this->update($request, Auth::id());
    }

    public function uploadAvatar(Request $request)
    {
        $url = $this->adminService->uploadAvatar($request);

        if ($url) {
            return ResponseUtils::created('头像上传成功', $url);
        }

        return ResponseUtils::unprocesableEntity('头像上传失败');
    }

    /**
     * @param Request $request
     * @param $adminId
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request, $adminId) {
        $admin = Admin::query()->find($adminId);

        if ($admin == null) {
            return ResponseUtils::notFound("该账号不存在", $admin);
        }

        /** @var Admin $admin*/
        $bool = $admin->resetPassword($request->password);
        if ($bool) {
            return ResponseUtils::created("密码重置成功", $admin);
        }
        return ResponseUtils::unprocesableEntity("数据重置失败", $admin);
    }
}
