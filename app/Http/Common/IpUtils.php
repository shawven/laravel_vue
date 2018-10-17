<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-06-13 17:55
 */

namespace App\Http\Common;

class IpUtils
{
    /**
     * @var int
     */
    const MAX_RETRIES = 3;

    /**
     * @param $ip
     * @return string
     */
    public static function getIpAddress($ip)
    {
        if ($ip == '127.0.0.1') {
            return '内网IP';
        }

        $addressInfo = static::getIpAddressInfo($ip);

        $address = $addressInfo['region'] . '-' . $addressInfo['city'] . '-' . $addressInfo['isp'];

        if ($address == 'XX-XX-XX') {
            return $addressInfo['country'];
        }

        return $address;
    }

    /**
     * @param $ip
     * @return mixed
     */
    public static function getIpAddressInfo($ip)
    {
        if ($ip == '127.0.0.1') {
            return '内网IP';
        }

        $i = 0;

        while ($i <= static::MAX_RETRIES) {
            $response = file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip);

            $object = json_decode($response, true);

            if ($object['code'] === 0) {
                return $object['data'];
            }

            $i ++;
        }

        return [
            'region' => '未知',
            'city' => '未知',
            'isp' => '未知'
        ];
    }

}
