<?php
/**
 * Created by PhpStorm.
 * @description: 解析器基类
 * @author: FS
 * @date: 2018-04-12 14:05
 */

namespace App\Http\Helpers\Selector\Parser;

use App\Http\Helpers\Selector\Config\Common;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class BaseParser implements Parser
{
    use ParserFilter;

    const DELIMITER = Common::DEFAULT_DELIMITER;

    /**
     * @var Collection
     */
    protected $queries;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @return Collection
     */
    public function getQueries()
    {
        return $this->queries;
    }

    /**
     * @param Collection $request
     */
    public function setQueries(Collection $request)
    {
        $this->queries = $request;
    }

    public function setModel(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }
}
