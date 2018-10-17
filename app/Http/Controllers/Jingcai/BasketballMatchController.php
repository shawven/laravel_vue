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
use App\Http\Models\Jingcai\BasketballMatch;
use App\Http\Models\Jingcai\BasketballMatchOdds;
use App\Http\Services\Jingcai\BasketballMatchService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BasketballMatchController extends BaseRestController
{

    /**
     * @var BasketballMatchService
     */
    private $basketballMatchService;

    /**
     * AdminController constructor.
     * @param BasketballMatchService $basketballMatchService
     */
    public function __construct(BasketballMatchService $basketballMatchService)
    {
        $this->basketballMatchService = $basketballMatchService;
    }


    /**
     * @param array $list
     * @return array
     */
    public function transform(array $list)
    {
        return BasketballMatch::addResultToMatches($list);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->post();
        $bool = $this->basketballMatchService->storeMatchAndOdds($data);

        if ($bool) {
            return ResponseUtils::created('添加成功', $data);
        }

        return ResponseUtils::unprocesableEntity('添加失败', $data);
    }


    /**
     * @param Request $request
     * @param array $pathVariables
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request,  ...$pathVariables)
    {
        $matchId = end($pathVariables);
        $data = $request->post();

        $bool = $this->basketballMatchService->updateMatchAndOdds($matchId, $data);

        if ($bool) {
            return ResponseUtils::created('更新成功', $data);
        }

        return ResponseUtils::unprocesableEntity('更新失败', $data);
    }

}
