<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Fields that are provided in an input step. They can be either categorical
 * (then they contain a number of options and have empty continuous attributes)
 * or continuous (they provide additional attributes)
 *
 * @property string friendlyTitle Title that is friendly to a patient
 * @property string friendlyDescription Description that is friendly to a patient
 * @property int continuousFieldMax Maximum input value (Applies only to continuous fields)
 * @property int continuousFieldMin Minimum input value (Applies only to continuous fields)
 * @property int continuousFieldStepBy Interval between possible input values (Applies only to continuous fields)
 * @property int continuousFieldUnit Unit of the value (kilograms, years, etc.) (Applies only to continuous fields)
 */
class Field extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'evidencioVariableId','friendlyTitle','friendlyDescription','continuousFieldMax','continuousFieldMin','continuousFieldStepBy','continuousFieldUnit'
    ];

    /**
     * Possible options of a field (Applies only to categorical fields)
     */
    public function options()
    {
        return $this->hasMany('App\Option','categoricalFieldId');
    }

    /**
     * Input steps that have this field
     */
    public function inputSteps()
    {
        return $this->belongsToMany('App\Step','field_in_input_steps','fieldId','inputStepId');
    }

    public function usedInRunsInSteps()
    {
        return $this->belongsToMany('App\Step','model_run_field_mappings','fieldId','stepId');
    }
}
