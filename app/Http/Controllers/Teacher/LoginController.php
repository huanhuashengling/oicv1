<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Redirect;

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
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/teacher';
    protected $username;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:teacher', ['except' => 'logout']);
        $this->username = config('teacher.global.username');
    }

        /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * 重写登录视图页面
     * @author 晚黎
     * @date   2016-09-05T23:06:16+0800
     * @return [type]                   [description]
     */
    public function showLoginForm()
    {
        return view('teacher.login.index');
    }

    public function logout()
    {
        if(auth()->guard('teacher')->user()){
            auth()->guard('teacher')->logout();
        }
        return Redirect::to('teacher/login');
    }
    /**
     * 自定义认证驱动
     * @author 晚黎
     * @date   2016-09-05T23:53:07+0800
     * @return [type]                   [description]
     */
    protected function guard()
    {
        return auth()->guard('teacher');
    }
}