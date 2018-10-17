<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-06-25 14:27
 */

namespace App\Http\Services;

use App\Http\Helpers\Selector\Page;
use App\Http\Helpers\Selector\PageInfoWrapper;
use App\Http\Models\User\UserMoneyRecordCount;
use App\Http\Models\UserBalanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserBalanceRecordService
{
    /**
     * @param Request $request
     * @return Page
     */
    public function getPageList(Request $request)
    {
        $subQuery = UserBalanceRecord::query()->selectRaw('count(*)')->groupBy('date');
        $total = DB::table(DB::raw("({$subQuery->toSql()}) as t"))
            ->mergeBindings($subQuery->getQuery())
            ->count();

        $page = new Page((int)$request->page, (int)$request->limit);

        $list = UserBalanceRecord::query()
            ->when($page->getPage() <= 1, function($query) use ($page) {
                $query->where('date', '>=', now()->subDay($page->getLimit())->toDateString());
            })
            ->when($page->getPage() > 1, function($query) use ($page) {
                $now = now();
                $endTime = $now->subDay($page->getOffset())->toDateString();
                $startTime =  $now->subDay($page->getLimit())->toDateString();
                $query->whereBetween('date', [$startTime, $endTime]);
            })
            ->latest('date')
            ->get()
            ->groupBy('date')
            ->toArray();

        $list = $this->addDetailsToList($this->transform($list));

        $page->setList($list);
        $page->setTotal($total);

        return $page;
    }

    /**
     * @param array $list
     * @return array
     */
    public function transform(array $list)
    {
        $newItems = [];
        $i = 0;
        foreach ($list as $key => $items) {
            if (count($items) == 1) {
                $tempItem = $items[0];
                unset($tempItem['id'], $tempItem['type'], $tempItem['desc'],
                    $tempItem['create_time'], $tempItem['update_time']);
                $newItems[$i] = $tempItem;
            } else {
                $tempItem = array_reduce($items, function($carry, $item) {
                    $carry['change_total'] = ($carry['change_total'] ?? 0) + $item['change_total'];
                    $carry['change_balance'] = ($carry['change_balance'] ?? 0) + $item['change_balance'];
                    $carry['change_handsel'] = ($carry['change_handsel'] ?? 0) + $item['change_handsel'];
                    $carry['change_draw_balance'] = ($carry['change_draw_balance'] ?? 0) + $item['change_draw_balance'];

                    return $carry;
                });

                usort($items, function($a, $b) {
                   return $a['id'] - $b['id'];
                });
                $firstItem = current($items);
                $endItem = end($items);

                $tempItem['initial_total'] = $firstItem['initial_total'];
                $tempItem['final_total'] = $endItem['final_total'];
                $tempItem['initial_balance'] = $firstItem['initial_balance'];
                $tempItem['final_balance'] = $endItem['final_balance'];
                $tempItem['initial_handsel'] = $firstItem['initial_handsel'];
                $tempItem['final_handsel'] = $endItem['final_handsel'];
                $tempItem['initial_draw_balance'] = $firstItem['initial_draw_balance'];
                $tempItem['final_draw_balance'] = $endItem['final_draw_balance'];

                $newItems[$i] = array_map(function($value) {
                    return sprintf('%.2f', $value);
                },$tempItem);

                $newItems[$i]['date'] = $key;
            }

            $newItems[$i]['parts'] = $items;
            $i++;
        }

        return $newItems;
    }

    public function addDetailsToList(array $list)
    {
        $dates = array_column($list, 'date');

        $records = UserMoneyRecordCount::query()->whereIn('date', $dates)->get()->toArray();

        array_walk($list, function(&$item) use ($records) {
            $item['details'] = current(array_filter($records, function($record) use ($item) {
                return $record['date'] === $item['date'];
            }));
        });

       return $list;
    }
}
