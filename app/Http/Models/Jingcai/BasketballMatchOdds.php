<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-04-17 17:56
 */

namespace App\Http\Models\Jingcai;

use App\Http\Models\BaseModel;

class BasketballMatchOdds extends BaseModel
{
    const CREATED_AT = 'addtime';
    const UPDATED_AT = 'updatetime';

    protected $table = 'jc_lq_match_sp';
    protected $dateFormat = 'U';
    protected $fillable = [
        'id', 'match_id', 'bind', 'homesxname', 'awaysxname', 'lg', 'play', 'sf', 'rfsf', 'dxf', 'sfc_k', 'sfc_z',
        'date'
    ];

}
