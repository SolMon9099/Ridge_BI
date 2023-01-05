<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\AdminController;
use App\Providers\RouteServiceProvider;
use App\Service\SafieApiService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class LoginController extends AdminController
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
    protected $redirectTo = RouteServiceProvider::ADMIN_HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view("{$this->platform}.auth.login");
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect(route('admin.login'));
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            return $this->sendLoginResponse($request);
        } else {
            //safie user check
            if (SafieApiService::checkSafieUser($request['email'], $request['password'])) {
                $new_user = new Admin();
                $new_user->is_main_admin = 1;
                $new_user->email = $request['email'];
                $new_user->name = $request['email'];
                $new_user->authority_id = config('const.authorities_codes.admin');
                $new_user->password = Hash::make($request['password']);
                $new_user->is_enabled = 1;
                $new_user->contract_no = $request['email'];
                $new_user->safie_user_name = $request['email'];
                $new_user->safie_password = $request['password'];
                $new_user->safie_client_id = env('CLIENT_ID', 'dc2537ffb887');
                $new_user->safie_client_secret = env('SECRET', 'd25100e130499d0fb257df19cd5b0279');
                $new_user->is_main_admin = 1;
                $new_user->header_menu_ids = implode(',', array_keys(config(('const.header_menus'))));
                $new_user->save();
                if ($this->attemptLogin($request)) {
                    if ($request->hasSession()) {
                        $request->session()->put('auth.password_confirmed_at', time());
                    }

                    return $this->sendLoginResponse($request);
                }
            }
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }
}
