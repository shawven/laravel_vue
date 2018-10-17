<?php
/**
 * Created by PhpStorm.
 * @description: 构造器基类
 * @author: FS
 * @date: 2018-04-12 18:43
 */

namespace App\Http\Helpers\Selector\Builder;

use App\Http\Helpers\Selector\Parser\Parser;
use Illuminate\Database\Eloquent\Model;

abstract class BaseBuilder implements Builder
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var Model
     */
    protected $model;

    /**
     *@return void
     */
    public function parse()
    {
        $this->parser->Parse();
    }

    /**
     * @return Parser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * @param Parser $parser
     */
    public function setParser(Parser $parser)
    {
        $this->parser = $parser;
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
}
