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

    protected $fillable = ['title', 'description', 'language_code'];

    public function author()
    {
        return $this->belongsTo('App\User', 'author_id');
    }

    public function verifiedByReviewer()
    {
        return $this->belongsTo('App\User', 'verified_by_reviewer_id');
    }

    /**
     * Individual nodes in the workflow tree
     */
    public function steps()
    {
        return $this->hasMany('App\Step', 'workflow_step_workflow_id');
    }

    /**
     * Models loaded in the designer page of the workflow
     */
    public function loadedEvidencioModels()
    {
        return $this->hasMany('App\LoadedEvidencioModel', 'workflow_id');
    }

    /**
     * Workflow remarks written by reviewers
     */
    public function verificationComments()
    {
        return $this->hasMany('App\VerificationComment', 'workflow_id');
    }

    public static function search($title)
    {
        return Workflow::join('users', 'users.id', '=', 'author_id')
        ->where(function ($query) use ($title) {
            $query->where('title', 'LIKE', '%' . $title . '%')
                  ->orWhere('description', 'LIKE', '%' . $title . '%');
        })
        ->where('workflows.is_verified',true)
        ->where('is_published',true)
        ->select('users.id', 'users.first_name', 'users.last_name', 'workflows.*')
        ->get();
    }

    /**
     * Returns all the results of a workflow
     */
    public function resultsOfWorkflow()
    {
        $steps = $this->steps()->get();
        $results = collect([]);
        foreach ($steps as $step) {
            $results = $results->merge($step->modelRunResults()->get());
        }
        return $results;
    }

    /**
     * Publishes the workflow. The designer can publish a workflow to indicate that it should appear on the website.
     * Right now it is automatically marked verified, but this should be done by another user/administrator, who
     * should be notified of the published workflow in some way. However, we have not been able to implement workflow
     * verification.
     *
     * @return void
     */
    public function publish()
    {
        $this->is_draft = false;
        $this->is_published = true;
        $this->is_verified = true; //TODO: remove this after implementing reviewing of the workflows
        $this->save();
    }


    /**
     * Deletes the workflow with all of its related objects in the database
     *
     * @return void
     */
    public function safeDelete() : void
    {

        //delete comment replies of those comments
        foreach ($this->verificationComments()->get() as $verificationComment) {
            CommentReply::where('verification_comment_id', $verificationComment->id)->delete();
        }

        //delete the verification comments
        VerificationComment::where('workflow_id', $this->id)->delete();

        /*****delete the record about the loaded evidencio model for that workflow*****/
        LoadedEvidencioModel::where('workflow_id', $this->id)->delete();


        /*****delete steps, options, fields, results*****/
        $steps = Step::where('workflow_step_workflow_id', $this->id)->orderBy('workflow_step_level', 'desc')->get();
        foreach ($steps as $step) {
            //references to Jaap DesignerSaveController, saveSteps function
            //$nextSteps = $step -> nextSteps() -> get();
            //foreach ($nextSteps as $nextStep) {
            //    $step->nextSteps()->detach($nextStep);
            //}
            $previousSteps = $step->previousSteps()->get();
            foreach ($previousSteps as $previousStep) {
                $step->previousSteps()->detach($previousStep);
            }

            $mappings = $step->modelRunFields()->get();
            foreach ($mappings as $mapping) {
                $step->modelRunFields()->detach($mapping);
            }
            $fields = $step->fields()->get();
            foreach ($fields as $field) {
                $step->fields()->detach($field);
                $options = $field->options()->get();
                foreach ($options as $option) {
                    $option->delete();
                }
                $field->delete();
            }
            Result::where('step_id', $step->id)->delete();

        }
        Step::where('workflow_step_workflow_id', $this->id)->delete();

        $this->delete();
    }
}
