<?php
/**
 * Created by PhpStorm.
 * @description: 普通解析器
 * @author: FS
 * @date: 2018-04-12 13:32
 */

namespace App\Http\Helpers\Selector\Parser;

use App\Http\Helpers\Selector\Config\QueryParam;
use App\Http\Helpers\Selector\Config\Range;

class CommonParser extends BaseParser
{
    /**
     * @var array
     */
    private $choices = [];

    /**
     * @var array
     */
    private $likes = [];

    /**
     * @var array
     */
    private $sorts = [];

    /**
     * @var array
     */
    private $ranges = [];

    /**
     * @var array
     */
    private $equals = [];

    /**
     * 解析当前实体类的条件
     */
    public function Parse()
    {
        $this->resolveChoices();
        $this->resolveLikes();
        $this->resolveSorts();
        $this->resolveRanges();
        $this->resolveEquals();
    }

    /**
     * 初始化多选值
     */
    private function resolveChoices()
    {
        if ($this->getQueries()->has(QueryParam::IN)) {
            $array = (array)$this->getQueries()->pull(QueryParam::IN);
            $this->choices = $this->filterQueries($array, static::DELIMITER, 1);
        }
    }

    /**
     * 初始化模糊查询
     */
    private function resolveLikes()
    {
        if ($this->getQueries()->has(QueryParam::LIKE)) {
            $likes = $this->filterQueries((array)$this->getQueries()->pull(QueryParam::LIKE));
            foreach ($likes as $field => $value) {
                $this->likes[$field] = $value;
            }
        }
    }

    /**
     * 初始化排序
     */
    private function resolveSorts()
    {
        if ($this->getQueries()->has(QueryParam::SORT)) {
            $sorts = $this->filterQueries((array)$this->getQueries()->pull(QueryParam::SORT));
            foreach ($sorts as $field => $value) {
                $this->sorts[$field] = empty($value) ? "DESC" : (strpos(strtoupper($value), 'A') !== false ? 'ASC' : 'DESC');
            }
        }
    }

    /**
     * 初始化字段范围
     */
    private function resolveRanges()
    {
        if ($this->getQueries()->has(QueryParam::RANGE)) {
            $array = (array)$this->getQueries()->pull(QueryParam::RANGE);
            $ranges = $this->filterQueries($array, static::DELIMITER, 1, 2);
            foreach ($ranges as $field => $values) {
                if (count($values) == 1) {
                    $hasMin = strpos($values[0], Range::PREFIX) === 0;
                    $hasMax = stripos($values[0], Range::SUFFIX, strlen($values[0]) - 1) === strlen($values[0]) - 1;
                    if ($hasMin) {
                        $this->ranges[$field][0] = ltrim($values[0], Range::PREFIX);
                    } else if ($hasMax) {
                        $this->ranges[$field][1] = rtrim($values[0], Range::SUFFIX);
                    }
                }
                if (count($values) == 2) {
                    $this->ranges[$field][0] = ltrim($values[0], Range::PREFIX);
                    $this->ranges[$field][1] = rtrim($values[1], Range::SUFFIX);
                }
            }
        }
    }

    /**
     * 初始化 model的属性 where相等的条件
     */
    private function resolveEquals()
    {
        if ($this->getQueries()->isNotEmpty()) {

            try {
                $reflectionClass = new \ReflectionClass(QueryParam::class);
            } catch (\ReflectionException $e) {
                throw new \RuntimeException('获取类 '. QueryParam::class . ' 常量失败');
            }
            $constants = $reflectionClass->getConstants();

            // 排除值为数组和预定义的查询参数（如模糊查询、排序、范围）后剩下即是模型的属性等值条件
            $items = $this->getQueries()->filter(function ($item, $key) use ($constants) {
                return !is_array($item) && !in_array($key, array_values($constants));
            })->all();

            $fields = $this->filterQueries((array)$items);
            foreach ($fields as $name => $value) {
                $this->equals[$name] = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param array $choices
     */
    public function setChoices(array $choices)
    {
        $this->choices = $choices;
    }

    /**
     * @return array
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * @param array $likes
     */
    public function setLikes(array $likes)
    {
        $this->likes = $likes;
    }

    /**
     * @return array
     */
    public function getSorts()
    {
        return $this->sorts;
    }

    /**
     * @param array $sorts
     */
    public function setSorts(array $sorts)
    {
        $this->sorts = $sorts;
    }

    /**
     * @return array
     */
    public function getRanges()
    {
        return $this->ranges;
    }

    /**
     * @param array $ranges
     */
    public function setRanges(array $ranges)
    {
        $this->ranges = $ranges;
    }

    /**
     * @return array
     */
    public function getEquals()
    {
        return $this->equals;
    }


    /**
     * @param array $equals
     */
    public function setEquals(array $equals)
    {
        $this->equals = $equals;
    }

}
