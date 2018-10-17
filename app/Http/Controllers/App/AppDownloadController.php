<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-06-20 18:33
 */

namespace App\Http\Controllers\App;

use App\Http\Common\IpUtils;
use App\Http\Common\ResponseUtils;
use App\Http\Controllers\Base\BaseRestController;
use App\Http\Models\App\AppDownload;
use App\Http\Models\User\UserChannel;
use Illuminate\Http\Request;

class AppDownloadController extends BaseRestController
{
    public function countAppDownloads(Request $request)
    {
        $channel = UserChannel::query()->where('app_promotion_url', $request->host)->first(['id']);

        $appDownload = AppDownload::create([
            'ip' => $request->ip(),
            'address' => IpUtils::getIpAddress($request->ip()),
            'host' => $request->host,
            'system' => $this->getOperatingSystem($request->userAgent()),
            'browser' => $this->getBrowser($request->userAgent()),
            'channel_id' => $channel ? $channel->id : null
        ]);

        return ResponseUtils::ok('统计下载成功', $appDownload);
    }

    public function getOperatingSystem($agent)
    {
        if (strpos($agent, 'iPhone') || strpos($agent, 'iPad')){
            return 'IOS';
        } else if(strpos($agent, 'Android')){
            return 'Android';
        } else {
            return '其他';
        }
    }

    /**
     * @param $agent
     * @return string
     */
    public function getBrowser($agent)
    {
        if (false !== stripos($agent, 'MicroMessage')) {
            return 'MicroMessage';
        }
        if (false !== stripos($agent, 'UC')) {
            return 'UCBrowser';
        }
        if (false !== stripos($agent, 'qq')) {
            return 'QQBrowser';
        }
        if (false == stripos($agent, 'MSIE') && false !== stripos($agent, 'Trident')) {
            return 'Internet Explorer 11.0';
        }
        if (false !== stripos($agent, 'MSIE 10.0')) {
            return 'Internet Explorer 10.0';
        }
        if (false !== stripos($agent, 'MSIE 9.0')) {
            return 'Internet Explorer 9.0';
        }
        if (false !== stripos($agent, 'MSIE 8.0')) {
            return 'Internet Explorer 8.0';
        }
        if (false !== stripos($agent, 'MSIE 7.0')) {
            return 'Internet Explorer 7.0';
        }
        if (false !== stripos($agent, 'MSIE 6.0')) {
            return 'Internet Explorer 6.0';
        }
        if (false !== stripos($agent, 'Edge')) {
            return 'Edge';
        }
        if (false !== stripos($agent, 'Firefox')) {
            return 'Firefox';
        }
        if (false !== stripos($agent, 'Chrome')) {
            return 'Chrome';
        }
        if (false !== stripos($agent, 'Safari')) {
            return 'Safari';
        }
        if (false !== stripos($agent, 'Opera')) {
            return 'Opera';
        }
        if (false !== stripos($agent, '360SE')) {
            return '360SE';
        }
        return 'Unknow';
    }
}
