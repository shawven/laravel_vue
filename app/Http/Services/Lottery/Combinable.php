<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-06-28 11:49
 */
namespace App\Http\Services\Lottery;

use InvalidArgumentException;
use RuntimeException;

trait Combinable
{
    /**
     * @var array 过关级别
     */
    public static $passages = ['单场', '双关', '三关', '四关', '五关', '六关', '七关', '八关'];

    /**
     * @var array 所有的玩法，分组区分，每组的数字对应过关级别的索引，表示该组的玩法起始过关
     * 例： 0组的玩法从单场开始 1组的玩法从双关开始
     */
    public static $plays = [0 => ['1*1', '2*1', '3*1', '4*1', '5*1', '6*1', '7*1', '8*1', '2*3', '3*6', '3*7', '4*10', '4*14', '4*15', '5*15', '5*25', '5*30', '5*31', '6*21', '6*41', '6*56', '6*62', '6*63', '7*127', '8*255',], 1 => ['3*3', '3*4', '4*6', '4*11', '5*10', '5*20', '5*26', '6*15', '6*35', '6*50', '6*57', '7*120', '8*247',], 2 => ['4*4', '4*5', '5*16', '6*20', '6*42',], 3 => ['5*5', '5*6', '6*22', '7*35', '8*70',], 4 => ['6*6', '6*7', '7*21', '8*56',], 5 => ['7*7', '7*8', '8*28',], 6 => ['8*8', '8*9']];

    /**
     * @var array 赛事对应玩法的所有组合数
     */
    public static $combinationsCount;

    /**
     * 根据玩法的所有子集逆向寻找玩法名称
     *
     * @param int $matchesCount
     * @param array $plays
     * @return null
     */
    public function getPlayByCombinationList($matchesCount, array $plays)
    {
        if (!isset($this->getCombinationsCount()[$matchesCount])) {
            throw new InvalidArgumentException("暂无此赛事场数 {$matchesCount} 的玩法");
        }

        // 根据赛事场数获取可能的每关组合数的集合
        $combinationCountItems = $this->getCombinationsCount()[$matchesCount];

        foreach ($combinationCountItems as $index => $combinationCountItem) {
            $existence = true;
            $lastValidIndex = 0;

            foreach ($combinationCountItem as $passageIndex => $number) {
                if ($number <= 0) {
                    continue;
                }

                $play = $number . '*' . ($passageIndex + 1);

                // 当前组合数集合有一项不满足既排除
                if (!in_array($play, $plays)) {
                    $existence = false;
                    break;
                }

                $lastValidIndex = $passageIndex;
            }

            // 未出现不满足的情况
            if ($existence == true && $lastValidIndex + 1 == $matchesCount) {
                return $this->joinPlay($matchesCount, $index);
            }
        }

        throw new RuntimeException("未找到对应的玩法" . PHP_EOL . print_r($plays, true));
    }

    /**
     * 获取赛事对应玩法的所有组合数
     *
     * @return array
     */
    public function getCombinationsCount()
    {
        if (empty(static::$combinationsCount)) {
            foreach (static::$plays as $playItems) {
                foreach ($playItems as $item) {
                    [$n, $m] = $this->splitPlay($item);
                    static::$combinationsCount[$n][$m] = $this->getMixedPassageCombinationsCount($item);
                }
            }
        }

        return static::$combinationsCount;
    }

    /**
     * 根据赛事和玩法获取赛事组合
     *
     * @param array $items
     * @param $play
     * @return array
     */
    public function getCombinationList(array $items, $play)
    {
        [$itemsCount, $bunches] = $this->splitPlay($play);

        if ($itemsCount != count($items)) {
            throw new InvalidArgumentException("赛事数量不匹配，不适用于此组合的 {$play} 玩法");
        }

        if (!isset($this->getCombinationsCount()[$itemsCount][$bunches])) {
            throw new InvalidArgumentException("暂无此 {$play} 玩法");
        }

        // 根据赛事场数和总组合数获取每关组合数
        $combinationCountItems = $this->getCombinationsCount()[$itemsCount][$bunches];

        $list = [];
        $count = 0;

        foreach ($combinationCountItems as $passageIndex => $combinatorialNumber) {
            // 排除组合数为 0
            if ($combinatorialNumber <= 0) {
                continue;
            }

            $n = $itemsCount;
            $m = $passageIndex + 1;

            // 获取组合集，N种结果选M种可能的集合
            $this->buildCombinations($items, $n, $m, function ($items) use (&$list, &$count) {
                sort($items);

                // 假如组合集是二维数组（元素也是数组），则取集合元素的笛卡尔积
                $pushList = $this->acrossCombinations($items);

                if (is_array(current($pushList))) {
                    array_push($list, ...$pushList);
                } else {
                    $list[] = $pushList;
                }

                $count ++;
            });
        }

        if ($count != array_sum($combinationCountItems)) {
            throw new RuntimeException("获取组合异常，期待组合总数：" . array_sum($combinationCountItems) . "，实际：{$count}");
        }

        return $list;
    }

