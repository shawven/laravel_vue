<?php
/**
 * Created by PhpStorm.
 * @description: 关联表信息对象
 * @author: FS
 * @date: 2018-04-09 14:54
 */

namespace App\Http\Helpers\Selector;

class JoinItem
{
    /**
     * 模型类
     */
    public $model;

    /**
     * 连接类型
     */
    public $type;

    /**
     * 关联关系名称
     */
    public $name;

    /**
     * 连接类型
     */
    public $fields = [];

    /**
     * 连表on条件
     */
    public $onConditions = [];

    /**
     * 连接where条件（相等）
     */
    public $whereEquals = [];

    /**
     * 连接where like条件
     */
    public $whereLikes = [];

    /**
     * 连接where 范围条件
     */
    public $whereRanges = [];

}
