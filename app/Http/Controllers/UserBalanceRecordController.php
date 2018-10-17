<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-06-22 11:02
 */

namespace App\Http\Controllers;

use App\Http\Common\ResponseUtils;
use App\Http\Controllers\Base\BaseRestController;
use App\Http\Services\UserBalanceRecordService;
use Illuminate\Http\Request;

class UserBalanceRecordController extends BaseRestController
{
    /**
     * @var UserBalanceRecordService
     */
    private $userBalanceRecordService;

    /**
     * UserBalanceRecordController constructor.
     * @param UserBalanceRecordService $userBalanceRecordService
     */
    public function __construct(UserBalanceRecordService $userBalanceRecordService)
    {
        $this->userBalanceRecordService = $userBalanceRecordService;
    }


    public function index(Request $request)
    {
        $page = $this->userBalanceRecordService->getPageList($request);

        if ($page->getTotal() > 0) {
            return ResponseUtils::ok("获取数据成功", $page->toArray());
        }

        return ResponseUtils::notFound("暂无数据");
    }


}
