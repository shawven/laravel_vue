<?php
/**
 * Created by PhpStorm.
 * @description: 查询分页对象
 * @author: VAIO
 * @date: 2018-04-08 22:43
 */

namespace App\Http\Helpers\Selector;


class Page
{
    const DEFAULT_PAGE = 1;
    const DEFAULT_LIMIT = 10;

    private $page;

    private $limit;

    private $list;

    private $rows;

    private $total;

    /**
     * Page constructor.
     * @param $page
     * @param $limit
     */
    public function __construct($page = self::DEFAULT_PAGE, $limit = self::DEFAULT_LIMIT)
    {
        $this->page = $page ?? self::DEFAULT_PAGE;
        $this->limit = $limit ?? self::DEFAULT_LIMIT;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return !($this->getOffset() === 0 && $this->getLimit() === 0);
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return float|int
     */
    public function getOffset()
    {
        return ($this->getPage() - 1) * $this->getLimit();
    }

    /**
     * @return array
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param array $list
     */
    public function setList(array $list)
    {
        $this->list = $list;
    }

    /**
     * @return int
     */
    public function getRows()
    {
        return $this->rows = $this->rows ?: count($this->list);
    }

    /**
     * @param int $rows
     */
    public function setRows($rows)
    {
        $this->rows = $rows;
    }



    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'page' => $this->getPage(),
            'limit' => $this->getLimit(),
            'rows' => $this->getRows(),
            'total' => $this->getTotal(),
            'list'=> $this->getList()
        ];
    }
}
