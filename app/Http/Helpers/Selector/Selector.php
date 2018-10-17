<?php
/**
 * Created by PhpStorm.
 * @description: 选择器
 * @author: FS
 * @date: 2018-04-12 16:04
 */

namespace App\Http\Helpers\Selector;

use App\Http\Helpers\Selector\Builder\Builder;
use App\Http\Helpers\Selector\Parser\Parser;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface Selector
{
    /**
     * @param Parser $parser
     * @param Builder $builder
     */
    public function registerProcessor(Parser $parser, Builder $builder);

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder();

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function modifyQueryBuilder(QueryBuilder $queryBuilder);

    /**
     * @return Model
     */
    public function getModel();

    /**
     * @param Model $model
     */
    public function setModel(Model $model);

    /**
     * @return Collection
     */
    public function getQueries();

    /**
     * @param array $request
     */
    public function setQueries(array $request);

    /**
     * @return array
     */
    public function getBuilders();

    /**
     * @return array
     */
    public function getParsers();

    /**
     * @param $class
     * @return Parser
     */
    public function getParser($class);

    /**
     * @param $class
     * @return Builder
     */
    public function getBuilder($class);
}
