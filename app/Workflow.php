<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    public function author()
    {
        return $this->belongsTo('App\User','authorId');
    }

    public function verifiedByReviewer()
    {
        return $this->belongsTo('App\User','verifiedByReviewerId');
    }

    public function steps()
    {
        return $this->hasMany('App\Step','workflowStepWorkflowId');
    }
}
