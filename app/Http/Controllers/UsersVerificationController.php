<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UsersVerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the users verification page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::where('is_verified',0)->get();
        return view('usersverification', ['users' => $users]);
    }

    /**
     * Positively verify users registration
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function accept()
    {
        //TODO check if current user is a reviewer
        $user = User::findOrFail($_POST['user_id']);

        abort_if($user->is_verified || $user->is_deactivated, 400);

        $user->is_verified = true;

        $user->save();

        return redirect()->route("usersverification.index");
    }

    /**
     * Reject users registration
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function reject()
    {
        //TODO check if current user is a reviewer
        $user = User::findOrFail($_POST['user_id']);

        abort_if($user->is_verified || $user->is_deactivated, 400);

        $user->delete();

        return redirect()->route("usersverification.index");
    }
}
