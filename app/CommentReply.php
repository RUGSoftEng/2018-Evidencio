<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommentReply extends Model
{
    protected $table = 'comment_replies';
    public $timestamps = false;

    public function author()
    {
        return $this->belongsTo('App\User','authorId');
    }

    public function verificationComment()
    {
        return $this->belongsTo('App\VerificationComment','verificationCommentId');
    }
}
