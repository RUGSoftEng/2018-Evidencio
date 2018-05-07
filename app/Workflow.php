<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Decision-tree like model that is designed by medical professionals for
 * the patients to fill in.
 *
 * @property timestamp created_at
 * @property timestamp updated_at
 * @property string languageCode Language of the workflow as a 2-character code
 * @property string title Title that is friendly to a patient
 * @property string description Description that is friendly to a patient
 * @property bool isDraft Defines if the workflow is a draft visible only to its
 * author
 * @property bool isPublished
 * @property bool isVerified Defines if the workflow is verified for publication
 * @property timestamp verificationDate
 */

class Workflow extends Model
{

    protected $fillable = ['title','description','languageCode'];

    public function author()
    {
        return $this->belongsTo('App\User','authorId');
    }

    public function verifiedByReviewer()
    {
        return $this->belongsTo('App\User','verifiedByReviewerId');
    }

    /**
     * Individual nodes in the workflow tree
     */
    public function steps()
    {
        return $this->hasMany('App\Step','workflowStepWorkflowId');
    }

    /**
     * Models loaded in the designer page of the workflow
     */
    public function loadedEvidencioModels()
    {
        return $this->hasMany('App\LoadedEvidencioModel','workflowId');
    }

    /**
     * Workflow remarks written by reviewers
     */
    public function verificationComments()
    {
        return $this->hasMany('App\VerificationComment','workflowId');
    }
}
