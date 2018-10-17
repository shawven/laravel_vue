<?php
/**
 * Created by PhpStorm.
 * @description: 分页解析器
 * @author: FS
 * @date: 2018-04-12 14:15
 */

namespace App\Http\Helpers\Selector\Parser;

use App\Http\Helpers\Selector\Config\QueryParam;
use App\Http\Helpers\Selector\Page;

class PageParser extends BaseParser
{
    const DEFAULT_DELIMITER = ',';

    /**
     * @var Page
     */
    private $page;

    /**
     * 解析分页数据
     */
    public function Parse()
    {
        $page = intval($this->getQueries()->pull(QueryParam::PAGE));
        $limit = intval($this->getQueries()->pull(QueryParam::LIMIT));
        $this->page = new Page($page, $limit);
    }


    /**
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }


    /**
     * @param Page $page
     */
    public function setPage(Page $page)
    {
        $this->page = $page;
    }


}
