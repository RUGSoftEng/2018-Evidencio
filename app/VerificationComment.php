<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * Remarks to a workflow written by a reviewer
 *
 * @property timestamp created_at
 * @property string text
 */
class VerificationComment extends Model
{
    public $timestamps = false;

    protected $fillable = ['text'];

    public function writtenByReviewer()
    {
        return $this->belongsTo('App\User','reviewer_id');
    }

    public function workflow()
    {
        return $this->belongsTo('App\Workflow','workflow_id');
    }

    /**
     * Replies written to the comment
     */
    public function commentReplies()
    {
        return $this->hasMany('App\CommentReply','verification_comment_id');
    }
}
