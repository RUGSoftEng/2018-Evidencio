<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Result of an Evidencio model run after a given step.
 *
 * @property string result_name Variable name by which is this result accessed by
 * the rule engine etc.
 * @property string result_number Index of the result in resultSet of an Evidencio
 * model run. If there is only one result, it should be set to 0
 * @property string expected_type Type of the returned result
 * @property string representation_label Label for the graphical representation
 * of the result (Applicable only if it's a result of a result steps)
 * @property string representation_type Type of graphical representation of the
 * result (Applicable only if it's a result of a result steps)
 */
class Result extends Model
{
    public $timestamps = false;

    protected $fillable = ['evidencio_model_id','result_name','result_number','expected_type','representation_label','representation_type'];

    /**
     * Step, after which there is an Evidencio model run that returns the result
     */
    public function step()
    {
        return $this->belongsTo('App\Step','step_id');
    }
}
