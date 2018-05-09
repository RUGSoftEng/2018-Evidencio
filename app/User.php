<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Medical professional, reviewer or administrator
 *
 * @property string name
 * @property string email
 * @property string password
 * @property string remember_token
 * @property timestamp created_at
 * @property timestamp updated_at
 * @property string first_name
 * @property string last_name
 * @property string language_code Language used by the User as a 2-character code
 * @property string photo_url
 * @property string academic_degree
 * @property string bio Description of the user, it can contain achievments etc.
 * @property bool is_administrator
 * @property bool is_deactivated If set to true, users personal data should be
 * removed and it is not possible to log in onto the users account anymore
 * @property bool is_reviewer If set to true, the user can comment and approve
 * other user's workflows
 * @property bool is_verified Needs to be set to true for the user to be able to
 * create new workflows
 * @property string organisation Name of the organisation/institution etc. where
 * the user currently works
 * @property timestamp verification_date
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'first_name', 'last_name', 'language_code', 'email', 'password',
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
        return $this->hasMany('App\Workflow','author_id');
    }

    /**
     * Workflows verified by the user (Applicable if isReviewer is set to true)
     */
    public function verifiedWorkflows()
    {
        return $this->hasMany('App\Workflow','verified_by_reviewer_id');
    }

    /**
     * Documents provided by the user during registration to allow their verification
     */
    public function registrationDocuments()
    {
        return $this->hasMany('App\RegistrationDocument','registree_id');
    }

    /**
     * Comments to workflows written by the user (Applicable if isReviewer is
     * set to true)
     */
    public function verificationCommentsWritten()
    {
        return $this->hasMany('App\VerificationComment','reviewer_id');
    }

    /**
     * Replies written to workflow comments by the user
     */
    public function commentRepliesWritten()
    {
        return $this->hasMany('App\CommentReply','author_id');
    }
}
