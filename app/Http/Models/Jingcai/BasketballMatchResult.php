<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-06-05 15:38
 */

namespace App\Http\Models\Jingcai;

use App\Http\Models\BaseModel;

class BasketballMatchResult extends BaseModel
{
    public $timestamps = false;
    protected $table = 'app_basketball_data';
    protected $fillable = [
        'id', 'time', 'matchid', 'League_match', 'Competition_team', 'Section_one', 'Section_two', 'Section_three',
        'Section_four', 'Time_score', 'Final_score',
    ];
}
