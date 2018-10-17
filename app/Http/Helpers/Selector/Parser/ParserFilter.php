<?php
/**
 * Created by PhpStorm.
 * @description: 解析过滤器
 * @author: FS
 * @date: 2018-04-12 10:53
 */

namespace App\Http\Helpers\Selector\Parser;

trait ParserFilter
{
    /**
     * 过滤条件，去除不合法的查询字段以及空值
     *
     * @param array $data
     * @param string $delimiter
     * @param int $min
     * @param null $max
     * @return array
     */
    protected function filterQueries($data, $delimiter = null, $min = null, $max = null)
    {
        if (empty($data)) {
            return [];
        }

        $keyValues = [];

        foreach ($data as $name => $value) {
            if (is_null($value) || ($value == "" && $value !== 0) ) {
                continue;
            }

            if (!is_string($name) || is_numeric($name) || !is_string($value)) {
                throw new \RuntimeException('查询的字段 ' . $name . ' 格式错误');
            }

            // 分隔符为不空，则进行字符串分割，填充的值也是分割后的数组
            if ($delimiter != "") {

                // 如果设置了最大分割数，则用最大分割数分割
                $values = $max === null ? explode($delimiter, $value) : explode($delimiter, $value, $max);

                // 如果设置了最小分割数，则检查是否满足此条件
                if ($min !== null && count($values) < $min) {
                    throw new \RuntimeException('查询的字段 ' . $name . ' 的值格式错误');
                }

                $value = $values;
            }
            $keyValues[trim($name, '\'')] = $this->filterBlankString($value);
        }

        return $keyValues;
    }

    /**
     * 过滤空格
     *
     * @param $value
     * @return array|string
     */
    public function filterBlankString($value) {
        if (is_string($value)) return trim($value);

        if (is_array($value)) {
            return array_map([$this, 'filterBlankString'], $value);
        }

        return $value;
    }
}
