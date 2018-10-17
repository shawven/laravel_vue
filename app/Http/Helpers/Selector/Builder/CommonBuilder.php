<?php
/**
 * Created by PhpStorm.
 * @description: 普通条件构建器（针对当前资源实体类）
 * @author: FS
 * @date: 2018-04-12 14:43
 */

namespace App\Http\Helpers\Selector\Builder;

use App\Http\Helpers\Selector\Parser\CommonParser;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

class CommonBuilder extends BaseBuilder
{
    /**
     * @var CommonParser
     */
    protected $parser;

    /**
     * @var string
     */
    private $table;

    /**
     * @param Model $model
     * @param QueryBuilder $queryBuilder
     */
    public function build(Model $model, QueryBuilder $queryBuilder)
    {
        $this->model = $model;

        $this->table = $this->getModel()->getTable();

        // 设置当前对象的where条件
        $this->setCondition($queryBuilder);

        // 设置当前对象的in条件
        $this->setInCondition($queryBuilder);

        // 设置当前对象的like条件
        $this->setLikeCondition($queryBuilder);

        // 设置当前对象的范围条件
        $this->setRangeCondition($queryBuilder);

        // 设置当前对象的排序条件
        $this->setSortCondition($queryBuilder);
    }

    /**
     * @param $queryBuilder
     */
    private function setCondition(QueryBuilder $queryBuilder)
    {
        $fields = $this->parser->getEquals();

        if (empty($fields)) return;

        foreach ($fields as $filed => $filedValue) {
            $queryBuilder->where($this->table . '.' . $filed, $filedValue);
        }
    }




    /**
     * @param QueryBuilder $queryBuilder
     */
    private function setInCondition(QueryBuilder $queryBuilder)
    {
        $choices = $this->parser->getChoices();

        if (empty($choices)) return;

        foreach ($choices as $choice => $choiceValues) {
            $queryBuilder->whereIn($this->table . '.' . $choice, $choiceValues);
        }
    }

    /**
     * @param $queryBuilder
     */
    private function setLikeCondition(QueryBuilder $queryBuilder)
    {
        $likes = $this->parser->getLikes();

        if (empty($likes)) return;

        foreach ($likes as $like => $likeValue) {
            $queryBuilder->where($this->table . '.' . $like, 'like', '%' . $likeValue . '%');
        }
    }

    /**
     * @param $queryBuilder
     */
    private function setRangeCondition(QueryBuilder $queryBuilder)
    {
        $ranges = $this->parser->getRanges();

        if (empty($ranges)) return;

        foreach ($ranges as $range => $rangeValues) {
            if (isset($rangeValues[0])) {
                if (isset($rangeValues[1])) {
                    $queryBuilder->whereBetween($this->table . '.' . $range, [$rangeValues[0], $rangeValues[1]]);
                } else {
                    $queryBuilder->where($this->table . '.' . $range, '>=', $rangeValues[0]);
                }
            } else if (isset($rangeValues[1])) {
                $queryBuilder->where($this->table . '.' . $range, '<=', $rangeValues[1]);
            }
        }
    }

    /**
     * @param $queryBuilder
     */
    private function setSortCondition(QueryBuilder $queryBuilder)
    {
        $sorts = $this->parser->getSorts();

        if (empty($sorts)) return;

        foreach ($sorts as $sort => $sortValue) {
            $queryBuilder->orderBy($this->table . '.' . $sort, $sortValue);
        }
    }

}
