<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-05-16 15:59
 */

namespace App\Http\Controllers\Base;

trait Transferable
{
    /**
     * @param array $list
     * @return array
     */
    protected function transform(array $list)
    {
        return $list;
    }
}
