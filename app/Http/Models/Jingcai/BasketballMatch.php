<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-04-14 14:38
 */

namespace App\Http\Models\Jingcai;

use App\Http\Models\BaseModel;

class BasketballMatch extends BaseModel
{

    public $timestamps = false;
    protected $table = 'jc_lq_match';
    protected $fillable = [
        'id', 'bind', 'homesxname', 'awaysxname', 'lg', 'sid', 'state', 'date', 'endtime', 'deadline', 'beginning',
    ];

    protected $joinModels = ['bbo' => BasketballMatchOdds::class, 'bbr' => BasketballMatchResult::class];

    public function matchOdds() {
        return $this->hasOne(BasketballMatchOdds::class, 'match_id');
    }

    /**
     * @param $items
     * @param $ids
     * @return array
     */
    public static function addOddsToMatches($items, $ids)
    {
        $oddsItems = BasketballMatchOdds::query()
            ->whereIn('match_id', $ids)
            ->get();

        foreach ($oddsItems as $odds) {
            foreach ($items as &$item) {
                if ($item['id'] == $odds['match_id']) {
                    $item['odds'] = $odds;
                }
            }
        }

        return $items;
    }


    /**
     * @param $items
     * @return array
     */
    public static function addResultToMatches($items)
    {
        $dates = array_unique(array_map(function ($item) {
            return date('Y-m-d', strtotime($item['endtime']));
        }, $items));

        $results = BasketballMatchResult::query()
            ->whereIn('time', $dates)
            ->orderByDesc('time')
            ->get();

        foreach ($results as $result) {
            foreach ($items as &$item) {
                if ($result['time'] == date('Y-m-d', strtotime($item['endtime']))
                    && $result['matchid'] == $item['bind']) {
                    $item['result'] = $result;
                }
            }
        }

        return $items;
    }

    /**
     * @param $offset
     * @param $limit
     * @return array
     */
    public static function getIdsGroupByDate($offset, $limit)
    {
        $oldItems = static::query()
            ->selectRaw('GROUP_CONCAT(id) as ids')
            ->orderByDesc('date')
            ->groupBy('date')
            ->whereDate('date', '<', now()->toDateString())
            ->offset($offset)
            ->limit($limit);

        $newItems = static::query()
            ->selectRaw('GROUP_CONCAT(id) as ids')
            ->orderByDesc('date')
            ->groupBy('date')
            ->whereDate('date', '>=', now()->toDateString());

        return $oldItems->union($newItems)->pluck('ids')->toArray();
    }

    /**
     * @return bool
     */
    public function hasResult()
    {
        return !!$this->saiguo;
    }
}
