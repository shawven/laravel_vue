<?php

namespace App\Http\Controllers\Auth;

use App\Http\Common\ResponseUtils;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Validator;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
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
        $this->middleware('auth');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $data = $request->all(); //接收所有的数据
        $user = Auth::user();

        $rules = ['password' => 'required|confirmed|min:5'];
        $messages = [
            'password.required' => '密码不能为空',
            'password.confirmed' => '新密码和确认密码不匹配'
        ];

        $validator = Validator::make($data, $rules, $messages);

        $validator->after(function($validator) use ($data, $user) {
            if (!$user->checkPassword($data['oldPassword'])) {
                $validator->errors()->add('password', '原密码错误');
            }
        });

        if ($validator->fails()) {
            return ResponseUtils::unprocesableEntity(last($validator->errors()->toArray()['password']));
        }

        $user->encryptPassword($data['password']);
        $user->save();      //成功后，保存新密码

        return ResponseUtils::ok('密码修改成功');
    }

}
