<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-07-31 17:12
 */

namespace App\Http\Services\Activity;

use App\Exceptions\BizException;
use App\Http\Models\Activity\Activity;
use App\Http\Models\Activity\ActivityRecord;
use App\Http\Models\User\User;
use Illuminate\Support\Facades\DB;

class ActivityRecordService
{
    /**
     * @param array $data
     * @return mixed
     */
    public function storeAndAddHandsel(array $data)
    {
        $userId = $data['user_id'];
        $activityId = $data['activity_id'];

        $data = ['user_id' => $userId, 'activity_id' => $activityId];

        /**@var Activity $activity*/
        $activity = Activity::query()->find($activityId);

        $count = ActivityRecord::query()
            ->where($data)
            ->count();

        if (!$activity->unlimited() && $count >= $activity->number) {
            if ($count == 1) {
                throw new BizException('该用户已经参与了该活动');
            }
            throw new BizException("该用户参与该活动已经{$count}次，最多参与{$activity->number}次");
        }

        try {
            return DB::transaction(function () use ($userId, $activity, $data) {
                $record = ActivityRecord::create($data);

                /**@var User $user*/
                $user = User::findOrFail($userId);

                $bool = $user->addHandselAndWallet($activity->amount);

                return $record != null && $bool;
            });
        } catch (\Throwable $e) {
            throw new BizException('参与活动失败', $e);
        }
    }
}
