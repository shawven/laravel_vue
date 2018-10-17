<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-05-04 17:00
 */

namespace App\Http\Helpers\Selector\Builder;

use App\Http\Helpers\Selector\Parser\PathVariableParser;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

class PathVariableBuilder extends BaseBuilder
{

    /**
     * @var PathVariableParser
     */
    protected $parser;

    public function build(Model $model, QueryBuilder $builder)
    {
        $this->model = $model;
        $this->setCondition($builder);
    }

    /**
     * @param $queryBuilder
     */
    private function setCondition(QueryBuilder $queryBuilder)
    {
        if (empty($this->parser->getPathVariables())) return;
        foreach ($this->parser->getPathVariables() as $name => $value) {
            $queryBuilder->where($this->getModel()->getTable() . '.' . $name, $value);
        }
    }
}
