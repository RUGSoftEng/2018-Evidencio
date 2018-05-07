<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Result of an Evidencio model run after a given step.
 *
 * @property string resultName Variable name by which is this result accessed by
 * the rule engine etc.
 * @property string resultNumber Index of the result in resultSet of an Evidencio
 * model run. If there is only one result, it should be set to 0
 * @property string expectedType Type of the returned result
 * @property string representationLabel Label for the graphical representation
 * of the result (Applicable only if it's a result of a result steps)
 * @property string representationType Type of graphical representation of the
 * result (Applicable only if it's a result of a result steps)
 */
class Result extends Model
{
    public $timestamps = false;

    protected $fillable = ['evidencioModelId','resultName','resultNumber','expectedType','representationLabel','representationType'];

    /**
     * Step, after which there is an Evidencio model run that returns the result
     */
    public function step()
    {
        return $this->belongsTo('App\Step','stepId');
    }
}
