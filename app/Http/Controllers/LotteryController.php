<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-06-03 15:37
 */

namespace App\Http\Controllers;

use App\Http\Common\ResponseUtils;
use App\Http\Services\Lottery\LotteryService;
use Illuminate\Http\Request;

class LotteryController
{
    /**
     * @var LotteryService
     */
    private $lotteryService;

    /**
     * LotteryController constructor.
     * @param LotteryService $lotteryService
     */
    public function __construct(LotteryService $lotteryService)
    {
        $this->lotteryService = $lotteryService;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $stages = $this->lotteryService->getLotteryStages($request);

        return ResponseUtils::ok('获取数据成功', $stages);
    }

    public function analyze(Request $request)
    {
        $info = $this->lotteryService->analyzeOrdersSettlementInfo($request->type, $request->orderIds);
        return ResponseUtils::ok('获取数据成功', $info);
    }

    public function settle(Request $request)
    {
        $this->lotteryService->settleOrders($request->type, $request->orderIds);
        return ResponseUtils::ok('派奖成功');
    }
}
