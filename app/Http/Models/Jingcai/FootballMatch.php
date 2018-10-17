<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-04-14 14:36
 */

namespace App\Http\Models\Jingcai;

use App\Http\Models\BaseModel;

class FootballMatch extends BaseModel
{
    public $timestamps = false;
    protected $table = 'jc_match';
    protected $fillable = [
        'id', 'bind', 'homesxname', 'awaysxname', 'lg', 'state', 'date', 'endtime', 'isDan', 'matchtime', 'zid', 'sid',
        'saiguo', 'bifen', 'bf_updatetime', 'isSpf'
    ];

    protected $joinModels = ['fbo' => FootBallMatchOdds::class, 'fbr' => FootballMatchResult::class];

    public function matchOdds() {
        return $this->hasOne(FootBallMatchOdds::class, 'match_id');
    }

    /**
     * @param array $items
     * @param $ids
     * @return array
     */
    public static function addOddsToMatches(array $items, $ids)
    {
        $oddsItems = FootBallMatchOdds::query()
            ->whereIn('match_id', $ids)
            ->get();

        foreach ($oddsItems as $odds) {
            foreach ($items as &$item) {
                if ($item->id == $odds->match_id) {
                    $item->odds = $odds;
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
        $oldItems = FootBallMatch::query()
            ->selectRaw('GROUP_CONCAT(id) as ids')
            ->orderByDesc('date')
            ->groupBy('date')
            ->whereDate('date', '<', now()->toDateString())
            ->offset($offset)
            ->limit($limit);

        $newItems = FootBallMatch::query()
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
