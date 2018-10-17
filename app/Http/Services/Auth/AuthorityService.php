<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-05-13 22:52
 */

namespace App\Http\Services\Auth;

use App\Exceptions\BizException;
use App\Http\Common\Constants;
use App\Http\Models\Auth\Authority;
use App\Http\Models\Auth\Role;
use App\Http\Helpers\Selector\Page;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;

class AuthorityService
{
    /**
     * @return Page
     */
    public function getAuthorityList()
    {
        $authorities = $this->selectAndCacheAll();

        $tree = $this->convertAuthoritiesForTreeStructure($authorities);

        $total = count($tree);
        $page = new Page(1, $total);
        $page->setList($tree);
        $page->setTotal($total);

        return $page;
    }

    /**
     * @param array $roleIds
     * @return array
     */
    public function getAllKindsRoutesByRoleIds(array $roleIds)
    {
        $authorities = $this->selectAndCacheAll();

        if (in_array(Role::SUPER_ADMIN_ROLE, $roleIds)) {
            return [
                'routes' => $this->convertAuthoritiesForRouteStructure($authorities),
                'grantedRoutes' => $this->convertAuthoritiesForRouteStructure($authorities),
                'forbiddenRouterNames' => []
            ];
        }

        $authorityIds = app(RoleService::class)->getAuthorityIdsByRoleIds($roleIds);

        $allowedAuthority = [];
        $forbiddenAuthority = [];
        foreach ($authorities as $authority) {
            if (in_array($authority['id'], $authorityIds)) {
                $allowedAuthority[] = $authority;
            } else {
                $forbiddenAuthority[] = $authority;
            }
        }

        return [
            'routes' => $this->convertAuthoritiesForRouteStructure($authorities),
            'grantedRoutes' => $this->convertAuthoritiesForRouteStructure($allowedAuthority),
            'forbiddenRouterNames' => $this->getAuthorityNames($forbiddenAuthority)
        ];
    }

    /**
     * @param array $authorities
     * @return array
     */
    public function convertAuthoritiesForRouteStructure(array $authorities)
    {
        return $this->modifyAuthoritiesToRoutes($this->getAuthorityTrees($authorities));
    }

    /**
     * 清除权限缓存和URL拦截器资源缓存
     */
    public function clearAuthorityCache()
    {
        Cache::forget(Constants::CACHE_URL_ACL);
        Cache::forget(Constants::CACHE_ADMIN_AUTHORITIES);
    }

    /**
     * @return mixed
     */
    public function selectAndCacheAll()
    {
        return Cache::rememberForever(Constants::CACHE_ADMIN_AUTHORITIES, function () {
            $authorities = Authority::all()->toArray();

            usort($authorities, function ($item1, $item2) {
                return intval($item1['sort']) - intval($item2['sort']);
            });

            return $authorities;
        });
    }

    /**
     * @param array $roleIds
     * @return array
     */
    public function getAuthoritiesByRoleIds(array $roleIds)
    {
        if (in_array(Role::SUPER_ADMIN_ROLE, $roleIds)) {
            return $this->selectAndCacheAll();
        }

        return array_map(function($authority) use ($roleIds) {
            return in_array($authority['id'], $roleIds);
        }, $this->selectAndCacheAll());
    }

    /**
     * @param array $authorities
     * @param int $parentId
     * @return array
     */
    public function getAuthorityTrees(array $authorities, $parentId = 0)
    {
        if (empty($authorities)) return [];

        $trunk = [];

        foreach ($authorities as $index => $authority) {
            $authorityId = $authority['id'];
            $authorityParentId = intval($authority['parent_id']);

            if ($authorityParentId === $parentId) {
                $authority['children'] = $this->getAuthorityTrees($authorities, $authorityId);
                $trunk[] = $authority;
            }
        }

        return $trunk;
    }

    /**
     * @param array $authorities
     * @return array
     */
    public function convertAuthoritiesForTreeStructure(array $authorities)
    {
        if (empty($authorities)) return [];

        foreach ($authorities as &$authority) {
            $authority['expand'] = $authority['level'] < 2;
            $authority['selected'] = false;
            $authority['checked'] = false;
        }

        return $this->getAuthorityTrees($authorities);
    }

    /**
     * @param array $authorities
     * @return array
     */
    public function modifyAuthoritiesToRoutes(array $authorities)
    {
        if (empty($authorities)) return [];

        $tempArray = [];

        foreach ($authorities as $authority) {
            $temp = [
                'title' => $authority['title'],
                'name' => $authority['name'],
                'path' => $authority['path'] ?? '',
                'icon' => $authority['icon'],
                'type' => $authority['type'],
                'component' => $authority['component'] ?? '',
                'props' => true
            ];

            if ($authority['children']) {
                if ($authority['level'] <= 1) {
                    $temp['children'] = $this->modifyAuthoritiesToRoutes($authority['children']);
                } else {
                    $temp['meta'] = $this->modifyAuthoritiesToRoutes($authority['children']);
                }
            }

            $tempArray[] = $temp;
        }

        return $tempArray;
    }

    /**
     * @param array $authorities
     * @return array
     */
    public function getAuthorityNames(array $authorities)
    {
        if (empty($authorities)) return [];

        return array_map(function($item) {
            return $item['name'];
        }, $authorities);
    }


    /**
     * @param $authId
     * @return mixed
     */
    public function deleteSelfAndChildren($authId)
    {
        try {
            return \DB::transaction(function () use ($authId) {
                $childrenIds = $this->getChildrenIds($authId);

                $int = Authority::destroy($authId, ...$childrenIds);

                return $int > 0;
            });
        } catch (\Throwable $e) {
            throw new BizException('删除失败', $e);
        }
    }

    /**
     * @param $authId
     * @return array
     */
    public function getChildrenIds($authId)
    {
        $ids = Authority::getChildrenIds($authId);

        foreach ($ids as $id) {

            $childrenIds = $this->getChildrenIds($id);
            if ($childrenIds) {
                array_push($ids, ...$childrenIds);
            }
        }

        return $ids;
    }
}
