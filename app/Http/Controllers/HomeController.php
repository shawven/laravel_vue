<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-05-18 15:28
 */

namespace App\Http\Controllers;

use App\Events\Schedules\CountUserMoneyRecords;
use App\Http\Common\ResponseUtils;
use App\Http\Services\HomeService;
use Illuminate\Http\Request;

class HomeController
{
    /**
     * @var HomeService
     */
    private $homeService;

    /**
     * HomeController constructor.
     * @param $homeService
     */
    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    public function run(Request $request)
    {
        event(new CountUserMoneyRecords);
    }

    /**
     * 首页
     */
    public function index()
    {
        return view('index');
    }

    /**
     * @return string
     */
    public function attempt()
    {
        return 'true';
    }


    public function phpInfo()
    {
        exit(phpinfo());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function count()
    {
        $data['order'] = $this->homeService->countOrders();
        $data['user'] = $this->homeService->countUsers();

        return ResponseUtils::ok('获取数据成功', $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function countOrders(Request $request)
    {
        $data = $this->homeService->countOrders([$request->startTime, $request->endTime]);

        return ResponseUtils::ok('获取数据成功', $data);
    }
}
