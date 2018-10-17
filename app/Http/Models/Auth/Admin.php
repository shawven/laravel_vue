<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-05-13 11:03
 */

namespace App\Http\Models\Auth;
use App\Http\Common\IpUtils;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    protected $table = 'admin';

    protected $dates = [
        'create_time', 'update_time', 'login_time', 'last_login_time'
    ];

    protected $fillable = [
        'id', 'username', 'nickname', 'real_name', 'password', 'mobile', 'email', 'avatar', 'department', 'role_id',
        'login_time', 'login_ip', 'login_ip_address', 'last_login_time', 'last_login_ip', 'last_login_ip_address',
        'register_ip', 'register_ip_address', 'status', 'create_time', 'update_time'
    ];

    protected $hidden = [
        'password',
    ];

    protected $rememberTokenName = '';

    /**
     * @param $data
     * @return Admin|\Illuminate\Database\Eloquent\Model
     */
    public static function createAccount($data)
    {
        return static::create([
            'username' => $data['username'],
            'nickname' => $data['nickname'] ?? $data['username'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'real_name' => $data['real_name'],
            'status' => $data['status'],
            'department' => $data['department'],
            'role_id' => $data['role_id'],
            'register_ip' => request()->ip(),
            'register_ip_address' => IpUtils::getIpAddress(request()->ip()),
        ]);
    }

    /**
     * @param $password
     * @return bool
     */
    public function checkPassword($password)
    {
        return \Hash::check($password, $this->password);
    }

    /**
     * @param $password
     */
    public function encryptPassword($password)
    {
        $this->password = bcrypt($password);
    }

    /**
     * @param $oldPassword
     * @param $newPassword
     * @return bool
     */
    public function updatePassword($oldPassword, $newPassword)
    {
        if ($this->checkPassword($oldPassword)) {
            $this->encryptPassword($newPassword);

            return $this->update();
        }

        return false;
    }

    /**
     * @param $newPassword
     * @return bool
     */
    public function resetPassword($newPassword)
    {
        $this->encryptPassword($newPassword);

        return $this->update();
    }
}
