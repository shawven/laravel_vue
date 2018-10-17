<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-04-14 14:35
 */

namespace App\Http\Controllers\Jingcai;

use App\Http\Common\ResponseUtils;
use App\Http\Controllers\Base\BaseRestController;
use App\Http\Models\Jingcai\FootballMatch;
use App\Http\Models\Jingcai\FootBallMatchOdds;
use App\Http\Services\Jingcai\FootballMatchService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FootballMatchController extends BaseRestController
{
    /**
     * @var FootballMatchService
     */
    private $footballMatchService;

    /**
     * AdminController constructor.
     * @param FootballMatchService $footballMatchService
     */
    public function __construct(FootballMatchService $footballMatchService)
    {
        $this->footballMatchService = $footballMatchService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $data = $request->post();
        $bool = $this->footballMatchService->storeMatchAndOdds($data);

        if ($bool) {
            return ResponseUtils::created('添加成功', $data);
        }

        return ResponseUtils::unprocesableEntity('添加失败', $data);
    }


    /**
     * @param Request $request
     * @param array $pathVariables
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function update(Request $request,  ...$pathVariables)
    {
        $matchId = end($pathVariables);
        $data = $request->post();

        $bool = $this->footballMatchService->updateMatchAndOdds($matchId, $data);

        if ($bool) {
            return ResponseUtils::created('更新成功', $data);
        }

        return ResponseUtils::unprocesableEntity('更新失败', $data);
    }
}
