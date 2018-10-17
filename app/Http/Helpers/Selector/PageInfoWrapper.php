<?php
/**
 * Created by PhpStorm.
 * @description: 分页信息包装器
 * @author: FS
 * @date: 2018-04-13 14:46
 */

namespace App\Http\Helpers\Selector;

use App\Http\Helpers\Selector\Parser\PageParser;

class PageInfoWrapper
{

    /**
     * 包装数据
     *
     * @param array $collection
     * @param Selector $selector
     * @return Page $page
     */
    public function wrap(array $collection, Selector $selector)
    {
        $page = $this->getPageParser($selector)->getPage();

        return $this->wrapData($collection, $page);
    }

    /**
     * 包装数据
     *
     * @param array $array
     * @param Page $page
     * @return \App\Http\Helpers\Selector\Page
     */
    public function wrapData(array $array, Page $page)
    {
        $page->setTotal($array['total']);

        $page->setList($array['list']);

        return $page;
    }

    /**
     * @param Selector $selector
     * @return PageParser
     */
    private function getPageParser(Selector $selector)
    {
        $pageParser = $selector->getParser(PageParser::class);

        if ($pageParser != null && $pageParser instanceof PageParser) {
            return $pageParser;
        }

        throw new \RuntimeException('找不到' . PageParser::class . '解析器');
    }
}
