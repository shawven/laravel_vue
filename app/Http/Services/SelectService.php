<?php
/**
 * Created by PhpStorm.
 * @description: 用于选择查询的服务层
 * @author: VAIO
 * @date: 2018-04-08 21:52
 */

namespace App\Http\Services;

use App\Http\Helpers\Selector\Decorator\Decorator;
use App\Http\Helpers\Selector\Page;
use App\Http\Helpers\Selector\PageInfoWrapper;
use App\Http\Helpers\Selector\Selector;
use Illuminate\Database\Eloquent\Model;

class SelectService
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @var Selector
     */
    private $selector;

    /**
     * @var array
     */
    private $decorators = [];

    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    private $queryBuilder;

    /**
     * SelectService constructor.
     * @param Selector $selector
     */
    public function __construct(Selector $selector)
    {
        $this->selector = $selector;
        $this->model = $selector->getModel();
        $this->queryBuilder = $this->selector->createQueryBuilder();
    }

    /**
     * 根据select条件查询数据
     *
     * @return Page
     */
    public function selectPageList()
    {
        $list = $this->selectList();

        $total = $this->countTotal();

        return $this->wrapPageInfo($list, $total);
    }


    /**
     * @return array
     */
    public function selectList()
    {
        $list = $this->queryBuilder->get()->all();

        $this->applyDecorators($list);

        return $list;
    }


    /**
     * @param $list
     * @param $total
     * @return Page
     */
    public function wrapPageInfo($list, $total)
    {
        $data = ['list' => $list, 'total'=> $total];

        return (new PageInfoWrapper())->wrap($data, $this->selector);
    }

    /**
     * @return int
     */
    public function countTotal()
    {
        return  $this->queryBuilder->offset(0)->limit(1)->count();
    }

    /**
     * @param array $collection
     */
    public function applyDecorators(array &$collection)
    {
        if (empty($this->getDecorators())) return;

        foreach ($this->getDecorators() as $decorator) {
            $decorator->decorate($collection, $this->selector);
        }
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
     * @return Selector
     */
    public function getSelector()
    {
        return $this->selector;
    }

    /**
     * @param Selector $selector
     */
    public function setSelector(Selector $selector)
    {
        $this->selector = $selector;
    }

    /**
     * @return array
     */
    public function getDecorators()
    {
        return $this->decorators;
    }

    /**
     * @param Decorator $decorator
     */
    public function setDecorator(Decorator $decorator)
    {
        $this->decorators[class_basename($decorator)] = $decorator;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getQuery()
    {
        return $this->queryBuilder;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $queryBuilder
     */
    public function setQueryBuilder(\Illuminate\Database\Eloquent\Builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
}
