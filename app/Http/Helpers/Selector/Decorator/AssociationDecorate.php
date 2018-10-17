<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-05-20 2:24
 */

namespace App\Http\Helpers\Selector\Decorator;

use App\Http\Helpers\Selector\JoinItem;
use App\Http\Helpers\Selector\Parser\AssociationParser;
use App\Http\Helpers\Selector\Selector;
use Illuminate\Database\Eloquent\Model;

class AssociationDecorate implements Decorator
{
    /**
     * @var array
     */
    private $collection = [];

    /**
     * @var Selector
     */
    private $selector = [];

    /**
     * @param array $collection
     * @param Selector $selector
     */
    public function decorate(array &$collection, Selector $selector)
    {
        if (empty($collection)) return;

        $this->collection = &$collection;

        $this->selector = $selector;

        $this->separateAssociation();
    }

    /**
     * 把关联表的所有字段用子节点区分开来
     */
    private function separateAssociation()
    {
        $associations = $this->getAssociationParser()->getAssociations();

        if (empty($associations)) return;

        foreach ($associations as $joinItem) {
            $this->separate($joinItem);
        }
    }

    /**
     * @param JoinItem $joinItem
     */
    private function separate($joinItem)
    {
        if (is_null($joinItem->name) || empty($joinItem->fields)) return;

        array_walk($this->collection, function (&$item) use ($joinItem){
            /**@var Model $item*/
            $joinModel = new $joinItem->model;
            $newModel = $this->selector->getModel()->newInstance();

            foreach ($item->toArray() as $key => $val) {
                if (in_array($key, $joinItem->fields)) {
                    $joinField = str_replace($joinItem->name . '_', '', $key);
                    $joinModel->{$joinField} = $val;
                } else {
                    $newModel->{$key} = $val;
                }
            }

            $joinModel->exists = true;
            $newModel->{$joinItem->name} = $joinModel;
            $newModel->exists = true;
            $item = $newModel;
        });
    }

    /**
     * @return \App\Http\Helpers\Selector\Parser\AssociationParser
     */
    private function getAssociationParser()
    {
        $associateParser = $this->selector->getParser(AssociationParser::class);

        if ($associateParser != null && $associateParser instanceof AssociationParser) {
            return $associateParser;
        }

        throw new \RuntimeException('找不到' . AssociationParser::class . '解析器');
    }
}
