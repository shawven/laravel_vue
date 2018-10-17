<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-05-04 10:14
 */

namespace App\Http\Controllers\User;

use App\Http\Common\ResponseUtils;
use App\Http\Controllers\Base\BaseRestController;
use App\Http\Services\User\UserMoneyRecordService;
use Illuminate\Http\Request;

class UserMoneyRecordController extends BaseRestController
{
    /**
     * @var UserMoneyRecordService
     */
    private $userMoneyRecordService;

    /**
     * HomeController constructor.
     * @param UserMoneyRecordService $userMoneyRecordService
     */
    public function __construct(UserMoneyRecordService $userMoneyRecordService)
    {
        $this->userMoneyRecordService = $userMoneyRecordService;
    }

    public function count(Request $request)
    {
        $data = $this->userMoneyRecordService->countMoneyRecords($request);

        return ResponseUtils::ok('获取数据成功', $data);
    }

}
