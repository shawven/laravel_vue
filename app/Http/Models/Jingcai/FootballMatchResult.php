<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-06-05 15:39
 */

namespace App\Http\Models\Jingcai;

use App\Http\Models\BaseModel;

class FootballMatchResult extends BaseModel
{
    public $timestamps = false;
    protected $table = 'app_football_result';
    protected $fillable = [
        'id', 'time', 'matchid', 'League_match', 'Competition_team', 'half_score', 'Total_score', 'nspf', 'spf', 'bf',
        'jqs', 'bqc'
    ];
}
