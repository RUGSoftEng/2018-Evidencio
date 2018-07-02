<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Step;
use App\loadedEvidencioModels;


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
     * @return Collection Collection with result-objects from the database
     */
    public function resultsOfWorkflow() : Collection
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
    public function publish() : void
    {
        $this->is_draft = false;
        $this->is_published = true;
        $this->is_verified = true; //TODO: remove this after implementing reviewing of the workflows
        $this->save();
    }

    // ---------------------------------- SAVE ---------------------------------- //


    /**
     * Saves the workflow in the database. Also returns the ids for the client side.
     * Should the workflowId be given, that workflow will be updated.
     *
     * @param Request $request Post request withWorkflow data (title/description, steps, etc.)
     * @param App|User $user User object from the database to save the workflow to.
     * @return Array Array with workflowId, [stepIds], [variableIds], [optionIds]
     */
    public function saveWorkflow(Request $request, User $user) : Array
    {
        $returnObj = [];
        $this->author_id = $user->id;
        $this->language_code = $request->languageCode;
        $this->title = $request->title;
        $this->description = $request->description;
        $this->save();
        $this->touch();
        if ($request->modelIds != null) {
            $this->saveLoadedEvidencioModels($request->modelIds);
        }
        $IDs = $this->saveSteps($request->steps, $request->variables);

        $returnObj["workflowId"] = $this->id;
        $returnObj["stepIds"] = $IDs["stepIds"];
        $returnObj["variableIds"] = $IDs["variableIds"];
        $returnObj["optionIds"] = $IDs["optionIds"];
        $returnObj["resultIds"] = $IDs["resultIds"];
        return $returnObj;
    }

    /**
     * Saves the loaded evimodels of a workflow, is required for the designer side.
     *
     * @param Array $modelIds IDs of loaded Evidencio models
     * @return void
     */
    private function saveLoadedEvidencioModels(Array $modelIds) : void
    {
        $savedLoadedModels = $this->loadedEvidencioModels()->get();
        foreach ($modelIds as $modelId) {
            if ($savedLoadedModels->where('model_id', $modelId)->isEmpty()) {
                $loadedModel = new LoadedEvidencioModel(['model_id' => $modelId]);
                $this->loadedEvidencioModels()->save($loadedModel);
            }
        }
    }

    /**
     * Saves the steps, variables, and rules in the database, deletes steps if they are removed.
     *
     * @param Array $steps Steps of workflow
     * @param Array $variables Variables of workflow
     * @return Array Array with [stepIds], [variableIds], [optionIds]
     */
    private function saveSteps(Array $steps, Array $variables) : Array
    {
        $savedSteps = $this->steps()->get();
        $stepIds = [];
        $fieldIds = ['variableIds' => [], 'optionIds' => []];
        foreach ($steps as $step) {
            if (($stp = $savedSteps->where('id', $step['id']))->isNotEmpty()) {
                $stp = $stp->first();
                $stp->saveSingleStep($step);
                $savedSteps = $savedSteps->filter(function ($value) use ($step) {
                    return $value->id != $step['id'];
                });
            } else {
                $stp = new Step;
                $stp->saveSingleStep($step);
                $this->steps()->save($stp);
            }
            $newFieldIds = $stp->saveFields($step, $variables);
            if ($step["type"] == "input") {
                $fieldIds["variableIds"] = array_merge($fieldIds["variableIds"], $newFieldIds["variableIds"]);
                $fieldIds["optionIds"] = array_merge($fieldIds["optionIds"], $newFieldIds["optionIds"]);
            }
            $stepIds[] = $stp->id;
        }

        // Remove deleted steps
        $savedSteps->map(function ($value) {
            $value->removeStep();
        });

        // Save the possible results
        foreach ($steps as $key => $step) {
            $resultIds[] = [];
            $dbStep = $this->steps()->where("id", $stepIds[$key])->first();
            if (!isset($step["apiCalls"]))
                $step["apiCalls"] = [];
            $resultIds[$key] = $dbStep->saveStepModelApiMapping($step["apiCalls"], $fieldIds["variableIds"]);
            if (!isset($step["rules"]))
                $step["rules"] = [];
            $dbStep->saveRules($step["rules"], $stepIds);
        }

        // Save the result-step
        foreach ($steps as $stepKey => $step) {
            $dbStep = $this->steps()->where("id", $stepIds[$stepKey])->first();
            if ($step["type"] == "result" && isset($step["chartItemReference"]) && !empty($step["chartItemReference"])) {
                $dbStep->saveResultStepInfo($step);
            } else {
                $dbStep->removeResults();
            }
        }

        return [
            "stepIds" => $stepIds,
            "variableIds" => $fieldIds["variableIds"],
            "optionIds" => $fieldIds["optionIds"],
            "resultIds" => $resultIds
        ];
    }

    // ---------------------------------- LOAD ---------------------------------- //

    /**
     * Loads a workflow from the database based on the workflowId
     *
     * @return Array
     */
    public function loadWorkflow() : Array
    {
        $retObj = [];
        $usedVariables = [];
        $retObj["success"] = true;
        $retObj["title"] = $this->title;
        $retObj["description"] = $this->description;
        $retObj["languageCode"] = $this->language_code;
        $retObj["evidencioModels"] = $this->getLoadedEvidencioModels();
        $retObj["isDraft"] = $this->is_draft;
        $retObj["steps"] = [];
        $counter = 0;
        $steps = $this->steps()->get();
        foreach ($steps as $step) {
            $stepLoaded = $step->loadStep($counter);
            $retObj["steps"][$counter] = $stepLoaded["step"];
            $usedVariables = array_merge($usedVariables, $stepLoaded["usedVariables"]);
            $counter++;
        }

        $retObj["usedVariables"] = $usedVariables;
        return $retObj;
    }


    /**
     * Returns the IDs of the loaded Evidencio Models of the Workflow
     *
     * @return Array Array of Evidencio model Ids
     */
    private function getLoadedEvidencioModels() : Array
    {
        $array = [];
        $models = $this->loadedEvidencioModels()->get();
        foreach ($models as $model)
            $array[] = $model->model_id;
        return $array;
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
        $steps = $this->steps()->get();
        foreach ($steps as $step) {
            $step->removeStep();

        }
  
        $this->delete();
    }
}
