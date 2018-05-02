<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'evidencioVariableId','friendlyTitle','friendlyDescription','continuousFieldMax','continuousFieldMin','continuousFieldStepBy','continuousFieldUnit'
    ];

    public function options()
    {
        return $this->hasMany('App\Option','categoricalFieldId');
    }

    public function inputSteps()
    {
        return $this->belongsToMany('App\Step','field_in_input_steps','fieldId','inputStepId');
    }

    public function usedInRunsInSteps()
    {
        return $this->belongsToMany('App\Step','model_run_field_mappings','fieldId','stepId');
    }
}
