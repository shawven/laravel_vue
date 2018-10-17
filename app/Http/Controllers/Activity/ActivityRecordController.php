<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-07-31 15:29
 */

namespace App\Http\Controllers\Activity;

use App\Http\Common\ResponseUtils;
use App\Http\Controllers\Base\BaseRestController;
use App\Http\Services\Activity\ActivityRecordService;
use Illuminate\Http\Request;

class ActivityRecordController extends BaseRestController
{
    /**
     * @var ActivityRecordService
     */
    private $activityRecordService;

    /**
     * ActivityRecordController constructor.
     * @param ActivityRecordService $activityRecordService
     */
    public function __construct(ActivityRecordService $activityRecordService)
    {
        $this->activityRecordService = $activityRecordService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->post();

        $bool = $this->activityRecordService->storeAndAddHandsel($data);

        if ($bool) {
            return ResponseUtils::created('活动记录添加成功', $data);
        }

        return ResponseUtils::unprocesableEntity('活动记录添加失败', $data);
    }

}
