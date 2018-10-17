<?php
/**
 * Created by PhpStorm.
 * @description: 关联条件构建器（针对当前资源实体类和关联实体类）
 * @author: FS
 * @date: 2018-04-12 14:43
 */

namespace App\Http\Helpers\Selector\Builder;

use App\Http\Helpers\Selector\AssociationHelpers;
use App\Http\Helpers\Selector\Config\JoinType;
use App\Http\Helpers\Selector\JoinItem;
use App\Http\Helpers\Selector\Parser\AssociationParser;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

class AssociationBuilder extends BaseBuilder
{
    use AssociationHelpers;

    /**
     * @var AssociationParser
     */
    protected $parser;

    /**
     * @param Model $model
     * @param QueryBuilder $queryBuilder
     * @return void
     */
    public function build(Model $model, QueryBuilder $queryBuilder)
    {
        $this->model = $model;

        $joinItems = $this->parser->getAssociations();

        if (empty($joinItems)) return;

        foreach ($joinItems as $joinItem) {

            if ($this->isIgnorable($joinItem)) continue;

            $this->addSelectFields($queryBuilder, $joinItem);
            $this->addWhereEquals($queryBuilder, $joinItem);
            $this->addWhereLikes($queryBuilder, $joinItem);
            $this->addWhereRanges($queryBuilder, $joinItem);

            if ($this->validJoin($joinItem)) {
                $this->join($queryBuilder, $joinItem);
            }
        }
    }

    /**
     *  是否可忽略
     *  1.自身关联且未设置别名时，即主表和连接表表名一样
     *  2.连接条件为空
     *
     * @param JoinItem $joinItem
     * @return bool
     */
    private function isIgnorable(JoinItem $joinItem)
    {
        return $this->getPrimaryTableName() == $this->getJoinTableName($joinItem) || empty($joinItem->onConditions);
    }

    /**
     * 添加查询的连接表字段
     *
     * @param QueryBuilder $queryBuilder
     * @param JoinItem $joinItem
     */
    private function addSelectFields(QueryBuilder $queryBuilder, JoinItem $joinItem)
    {
        if ($this->validFields($joinItem)) {

            $fields = array_map(function ($field) use ($joinItem) {
                return $this->getJoinTableName($joinItem) . '.' . last(explode('_', $field, 2)) . ' as ' . $field;
            }, $joinItem->fields);

            $queryBuilder->addSelect($fields);
        }
    }

    /**
     * @param $queryBuilder
     * @param $joinItem
     */
    private function addWhereEquals(QueryBuilder $queryBuilder, JoinItem $joinItem)
    {
        if ($this->validEquals($joinItem)) {
            foreach ($joinItem->whereEquals as $filed => $value) {
                $queryBuilder->where($this->getJoinTableName($joinItem) . '.' . $filed, $value);
            }
        }
    }

    /**
     * @param $queryBuilder
     * @param $joinItem
     */
    private function addWhereLikes(QueryBuilder $queryBuilder, JoinItem $joinItem)
    {
        if ($this->validLikes($joinItem)) {
            foreach ($joinItem->whereLikes as $filed => $value) {
                $queryBuilder->where($this->getJoinTableName($joinItem) . '.' . $filed, 'like', '%' . $value . '%');
            }
        }
    }

    /**
     * @param $queryBuilder
     * @param $joinItem
     */
    private function addWhereRanges(QueryBuilder $queryBuilder, JoinItem $joinItem)
    {
        if ($this->validRanges($joinItem)) {
            $joinTableName = $this->getJoinTableName($joinItem);
            foreach ($joinItem->whereRanges as $filed => $values) {
                if (isset($values[0])) {
                    if (isset($values[1])) {
                        $queryBuilder->whereBetween($joinTableName . '.' . $filed, [$values[0], $values[1]]);
                    } else {
                        $queryBuilder->where($joinTableName . '.' . $filed, '>=', $values[0]);
                    }
                } else if (isset($values[1])) {
                    $queryBuilder->where($joinTableName . '.' . $filed, '<=', $values[1]);
                }
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param JoinItem $joinItem
     */
    private function join(QueryBuilder $queryBuilder, JoinItem $joinItem)
    {
        $joinTableName = $this->getJoinTableName($joinItem);

        $foreignKey = $this->getForeignKey($joinItem);

        $referenceKey = $this->getReferenceKey($joinItem);

        switch (JoinType::typeOf($joinItem->type)) {
            case JoinType::JOIN:
                $queryBuilder->join($joinTableName, $foreignKey, '=', $referenceKey);
                break;
            case JoinType::LEFT_JOIN:
                $queryBuilder->leftJoin($joinTableName, $foreignKey, '=', $referenceKey);
                break;
            case JoinType::RIGHT_JOIN:
                $queryBuilder->rightJoin($joinTableName, $foreignKey, '=', $referenceKey);
                break;
            case JoinType::CROSS_JOIN:
                $queryBuilder->crossJoin($joinTableName, $foreignKey, '=', $referenceKey);
                break;
            default;
        }
    }

    /**
     * @param JoinItem $joinItem
     * @return bool
     */
    private function validJoin(JoinItem $joinItem)
    {
        return $this->validFields($joinItem) || $this->validEquals($joinItem)
            || $this->validLikes($joinItem) || $this->validRanges($joinItem);
    }

    /**
     * @param JoinItem $joinItem
     * @return bool
     */
    private function validFields(JoinItem $joinItem)
    {
        return !empty($joinItem->fields);
    }

    /**
     * @param JoinItem $joinItem
     * @return bool
     */
    private function validEquals(JoinItem $joinItem)
    {
        return !empty($joinItem->whereEquals);
    }

    /**
     * @param JoinItem $joinItem
     * @return bool
     */
    private function validLikes(JoinItem $joinItem)
    {
        return !empty($joinItem->whereLikes);
    }

    /**
     * @param JoinItem $joinItem
     * @return bool
     */
    private function validRanges(JoinItem $joinItem)
    {
        return !empty($joinItem->whereRanges);
    }

    /**
     * 连接表是否设置别名
     *
     * @param JoinItem $joinItem
     * @return bool
     */
    private function hasAlisa(JoinItem $joinItem)
    {
        return $joinItem->name != null;
    }

    /**
     * @param JoinItem $joinItem
     * @return string
     */
    private function getForeignKey(JoinItem $joinItem)
    {
        return $this->getJoinTableName($joinItem) . '.' . $joinItem->onConditions[0];
    }

    /**
     * @param JoinItem $joinItem
     * @return string
     */
    private function getReferenceKey(JoinItem $joinItem)
    {
        return $this->getPrimaryTableName(). '.' . $joinItem->onConditions[1];
    }

    /**
     * @return string
     */
    private function getPrimaryTableName()
    {
        return $this->getModel()->getTable();
    }

}
