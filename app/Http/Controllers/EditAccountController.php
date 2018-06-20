<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EditAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view-designer');
    }

    /**
     * Show the edit accounts page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('editaccount',['user' => Auth::user()]);
    }

    /**
     * Get a validator for an incoming edit account request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'photo' => 'nullable|file|image|max:300',
            'academic_degree' => 'nullable|string|max:30',
            'big_code' => 'nullable|integer|digits:11',
            'bio' => 'nullable|string|max:5000',
            'organisation' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
            'old-password' => 'required',
        ]);
    }

    /**
     * Handle an edit account request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $data = $request->all();
        $pwd_hash = Auth::user()->password;

        // Check if old password was provided
        if(!Hash::check($data['old-password'],$pwd_hash))
        {
            throw ValidationException::withMessages(['old-password' => _("Wrong password.")]);
        }

        $this->validator($request->all())->validate();

        $user = Auth::user();

        $user->academic_degree = $data['academic_degree'];
        $user->big_code = $data['big_code'];
        $user->bio = $data['bio'];
        $user->organisation = $data['organisation'];
        if($data['password'] != '')
        {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('editaccount.index')->with('status',_("Account successfuly updated."));
    }

}
