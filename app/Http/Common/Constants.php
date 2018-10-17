<?php
/**
 * Created by PhpStorm.
 * @description: 系统常量
 * @author: VAIO
 * @date: 2018-05-13 10:57
 */

namespace App\Http\Common;

class Constants
{
    /**
     * model 命名空间
     */
    const MODELS_NAMESPACE = 'App\\Http\\Models';

    /**
     * 权限菜单缓存
     */
    const CACHE_ADMIN_AUTHORITIES = 'cache_admin_authorities';

    /**
     * URL权限访问控制
     */
    const CACHE_URL_ACL = 'cache_url_acl';
}
