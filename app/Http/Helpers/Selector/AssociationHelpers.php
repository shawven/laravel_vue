<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-05-20 14:11
 */

namespace App\Http\Helpers\Selector;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

trait AssociationHelpers
{

    /**
     * 实际上的用于连接的表名
     *
     * @param JoinItem $joinItem
     * @return mixed|string
     */
    private function getJoinTableName(JoinItem $joinItem)
    {
        return $this->getTableName($joinItem->model);
    }

    /**
     * 获取表名，有两种情况
     * 1，类对象是否是Model的子类，如果有自定义表名取自定义表名
     * 2，按照大写转下划线规则转换成表名
     *
     * @param string $class
     * @return mixed|string
     */
    public function getTableName($class)
    {
        // 判断类是否存在
        if (class_exists($class)) {
            $classObj = new $class;

            if ($classObj instanceof Model) {
                if ($classObj->getTable() != '') {
                    return $classObj->getTable();
                }

                return $this->getSnakeName(class_basename($class));
            }

            throw new \RuntimeException('Class ' . class_basename($class) . ' not an instance of ' . Model::class);
        }

        throw new ModelNotFoundException('Class ' . class_basename($class) . ' not found');
    }

    /**
     * @param $string
     * @return mixed
     */
    private function getSnakeName($string)
    {
        return str_replace('\\', '', Str::snake(Str::plural($string)));
    }
}
