<?php
/**
 * Created by PhpStorm.
 * @description: 查询字符串名称
 * @author: FS
 * @date: 2018-04-11 10:24
 */

namespace App\Http\Helpers\Selector\Config;


class QueryParam
{
    /**
     * 页数
     */
    const PAGE = 'page';

    /**
     * 限制条数
     */
    const LIMIT = 'limit';

    /**
     * in查询
     */
    const IN = 'i';

    /**
     * like查询
     */
    const LIKE = 'l';

    /**
     * 排序
     */
    const SORT = 's';

    /**
     * 范围查询
     */
    const RANGE = 'r';

    /**
     * 连表查询
     */
    const JOIN = 'j';

    /**
     * 连表别名
     */
    const JOIN_ALIAS = 'ja';
    /**
     * 连表on条件
     */
    const JOIN_ON = 'jo';

    /**
     * 连表where条件
     */
    const JOIN_WHERE = 'jw';

    /**
     * 连表where like条件
     */
    const JOIN_WHERE_LIKE = 'jwl';

    /**
     * 连表where Range条件
     */
    const JOIN_WHERE_RANGE = 'jwr';
}
