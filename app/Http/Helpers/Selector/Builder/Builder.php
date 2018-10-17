<?php
/**
 * Created by PhpStorm.
 * @description: 查询构造器
 * @author: FS
 * @date: 2018-04-12 14:44
 */

namespace App\Http\Helpers\Selector\Builder;

use App\Http\Helpers\Selector\Parser\Parser;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

interface Builder
{
    /**
     *
     */
    public function parse();

    /**
     * @param Model $model
     * @param QueryBuilder $builder
     */
    public function build(Model $model, QueryBuilder $builder);

    /**
     * @return Model
     */
    public function getModel();

    /**
     * @param Model $model
     */
    public function setModel(Model $model);

    /**
     * @return Parser
     */
    public function getParser();

    /**
     * @param Parser $parser
     */
    public function setParser(Parser $parser);

}