    /**
     * 获取混合过关的组合数
     *
     * @param string $play 玩法
     * @param null|int $start 起始过关
     * @return array
     */
    public function getMixedPassageCombinationsCount($play, $start = null)
    {
        [$matchesCount, $bunches] = $this->splitPlay($play);

        $i = $start ?? $this->getStarter($play);
        $finder = array_fill(0, 8, 0);

        while (true) {
            if ($bunches == 1) {
                $finder[$matchesCount - 1] = 1;
                break;
            }

            $nowNumber = $this->C($matchesCount, $i + 1);

            $finder[$i] = $nowNumber;

            $currentTotal = array_sum($finder);

            if ($bunches == $currentTotal) {
                break;
            }

            if ($bunches == $currentTotal + 1) {
                $finder[$i + 1] = 1;
                break;
            }

            $i++;
        }

        return $finder;
    }

    /**
     * 获取可组合的集合，从N种结果选M种可能的组合
     *
     * @param $arr
     * @param int $n n
     * @param int $m
     * @param callable $collector
     * @param array $list
     */
    public function buildCombinations($arr, $n, $m, callable $collector, &$list = [])
    {
        for ($i = $n; $i >= $m; $i--) {

            $list[$m - 1] = $arr[$i - 1];

            if ($m > 1) {
                $this->buildCombinations($arr, $i - 1, $m - 1, $collector, $list);
            } else {
                $collector($list);
            }
        }
    }

    /**
     * 取组合集和的笛卡尔积
     *
     * @param $items
     * @return array
     */
    private function acrossCombinations($items)
    {
        $num = 0;
        $arrays = array_map(function ($item) use (&$num) {
            if (is_array($item)) {
                $num++;
                return $item;
            } else {
                return [$item];
            }
        }, $items);

        if ($num == 0) {
            return $items;
        }

        return $this->crossJoin($arrays);
    }


    /**
     * 交叉连接（笛卡尔积）
     *
     * @param array ...$arrays
     * @return array
     */
    public function crossJoin(...$arrays)
    {
        $results = [[]];

        foreach ($arrays as $index => $array) {
            $append = [];

            foreach ($results as $product) {
                foreach ($array as $item) {
                    $product[$index] = $item;

                    $append[] = $product;
                }
            }

            $results = $append;
        }

        return $results;
    }

    /**
     * @param $play
     * @return array
     */
    private function splitPlay($play)
    {
        return array_map('intval', explode('*', $play));
    }

    /**
     * @param $n
     * @param $m
     * @return string
     */
    private function joinPlay($n, $m)
    {
        return $n . '*' . $m;
    }

    /**
     * 获取起点
     *
     * @param $play
     * @return int|null
     */
    private function getStarter($play)
    {
        foreach (static::$plays as $index => $playItems) {
            if (false !== array_search($play, $playItems)) {
                return intval($index);
            }
        }

        return null;
    }

    /**
     * 求组合数
     *
     * @param $n
     * @param $m
     * @return int
     */
    public function C($n, $m)
    {
        if ($n == $m) {
            return 1;
        }

        if ($m == 1 || ($n > $m && $m > 0 && $n - $m == 1)) {
            return $n;
        }

        return $this->getFactorial($n) / ($this->getFactorial($m) * $this->getFactorial($n - $m));
    }

    /**
     * 获取阶乘
     *
     * @param $n
     * @return int
     */
    public function getFactorial($n)
    {
        if ($n == 0) {
            return 0;
        }

        if ($n == 1) {
            return 1;
        }

        return $n * $this->getFactorial($n - 1);
    }
}
