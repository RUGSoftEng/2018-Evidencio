<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    public function workflow()
    {
        return $this->belongsTo('App\Workflow','workflowStepWorkflowId');
    }

    public function nextSteps()
    {
        return $this->belongsToMany('App\Step','next_steps','previousStepId','nextStepId')->withPivot("condition","title","description");
    }

    public function previousSteps()
    {
        return $this->belongsToMany('App\Step','next_steps','nextStepId','previousStepId')->withPivot("condition","title","description");
    }

    public function fields()
    {
        return $this->belongsToMany('App\Field','field_in_input_steps','inputStepId','fieldId');
    }

    /**
      * @return List of Evidencio model ids that are run after the step
      */
    public function modelRuns()
    {
        return $this->hasMany('App\Result','stepId')->groupBy('evidencioModelId')->pluck('evidencioModelId');
    }

    public function modelRunResults()
    {
        return $this->hasMany('App\Result','stepId');
    }

    public function modelRunResultsById($modelId)
    {
        return $this->hasMany('App\Result','stepId')->where('evidencioModelId',$modelId);
    }

    public function modelRunFields()
    {
        return $this->belongsToMany('App\Field','model_run_field_mappings','stepId','fieldId')->withPivot('evidencioModelId','evidencioFieldId');
    }

    public function modelRunFieldsById($modelId)
    {
        return $this->belongsToMany('App\Field','model_run_field_mappings','stepId','fieldId')->withPivot('evidencioFieldId')->wherePivot('evidencioModelId', $modelId);
    }
}
