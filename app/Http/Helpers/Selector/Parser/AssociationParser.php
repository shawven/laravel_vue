<?php
/**
 * Created by PhpStorm.
 * @description: 关联解析器
 * @author: FS
 * @date: 2018-04-12 10:44
 */

namespace App\Http\Helpers\Selector\Parser;

use App\Http\Models\BaseModel;
use App\Http\Helpers\Selector\Config\JoinType;
use App\Http\Helpers\Selector\Config\QueryParam;
use App\Http\Helpers\Selector\Config\Range;
use App\Http\Helpers\Selector\JoinItem;

class AssociationParser extends BaseParser
{

    /**
     * @var array
     */
    private $associations = [];

    /**
     * 解析关联实体类的条件
     */
    public function Parse()
    {
        if ($this->getQueries()->has(QueryParam::JOIN)) {
            $this->resolveJoinTableAndField();
        }

        if (empty($this->associations)) return;

        if ($this->getQueries()->has(QueryParam::JOIN_ALIAS)) {
            $this->resolveJoinAssociationName();
        }

        if ($this->getQueries()->has(QueryParam::JOIN_ON)) {
            $this->resolveJoinOn();
        }

        if ($this->getQueries()->has(QueryParam::JOIN_WHERE)) {
            $this->resolveJoinWhereEquals();
        }

        if ($this->getQueries()->has(QueryParam::JOIN_WHERE_LIKE)) {
            $this->resolveJoinWhereLike();
        }

        if ($this->getQueries()->has(QueryParam::JOIN_WHERE_RANGE)) {
            $this->resolveJoinWhereRange();
        }
    }


    /**
     * 解析关联表名和表字段
     */
    private function resolveJoinTableAndField()
    {
        $array = (array)$this->getQueries()->pull(QueryParam::JOIN);
        $joinFields = $this->filterQueries($array, static::DELIMITER);

        foreach ($joinFields as $needle => $fields) {
            // 过滤查询字段*,避免查询所有
            if (($key = array_search("*", $fields)) !== false) {
                unset($fields[$key]);
            }

            $this->setJoinItem($needle, $fields);
        }
    }

    /**
     * 解析关联名称，连表查询的数据将以该名称为键组成二维关联数组
     */
    private function resolveJoinAssociationName()
    {
        $array = (array)$this->getQueries()->pull(QueryParam::JOIN_ALIAS);
        $associations = $this->filterQueries($array);

        foreach ($associations as $needle => $name) {
            $joinItem = $this->getJoinItem($needle);

            if ($joinItem == null) continue;

            // 重新定义关联名称
            $joinItem->name = $name;

            array_walk($joinItem->fields, function (&$field) use ($joinItem, $needle) {
                // 去除默认的字段关联名称，改为用户定义的
                $field = $joinItem->name . '_' . str_replace($needle . '_', '', $field);
            });
        }
    }

    /**
     * 解析关联表的on条件
     * on条件第一个参数是连接类型，on条件第二参数和第三参数是连接的外键和关联键
     * 1：当只有一个参数时，默认为内连接，该参数为同名的外键和关联键
     * 2：当只有二个参数时，①第一个参数是连接类型，则第二个参数为同名的外键和关联键
     *                     ②第一个参数不是连接类型时即为外键，第二个参数为关联键
     * 3: 当有三个参数时，第一个参数为连接类型，第二个参数外键，第三个参数为关联键
     */
    private function resolveJoinOn()
    {
        $array = (array)$this->getQueries()->pull(QueryParam::JOIN_ON);
        $joinConditions = $this->filterQueries($array, static::DELIMITER, 1);

        foreach ($joinConditions as $needle => $conditions) {
            $joinItem = $this->getJoinItem($needle);
            if ($joinItem == null) continue;

            // 只有一个参数
            if (count($conditions) == 1) {
                $joinItem->type = JoinType::JOIN;
                $joinItem->onConditions = [$conditions[0], $conditions[0]];
            // 只有二个参数时
            } else if (count($conditions) == 2) {
                // ①第一个参数是否为连接类型
                if (in_array($conditions[0], JoinType::getAll())) {
                    $joinItem->type = $conditions[0];
                    $joinItem->onConditions = [$conditions[1], $conditions[1]];
                } else {
                    $joinItem->type = JoinType::JOIN;
                    $joinItem->onConditions = [$conditions[0], $conditions[1]];
                }
            // 有三个参数时
            } else if (count($conditions) == 3) {
                $joinItem->type = $conditions[0];
                $joinItem->onConditions = [$conditions[1], $conditions[2]];
            }
        }
    }

