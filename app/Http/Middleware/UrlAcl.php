<?php

namespace App\Http\Middleware;

use App\Http\Common\Constants;
use App\Http\Common\ResponseUtils;
use App\Http\Models\Auth\Role;
use App\Http\Services\Auth\AuthorityService;
use App\Http\Services\Auth\RoleService;
use Closure;
use http\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Routing\Router;

class UrlAcl
{
    /**
     * @var AuthorityService
     */
    private $authorityService;

    /**
     * @var RoleService
     */
    private $roleService;

    /**
     * false为严格模式
     *
     * @var bool
     */
    private $accessToPublicUrl = true;

    /**
     * UrlAcl constructor.
     * @param $authorityService
     * @param $roleService
     */
    public function __construct(AuthorityService $authorityService, RoleService $roleService)
    {
        $this->authorityService = $authorityService;
        $this->roleService = $roleService;
    }


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->method() == 'GET' || $this->decide($request)) {
            return $next($request);
        }

        return ResponseUtils::forbidden('没有权限，请联系管理员');
    }

    /**
     * 决定是否有权限访问当前URL
     *
     * @param Request $request
     * @return bool
     */
    public function decide(Request $request)
    {
        $visitorRoleIds =  $this->getVisitorRoleIds($request);

        // 访问者有超级管理员权限
        if (in_array(Role::SUPER_ADMIN_ROLE, $visitorRoleIds)) {
            return true;
        }

        // 获取url路径所对应的角色ID的资源定义
        $urlAclResources = $this->generateUrlAclResources();

        // 是否成功匹配URL
        $successMatchUrl = false;

        foreach ($urlAclResources as $url => $pathRoleIds) {
            // URL资源与当前访问URL匹配成功
            if ($this->matchUrl($url, $request)) {
                $successMatchUrl = true;
                // 访问者的角色是否在访问url所需要的角色当中
                foreach ($visitorRoleIds as $roleId) {
                    if (in_array($roleId, $pathRoleIds)) {
                        return true;
                    }
                }
                break;
            }
        }

        // 如果URL未成功匹配, 即当前URL为未配置拦截的URL资源
        if (!$successMatchUrl && $this->accessToPublicUrl) {
            return true;
        }

        // URL成功匹配, 但访问者没有权限
        return false;
    }

    /**
     * 获取访问者拥有的所有角色ID
     *
     * @param Request $request
     * @return array
     */
    public function getVisitorRoleIds(Request $request)
    {
        $roleIdStr = $request->session()->get('user')->role_id;

        if (!trim($roleIdStr)) {
            return [];
        }

        return  explode(',', $roleIdStr);
    }

    /**
     * 生成访问URL资源所对应的所有角色ID的资源定义，并缓存起来
     *
     * @return mixed
     */
    public function generateUrlAclResources()
    {
        return Cache::rememberForever(Constants::CACHE_URL_ACL, function() {
            $authorities = $this->authorityService->selectAndCacheAll();
            $roles = $this->roleService->selectAll();

            $urlAclResource = [];

            foreach ($authorities as $authority) {
                if ($authority <= 1) continue;
                if (!$url = trim($authority['resource'] ?? '')) continue;

                $urlAclResource[$url] = [];

                foreach ($roles as $role) {
                    // 角色的所有权限id
                    $roleAuthorityIds =  $role['auth_id'] ? explode(',', $role['auth_id']) : [];
                    // 权限在角色中
                    $authorityInRoles = in_array($authority['id'], $roleAuthorityIds);

                    if ($authorityInRoles) {
                        $urlAclResource[$url][] = $role['id'];
                    }
                }
            }

            return $urlAclResource;
        });
    }

    /**
     * 匹配URL
     *
     * @param $subjectUrl
     * @param Request $request
     * @return bool
     */
    public function matchUrl($subjectUrl, Request $request)
    {
        //
        $subjectUrl = trim($subjectUrl, '/');
        $requestUrl = trim($request->path(), '/');

        if ($subjectUrl == $requestUrl) {
            return true;
        }

        if (strpos($subjectUrl, ':')) {

            // 后端的动态路由
            $actualDynamicUrl = trim(ltrim($request->route()->uri()), '/');

            $pattern = preg_replace('/(?<=\/):([^\/]+)(?=\/)?/', '{{1}[^/]+}{1}', $subjectUrl);

            $expectDynamicUrlPattern = '/^' . str_replace('/', '\/', $pattern) . '$/';

            // 后端动态路由： $actualDynamicUrl    api/users/{user_id}/orders/{order_id}
            // 前端动态路由： $subjectUrl          api/users/:user_id/banks/:bank
            // 前端动态路由正则：/^api\/users\/{{1}[^\/]+}{1}\/banks\/{{1}[^\/]+}{1}$
            // 把前端的动态路由转化成正则表达式去匹配后端的动态路由
            if (preg_match($expectDynamicUrlPattern, $actualDynamicUrl) == 1) {
                return true;
            }
        }

        return false;
    }
}
