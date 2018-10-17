<?php

namespace App\Http\Controllers\Auth;

use App\Http\Common\IpUtils;
use App\Http\Common\ResponseUtils;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * @param Request $request
     * @param $user
     * @return \Illuminate\Http\JsonResponse
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->status === 0) {
            return ResponseUtils::forbidden('该账户已未激活');
        }

        $user->last_login_time = $user->login_time;

        $user->last_login_ip = $user->login_ip;

        $user->last_login_ip_address = $user->login_ip_address;

        $user->login_time = now();

        $user->login_ip = $request->ip();

        $user->login_ip_address = IpUtils::getIpAddress($request->ip());

        $user->update();

        $request->session()->put('user', $user);

        return ResponseUtils::ok('用户登录成功', $user);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return ResponseUtils::ok('用户退出成功');
    }

}
