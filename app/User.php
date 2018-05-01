<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'firstName', 'lastName', 'languageCode', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function createdWorkflows()
    {
        return $this->hasMany('App\Workflow','authorId');
    }

    public function verifiedWorkflows()
    {
        return $this->hasMany('App\Workflow','verifiedByReviewerId');
    }

    public function registrationDocuments()
    {
        return $this->hasMany('App\RegistrationDocument','registreeId');
    }

    public function verificationCommentsWritten()
    {
        return $this->hasMany('App\VerificationComment','reviewerId');
    }

    public function commentRepliesWritten()
    {
        return $this->hasMany('App\CommentReply','authorId');
    }
}
