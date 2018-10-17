<?php
/**
 * Created by PhpStorm.
 * @description: 装饰器
 * @author: FS
 * @date: 2018-04-13 13:44
 */

namespace App\Http\Helpers\Selector\Decorator;

use App\Http\Helpers\Selector\Selector;

interface Decorator
{
    /**
     * @param array $collection
     * @param Selector $selector
     */
    public function decorate(array &$collection, Selector $selector);
}
