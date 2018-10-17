<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-06-06 15:30
 */

namespace App\Http\Services\Jingcai;

use App\Exceptions\BizException;
use App\Http\Models\Jingcai\BasketballMatch;
use App\Http\Models\Jingcai\BasketballMatchOdds;
use App\Http\Models\Jingcai\BasketballMatchResult;
use DB;

class BasketballMatchService
{

    /**
     * @param $data
     * @return mixed
     */
    public function storeMatchAndOdds($data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $match = BasketballMatch::create($data);

                $matchOdds = new BasketballMatchOdds();
                $matchOdds->fill($data);
                return $match->matchOdds()->save($matchOdds);
            });
        } catch (\Throwable $e) {
            throw new BizException('添加篮球赛事数据失败', $e);
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
                /** @var BasketballMatch $match */
                $match = BasketballMatch::findOrFail($matchId);
                $bool = $match->fill($data)->update();

                $matchOdds = $match->matchOdds();
                $result = $matchOdds->updateOrCreate([$matchOdds->getForeignKeyName() => $matchOdds->getParentKey()], $data['bbo']);

                return $bool && $result != null;
            });
        } catch (\Throwable $e) {
            throw new BizException('更新篮球赛事数据失败', $e);
        }
    }
}
