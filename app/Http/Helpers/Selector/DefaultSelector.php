<?php

/**
 * Created by PhpStorm.
 * @description: 用于select查询的默认对象
 * @author: FS
 * @date: 2018-04-10 12:26
 */

namespace App\Http\Helpers\Selector;

use App\Http\Helpers\Selector\Builder\Builder;
use App\Http\Helpers\Selector\Parser\Parser;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DefaultSelector implements Selector
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @var Collection
     */
    private $queries;

    /**
     * @var Collection
     */
    private $pathVariables;

    /**
     * @var array
     */
    private $builders = [];

    /**
     * @var array
     */
    private $parsers = [];

    /**
     * Select constructor.
     * @param Request $request
     * @param Model $model
     */
    public function __construct(Request $request, Model $model)
    {
        $this->setModel($model);
        $this->setQueries($request->all());
        $this->setPathVariables($request->route()->parameters());
    }


    /**
     * @param Parser $parser
     * @param Builder $builder
     */
    public function registerQueryProcessor(Parser $parser, Builder $builder)
    {
        $parser->setModel($this->getModel());

        $parser->setQueries($this->getQueries());

        $builder->setParser($parser);

        $this->registerProcessor($parser, $builder);
    }

    /**
     * @param Parser $parser
     * @param Builder $builder
     */
    public function registerPathVariableProcessor(Parser $parser, Builder $builder)
    {
        $parser->setModel($this->getModel());

        $parser->setQueries($this->getPathVariables());

        $builder->setParser($parser);

        $this->registerProcessor($parser, $builder);
    }

    /**
     * @param Parser $parser
     * @param Builder $builder
     */
    public function registerProcessor(Parser $parser, Builder $builder)
    {
        $this->parsers[class_basename($parser)] = $parser;

        $this->builders[class_basename($builder)] = $builder;
    }

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder()
    {
        $queryBuilder = $this->getQueryBuilder();

        $this->modifyQueryBuilder($queryBuilder);

        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function modifyQueryBuilder(QueryBuilder $queryBuilder)
    {
        if (empty($this->builders)) {
            return;
        }

        foreach ($this->builders as $builder) {
            $builder->parse();
            $builder->build($this->model, $queryBuilder);
        }
    }

    /**
     * @return QueryBuilder
     */
    private function getQueryBuilder(): QueryBuilder
    {
        return $this->getModel()::query()->select($this->getModel()->getTable() . '.*');
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Model $model
     */
    public function setModel(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param $name
     * @param $value
     */
    public function getQuery($name, $value)
    {
        $this->queries->get($name, $value);
    }

    /**
     * @param $name
     * @param $value
     */
    public function putQuery($name, $value)
    {
        $this->queries->put($name, $value);
    }

    /**
     * @param array $queries
     */
    public function setQueries(array $queries)
    {
        $this->queries = collect(array_change_key_case($queries, CASE_LOWER));
    }

    /**
     * @return Collection
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * @return Collection
     */
    public function getPathVariables()
    {
        return $this->pathVariables;
    }

    /**
     * @param array $pathVariables
     */
    public function setPathVariables(array $pathVariables)
    {
        $this->pathVariables = collect($pathVariables);
    }


    /**
     * @return array
     */
    public function getBuilders()
    {
        return $this->builders;
    }

    /**
     * @return array
     */
    public function getParsers()
    {
        return $this->parsers;
    }


    /**
     * @param $class
     * @return Parser|mixed|null
     */
    public function getParser($class)
    {
        $builderName = class_basename($class);

        if (in_array($builderName, array_keys($this->parsers))) {
            return $this->parsers[$builderName];
        }

        return null;
    }


    /**
     * @param $class
     * @return Builder|mixed|null
     */
    public function getBuilder($class)
    {
        $builderName = class_basename($class);

        if (in_array($builderName, array_keys($this->builders))) {
            return $this->builders[$builderName];
        }

        return null;
    }
}
