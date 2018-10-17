<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-04-06 23:13
 */

namespace App\Http\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

abstract class BaseModel extends Model
{
    const STANDARD_DATETIME_FORMAT = 'Y-m-d H:i:s';

    const STANDARD_DATE_FORMAT = 'Y-m-d';

    protected $returnDateFormat;

    protected $joinModels = [];

    /**
     * @return array
     */
    public function getJoinModels()
    {
        $tempArray = [];
        foreach ($this->joinModels as $key => $model) {
            if (is_string($key)) {
                $tempArray[$key] = $model;
                $alisa = class_basename($model);
                $tempArray[$alisa] = $model;
            }
            if (is_numeric($key)) {
                $alisa = class_basename($model);
                $tempArray[$alisa] = $model;
            }
        }
        return $tempArray;
    }

    /**
     * 重写父类方法，增加标准时间字符串转化
     *
     * @param mixed $value
     * @return Carbon
     */
    protected function asDateTime($value)
    {
        if ($this->isStandardDateTimeFormat($value)) {
            return Carbon::createFromFormat(static::STANDARD_DATETIME_FORMAT, $value);
        }
        return parent::asDateTime($value);
    }

    /**
     * @param $value
     * @return false|int
     */
    protected function isStandardDateTimeFormat($value) {
        return preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})\s([01]?\d|2[0-3]):[0-5]?\d:[0-5]?\d$/', $value);
    }


    /**
     * 重写时间取出时的格式化方法，例数据库存unix时间戳，取出时转化为标准时间字符串
     *
     * @param \Illuminate\Support\Carbon $date 实现类
     * @param DateTimeInterface $date    接口类
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        if ($this->returnDateFormat) {
            switch ($this->returnDateFormat) {
                case static::STANDARD_DATETIME_FORMAT:
                    return $date->toDateTimeString();
                case static::STANDARD_DATE_FORMAT:
                    return $date->toDateString();
                default:
            }
        }

        return parent::serializeDate($date);
    }
}
