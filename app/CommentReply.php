<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Reply written to a VerificationComment
 *
 * @property timestamp created_at
 * @property string text
 */
class CommentReply extends Model
{
    protected $table = 'comment_replies';
    public $timestamps = false;

    protected $fillable = ['text'];

    public function author()
    {
        return $this->belongsTo('App\User','authorId');
    }

    public function verificationComment()
    {
        return $this->belongsTo('App\VerificationComment','verificationCommentId');
    }
}
