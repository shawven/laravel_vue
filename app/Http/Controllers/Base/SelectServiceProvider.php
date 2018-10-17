<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-05-02 11:31
 */

namespace App\Http\Controllers\Base;

use App\Http\Helpers\Selector\Builder\AssociationBuilder;
use App\Http\Helpers\Selector\Builder\CommonBuilder;
use App\Http\Helpers\Selector\Builder\PageBuilder;
use App\Http\Helpers\Selector\Builder\PathVariableBuilder;
use App\Http\Helpers\Selector\Decorator\AssociationDecorate;
use App\Http\Helpers\Selector\DefaultSelector;
use App\Http\Helpers\Selector\Parser\AssociationParser;
use App\Http\Helpers\Selector\Parser\CommonParser;
use App\Http\Helpers\Selector\Parser\PageParser;
use App\Http\Helpers\Selector\Parser\PathVariableParser;
use App\Http\Services\SelectService;
use Illuminate\Http\Request;

trait SelectServiceProvider
{

    use ModelExtractor;

    /**
     * @param Request $request
     * @return SelectService
     */
    public function getSelectService(Request $request) {
        $selector = new DefaultSelector($request, $this->getModel());

        $selector->registerQueryProcessor(new PageParser(), new PageBuilder());
        $selector->registerQueryProcessor(new CommonParser(), new CommonBuilder());
        $selector->registerQueryProcessor(new AssociationParser(), new AssociationBuilder());
        $selector->registerPathVariableProcessor(new PathVariableParser(), new PathVariableBuilder());

        $selectService = new SelectService($selector);
        $selectService->setDecorator(new AssociationDecorate());

        return $selectService;
    }
}
