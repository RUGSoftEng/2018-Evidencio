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
        return $this->belongsToMany('App\Step','next_steps','previousStepId','nextStepId'); //TODO pivots
    }

    public function previousSteps()
    {
        return $this->belongsToMany('App\Step','next_steps','nextStepId','previousStepId'); //TODO pivots
    }

    public function fields()
    {
        return $this->belongsToMany('App\Field','field_in_input_steps','inputStepId','fieldId');
    }
}
