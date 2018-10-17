<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-06-13 17:32
 */

namespace App\Http\Models\App;

use App\Http\Models\BaseModel;
use App\Http\Models\User\UserChannel;

class AppDownload extends BaseModel
{
    const CREATED_AT = 'create_time';
    const UPDATED_AT = null;

    protected $table = 'app_download';
    protected $fillable = ['id', 'ip', 'address', 'host', 'system', 'browser', 'channel_id', 'create_time'];

    protected $joinModels = ['uc' => UserChannel::class];
}
