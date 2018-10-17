<?php
/**
 * Created by PhpStorm.
 * @description: 分页条件构建器
 * @author: FS
 * @date: 2018-04-12 15:49
 */

namespace App\Http\Helpers\Selector\Builder;

use App\Http\Helpers\Selector\Parser\PageParser;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

class PageBuilder extends BaseBuilder
{
    /**
     * @var PageParser
     */
    protected $parser;

    /**
     * @param Model $model
     * @param QueryBuilder $queryBuilder
     */
    public function build(Model $model, QueryBuilder $queryBuilder)
    {
        $this->model = $model;

        $this->setPaginate($queryBuilder);
    }

    /**
     * @param $queryBuilder
     */
    private function setPaginate(QueryBuilder $queryBuilder)
    {
        $page = $this->parser->getPage();

        if (!$page->isValid()) return;

        if ($page->getOffset() > 0) {
            $queryBuilder->offset($page->getOffset());
        }

        if ($page->getLimit() > 0) {
            $queryBuilder->limit($page->getLimit());
        }
    }
}
