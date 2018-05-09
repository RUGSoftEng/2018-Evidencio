<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Fields that are provided in an input step. They can be either categorical
 * (then they contain a number of options and have empty continuous attributes)
 * or continuous (they provide additional attributes)
 *
 * @property string friendly_title Title that is friendly to a patient
 * @property string friendly_description Description that is friendly to a patient
 * @property int continuous_field_max Maximum input value (Applies only to continuous fields)
 * @property int continuous_field_min Minimum input value (Applies only to continuous fields)
 * @property int continuous_field_step_by Interval between possible input values (Applies only to continuous fields)
 * @property int continuous_field_unit Unit of the value (kilograms, years, etc.) (Applies only to continuous fields)
 * @property int evidencio_variable_id #TODO desc
 */
class Field extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'evidencio_variable_id','friendly_title','friendly_description','continuous_field_max','continuous_field_min','continuous_field_step_by','continuous_field_unit'
    ];

    /**
     * Possible options of a field (Applies only to categorical fields)
     */
    public function options()
    {
        return $this->hasMany('App\Option','categorical_field_id');
    }

    /**
     * Input steps that have this field
     */
    public function inputSteps()
    {
        return $this->belongsToMany('App\Step','field_in_input_steps','field_id','input_step_id');
    }

    public function usedInRunsInSteps()
    {
        return $this->belongsToMany('App\Step','model_run_field_mappings','field_id','step_id');
    }
}
