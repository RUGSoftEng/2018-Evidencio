<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Nodes in the workflow tree, which can be
 * either input steps (where one inputs data) or result steps (where
 * one is presented with their result). Thus, some attributes apply only
 * to one type of a step and are empty when the step is of other type.
 * Step can also be a "stored step", it is then not connected to
 * a workflow but serves as a possible starting point for creating new
 * steps.
 *
 * @property timestamp created_at
 * @property timestamp updated_at
 * @property string title Title that is friendly to a patient
 * @property string description Description that is friendly to a patient
 * @property bool is_stored If set to true, the step should not be a part of
 * a workflow nor have children or parents. It should serve as a potential
 * starting point for new steps
 * @property int workflow_step_level Level (depth) of the step in workflow tree
 * @property string colour Colour of the node in HTML format (#rrggbb)
 */
class Step extends Model
{

    protected $fillable = ['title','description','workflow_step_level','colour','is_stored'];

    public function workflow()
    {
        return $this->belongsTo('App\Workflow','workflow_step_workflow_id');
    }

    public function nextSteps()
    {
        return $this->belongsToMany('App\Step','next_steps','previous_step_id','next_step_id')->withPivot("condition","title","description");
    }

    public function previousSteps()
    {
        return $this->belongsToMany('App\Step','next_steps','next_step_id','previous_step_id')->withPivot("condition","title","description");
    }

    public function fields()
    {
        return $this->belongsToMany('App\Field','field_in_input_steps','input_step_id','field_id');
    }

    /**
      * @return List of Evidencio model ids that are run after the step
      */
    public function modelRuns()
    {
        return $this->hasMany('App\Result','step_id')->groupBy('evidencio_model_id')->pluck('evidencio_model_id');
    }

    /**
     * All results of all model runs done after/during the step
     */
    public function modelRunResults()
    {
        return $this->hasMany('App\Result','step_id');
    }

    /**
     * Results of a specific model run done after/during the step
     */
    public function modelRunResultsById($modelId)
    {
        return $this->hasMany('App\Result','step_id')->where('evidencio_model_id',$modelId);
    }


    /**
     * All fields associated with all model runs done after/during the step
     */
    public function modelRunFields()
    {
        return $this->belongsToMany('App\Field','model_run_field_mappings','step_id','field_id')->withPivot('evidencio_model_id','evidencio_field_id');
    }

    /**
     * Fields associated with a specific model run done after/during the step
     */
    public function modelRunFieldsById($modelId)
    {
        return $this->belongsToMany('App\Field','model_run_field_mappings','step_id','field_id')->withPivot('evidencio_field_id')->wherePivot('evidencio_model_id', $modelId);
    }
}
