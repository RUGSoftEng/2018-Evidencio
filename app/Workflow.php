<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Decision-tree like model that is designed by medical professionals for
 * the patients to fill in.
 *
 * @property timestamp created_at
 * @property timestamp updated_at
 * @property string language_code Language of the workflow as a 2-character code
 * @property string title Title that is friendly to a patient
 * @property string description Description that is friendly to a patient
 * @property bool is_draft Defines if the workflow is a draft visible only to its
 * author
 * @property bool is_published
 * @property bool is_verified Defines if the workflow is verified for publication
 * @property timestamp verification_date
 */

class Workflow extends Model
{

    protected $fillable = ['title','description','language_code'];

    public function author()
    {
        return $this->belongsTo('App\User','author_id');
    }

    public function verifiedByReviewer()
    {
        return $this->belongsTo('App\User','verified_by_reviewer_id');
    }

    /**
     * Individual nodes in the workflow tree
     */
    public function steps()
    {
        return $this->hasMany('App\Step','workflow_step_workflow_id');
    }

    /**
     * Models loaded in the designer page of the workflow
     */
    public function loadedEvidencioModels()
    {
        return $this->hasMany('App\LoadedEvidencioModel','workflow_id');
    }

    /**
     * Workflow remarks written by reviewers
     */
    public function verificationComments()
    {
        return $this->hasMany('App\VerificationComment','workflow_id');
    }
}
