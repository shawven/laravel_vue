<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-05-16 15:40
 */

namespace App\Http\Models\User;

use App\Http\Models\BaseModel;

class UserOrderTicket extends BaseModel
{
    const CREATED_AT = 'addtime';
    const UPDATED_AT = null;

    protected $dateFormat = 'U';
    protected $returnDateFormat = self::STANDARD_DATETIME_FORMAT;

    protected $table = 'user_order_ticket_record';
    protected $fillable = ['id', 'order_id', 'obj_id', 'state', 'addtime', 'message', 'code', 'PrintTicketId', 'Odds',
        'content'
    ];

    public function isMixedPassage()
    {
        return strpos($this->Odds, '//') !== false;
    }
}
