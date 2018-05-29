<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Gate;
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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Function fired after user has been authenticated.
     *
     * @param Illuminate\Http\Request $request
     * @param App\User $user
     * @return Illuminate\Http\RedirectResponse
     */
    public function authenticated(Request $request, $user)
    {
        if (!$user->email_verified)
        {
            auth()->logout();
            return back()->with('warning', _("You need to confirm your account. We have sent you an activation code, please check your email."));
        }
        return redirect()->intended($this->redirectPath());
    }

    /**
     * Specified where to redirect users after login.
     *
     * @return string redirection url
     */
    protected function redirectTo()
    {
        if(Gate::allows('view-designer'))
        {
            return route('myworkflows');
        }
        else
        {
            return route('notverified');
        }
    }
}
