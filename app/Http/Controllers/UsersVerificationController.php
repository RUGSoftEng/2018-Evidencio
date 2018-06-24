<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\User;
use App\RegistrationDocument;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountApproved;
use App\Mail\AccountRejected;

class UsersVerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:is-administrator');
    }

    private function deleteDocuments(User $user)
    {
        foreach($user->registrationDocuments as $document)
        {
            Storage::delete($document->url);
            $document->delete();
        }
    }

    /**
     * Show the users verification page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::where('is_verified',0)->where('email_verified',1)->get();
        return view('usersverification', compact("users"));
    }

    /**
     * Positively verify users registration
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function accept()
    {
        $user = User::findOrFail($_POST['user_id']);

        abort_if($user->is_verified || $user->is_deactivated, 400);

        $this->deleteDocuments($user);

        $user->is_verified = true;

        $user->save();

        Mail::to($user)->queue(new AccountApproved($user));

        return redirect()->route("usersverification.index");
    }

    /**
     * Reject users registration
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function reject()
    {
        $user = User::findOrFail($_POST['user_id']);

        abort_if($user->is_verified || $user->is_deactivated, 400);

        $this->deleteDocuments($user);

        $userName = $user->first_name;
        $email = $user->email;

        $user->delete();

        Mail::to($email)->queue(new AccountRejected($userName));

        return redirect()->route("usersverification.index");
    }

    /**
     * Download registration document
     *
     * @return Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download($id)
    {
        $document = RegistrationDocument::findOrFail($id);
        return Storage::response($document->url,$document->name);
    }
}
