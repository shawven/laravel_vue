<?php
/**
 * Created by PhpStorm.
 * @description: 连表类型
 * @author: FS
 * @date: 2018-04-11 10:24
 */

namespace App\Http\Helpers\Selector\Config;

class JoinType
{
    /**
     * 内连接
     */
    const JOIN = 'i';

    /**
     * 左连接
     */
    const LEFT_JOIN = 'l';

    /**
     * 右连接
     */
    const RIGHT_JOIN = 'r';

    /**
     * 交叉连接
     */
    const CROSS_JOIN = 'c';

    /**
     * @param $value
     * @return string
     */
    public static function typeOf($value)
    {
        $value = strtolower($value);
        if ($value == strtolower(static::LEFT_JOIN)) {
            return static::LEFT_JOIN;
        }
        if ($value == strtolower(static::RIGHT_JOIN)) {
            return static::RIGHT_JOIN;
        }
        if ($value == strtolower(static::CROSS_JOIN)) {
            return static::CROSS_JOIN;
        }
        return static::JOIN;
    }

    /**
     * @return array
     */
    public static function getAll()
    {
        try {
            $reflectionClass = new \ReflectionClass(static::class);
        } catch (\ReflectionException $e) {
            return [];
        }
        return $reflectionClass->getConstants();
    }
}
