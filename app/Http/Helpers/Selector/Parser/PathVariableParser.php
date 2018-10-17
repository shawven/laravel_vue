<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-05-04 16:38
 */

namespace App\Http\Helpers\Selector\Parser;

use App\Http\Helpers\Selector\Config\Common;
use App\Http\Helpers\Selector\Config\QueryParam;

class PathVariableParser extends BaseParser
{
    /**
     * @var array
     */
    private $pathVariables = [];

    public function Parse()
    {
        $this->resolvePathVariable();
    }

    private function resolvePathVariable() {
        if ($this->getQueries()->isNotEmpty()) {
            $last = array_slice($this->getQueries()->all(), -1);
            $this->pathVariables[key($last)] = current($last);
        }
    }

    /**
     * @return array
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
        $this->pathVariables = $pathVariables;
    }
}
