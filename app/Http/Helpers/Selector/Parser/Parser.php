<?php
/**
 * Created by PhpStorm.
 * @description: 解析器
 * @author: FS
 * @date: 2018-04-12 13:54
 */

namespace App\Http\Helpers\Selector\Parser;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface Parser
{
    /**
     * @param Collection $request
     */
    public function setQueries(Collection $request);

    /**
     * @param Model $model
     */
    public function setModel(Model $model);

    /**
     *
     */
    public function Parse();
}
