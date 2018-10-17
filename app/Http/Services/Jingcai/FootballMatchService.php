<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-06-06 15:30
 */

namespace App\Http\Services\Jingcai;

use App\Exceptions\BizException;
use App\Http\Models\Jingcai\FootballMatch;
use App\Http\Models\Jingcai\FootBallMatchOdds;
use App\Http\Models\Jingcai\FootballMatchResult;
use DB;
use Illuminate\Database\Eloquent\Collection;

class FootballMatchService
{
    /**
     * @param $data
     * @return mixed
     */
    public function storeMatchAndOdds($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $match = FootballMatch::create($data);

                $matchOdds = new FootBallMatchOdds();
                $matchOdds->fill($data);
                return $match->matchOdds()->save($matchOdds);
            });
        } catch (\Throwable $e) {
            throw new BizException('添加足球赛事数据失败', $e);
        }
    }

    /**
     * @param $data
     * @param $matchId
     * @return mixed
     */
    public function updateMatchAndOdds($data, $matchId)
    {
        try {
            return DB::transaction(function () use ($data, $matchId) {
                /** @var FootballMatch $match */
                $match = FootballMatch::findOrFail($matchId);
                $bool = $match->fill($data)->update();

                $matchOdds = $match->matchOdds();
                $attributes = [$matchOdds->getForeignKeyName() => $matchOdds->getParentKey()];
                $result = $matchOdds->updateOrCreate($attributes, $data['fbo']);

                return $bool && $result != null;
            });
        } catch (\Throwable $e) {
            throw new BizException('更新足球赛事数据失败', $e);
        }
    }
}
