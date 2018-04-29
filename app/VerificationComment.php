<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VerificationComment extends Model
{
    public $timestamps = false;

    public function writtenByReviewer()
    {
        return $this->belongsTo('App\User','reviewerId');
    }

    public function workflow()
    {
        return $this->belongsTo('App\Workflow','workflowId');
    }

    public function commentReplies()
    {
        return $this->hasMany('App\CommentReply','verificationCommentId');
    }
}