    /**
     * 解析关联表的where条件
     */
    private function resolveJoinWhereEquals()
    {
        $array = (array)$this->getQueries()->pull(QueryParam::JOIN_WHERE);
        $joinWhereEquals = $this->filterQueries($array, static::DELIMITER, 2);

        foreach ($joinWhereEquals as $needle => $wheres) {
            $joinItem = $this->getJoinItem($needle);
            if ($joinItem == null) continue;

            $entry = array_chunk($wheres, 2);

            foreach ($entry as $item) {
                // where条件格式为 键_值 对必须成对出现
                if (count($item) == 2) {
                    // 去除值为空的项
                    if (is_null($item[1]) || $item[1] === "") continue;
                    $joinItem->whereEquals[$item[0]] = $item[1];
                }
            }

        }
    }

    /**
     * 解析关联表的whereLike条件
     */
    private function resolveJoinWhereLike()
    {
        $array = (array)$this->getQueries()->pull(QueryParam::JOIN_WHERE_LIKE);
        $joinWhereLikes = $this->filterQueries($array, static::DELIMITER, 2);

        foreach ($joinWhereLikes as $needle => $whereLikes) {
            $joinItem = $this->getJoinItem($needle);
            if ($joinItem == null) continue;

            $entry = array_chunk($whereLikes, 2);

            foreach ($entry as $item) {
                // where条件格式为 键_值 对必须成对出现
                if (count($item) == 2) {
                    // 去除值为空的项
                    if (is_null($item[1]) || $item[1] === "") continue;
                    $joinItem->whereLikes[$item[0]] = $item[1];
                }
            }

        }
    }

    /**
     * 解析关联表的where范围条件
     */
    private function resolveJoinWhereRange()
    {
        $array = (array)$this->getQueries()->pull(QueryParam::JOIN_WHERE_RANGE);
        $whereRanges = $this->filterQueries($array, static::DELIMITER, 2, 3);

        foreach ($whereRanges as $needle => $ranges) {
            $joinItem = $this->getJoinItem($needle);
            if ($joinItem == null) continue;
            $field = array_shift($ranges);

            if (count($ranges) == 1) {
                $hasMin = strpos($ranges[0], Range::PREFIX) === 0;
                $hasMax = stripos($ranges[0], Range::SUFFIX, strlen($ranges[0]) - 1) === strlen($ranges[0]) - 1;
                if ($hasMin) {
                    $joinItem->whereRanges[$field][0] = ltrim($ranges[0], Range::PREFIX);
                } else if ($hasMax) {
                    $joinItem->whereRanges[$field][1] = rtrim($ranges[0], Range::SUFFIX);
                }
            }
            if (count($ranges) == 2) {
                $joinItem->whereRanges[$field][0] = ltrim($ranges[0], Range::PREFIX);
            }
        }
    }

    /**
     * 设置关联对象
     *
     * @param $needle
     * @param array $fields
     */
    private function setJoinItem($needle, array $fields)
    {
        $joinModelClassName = $this->getJoinModelClassName($needle);

        if ($joinModelClassName !== null) {
            $joinItem = new JoinItem();

            $joinItem->model = $joinModelClassName;
            $joinItem->name = $needle;
            $joinItem->fields = array_map(function ($field) use ($needle) {
                return $needle . '_' . $field;
            }, $fields);

            $this->associations[$joinModelClassName] = $joinItem;
        }
    }

    /**
     * 根据别名获取关联对象
     *
     * @param $needle
     * @return JoinItem|null
     */
    private function getJoinItem($needle)
    {
        if ($needle == null) return null;

        foreach ($this->associations as $JoinItem) {
            if ($JoinItem->model == $this->getJoinModelClassName($needle)) {
                return $JoinItem;
            }
        }

        return null;
    }

    /**
     * 获取模型名
     *
     * @param $needle
     * @return mixed
     */
    private function getJoinModelClassName($needle)
    {
        $methodName = 'getJoinModels';
        if ($this->getModel() instanceof BaseModel || method_exists($this->getModel(), $methodName)) {
            $joinModels = call_user_func([$this->getModel(), $methodName]);

            $aliases = array_keys($joinModels);

            // 在别名-模型对象关系映射中
            if (in_array($needle, $aliases)) {
                return $joinModels[$needle];
            }

            throw new \RuntimeException('连接操作无效, 请检查类 ' . get_class($this->getModel()) . ' joinModels数组配置'
                . $needle);
        }

        throw new \RuntimeException('类异常： ' . get_class($this->getModel()) . '不是' . BaseModel::class . '的实例 '
            ." 或没有 {$methodName} 方法");
    }

    /**
     * @return array
     */
    public function getAssociations()
    {
        return $this->associations;
    }


    /**
     * @param array $associations
     */
    public function setAssociations(array $associations)
    {
        $this->associations = $associations;
    }
}
