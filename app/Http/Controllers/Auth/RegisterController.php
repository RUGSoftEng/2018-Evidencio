<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Mail\VerifyMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectTo = route('notverified');
        $this->middleware('guest');
    }

    /**
     * Function called just after registration
     *
     * @param Illuminate\Http\Request $request
     * @param App\User $user
     * @return Illuminate\Http\RedirectResponse
     */
    protected function registered(Request $request, $user)
    {
        $this->guard()->logout();
        return redirect()->route("login")->with('status', _("We sent you an activation code. Check your email and click on the link to verify."));
    }


    /**
     * Verify users when they provide a link containing the token they received
     * by email
     *
     * @param string $token
     * @return Illuminate\Http\RedirectResponse
     */
    public function verifyUser($token)
    {
        $user = User::where('email_token', $token)->first();
        if(isset($user))
        {
            if(!$user->email_verified)
            {
                // We don't remove the token to be able to show information that
                // the e-mail has already been verified.
                $user->email_verified = true;
                $user->save();
                $status = _("Your e-mail is verified, you can now log in!");
            }
            else
            {
                $status = _("Your e-mail is already verified, you can now log in!");
            }
        }
        else
        {
            return redirect()->route("login")->with('warning',_("Sorry your email cannot be identified!"));
        }

        return redirect()->route("login")->with('status',$status);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        return Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'photo' => 'nullable|file|image|max:300',
            'academic_degree' => 'nullable|string|max:30',
            'big_code' => 'nullable|integer|digits:11',
            'bio' => 'nullable|string|max:5000',
            'organisation' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'file' => 'nullable|array|max:5',
            'file.*' => 'nullable|mimes:pdf|max:1000',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            // TODO photo, language
            'language_code' => 'en',
            'academic_degree' => $data['academic_degree'],
            'big_code' => $data['big_code'],
            'bio' => $data['bio'],
            'organisation' => $data['organisation'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'email_token' => bin2hex(openssl_random_pseudo_bytes(16)),
        ]);

        if(array_key_exists('file',$data))
        foreach($data['file'] as $file)
        {
            $path = $file->store('documents');

            $user->registrationDocuments()->create([
                'name' => $file->getClientOriginalName(),
                'url' => $path
            ]);
        }

        Mail::to($user)->send(new VerifyMail($user));

        return $user;
    }
}
