<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-04-11 12:27
 */

namespace App\Http\Models\User;

use App\Http\Models\BaseModel;

class User extends BaseModel {


    const CREATED_AT = 'addtime';
    const UPDATED_AT = 'updatetime';

    protected $dateFormat = 'U';
    protected $returnDateFormat = self::STANDARD_DATETIME_FORMAT;

    protected $table = 'user';
    protected $fillable = [
        'id', 'usernick', 'userName', 'userPassword', 'userPhone', 'wallet', 'isRealAttestation', 'real_name',
        'real_card', 'real_phone', 'avatar', 'handsel', 'balance', 'draw_balance','devicdid', 'addtime', 'updatetime'
    ];

    /**
     * @param $money
     * @return bool
     */
    public function addDrawBalanceAndWallet($money)
    {
        $money = floatval($money);

        $this->draw_balance += $money;

        return $this->addWallet($money);
    }

    /**
     * @param $money
     * @return bool
     */
    public function addHandselAndWallet($money)
    {
        $money = floatval($money);

        $this->handsel += $money;

        return $this->addWallet($money);
    }

    /**
     * @param $money
     * @return bool
     */
    public function addBalanceAndWallet($money)
    {
        $money = floatval($money);

        $this->balance += $money;

        return $this->addWallet($money);
    }

    /**
     * @param $money
     * @return bool
     */
    public function addWallet($money)
    {
        $money = floatval($money);

        $this->wallet += $money;

        return $this->update();
    }
}
