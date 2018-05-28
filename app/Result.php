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
    protected $fillable = ['evidencio_model_id','result_name','result_number','expected_type','representation_label','representation_type'];
    protected $touches = ['step'];

    public $timestamps = false;
    /**
     * Step, after which there is an Evidencio model run that returns the result
     */
    public function step()
    {
        return $this->belongsTo('App\Step','step_id');
    }

    public static function getResult($id)
    {
      return Result::join('steps', 'steps.id', '=', 'results.step_id')
        ->join('workflows', 'workflows.id', '=', 'steps.workflow_step_workflow_id')
        ->where('workflows.id', '=', $id)
        ->select('steps.id as sid', 'workflows.id as wid', 'results.*')
        ->get();
    }

}

/* Getting the friendly result from our DB. TODO convert to Eloquent
SELECT results.*, w.id as wid, s.id as sid FROM results
INNER JOIN steps as s ON s.id = results.step_id
INNER JOIN workflows as w ON workflow_step_workflow_id = w.id
WHERE w.id = 22
ORDER BY results.result_number */

/* Ordered steps. SQL equivalent. TODO convert to Eloquent
SELECT * FROM fields as f
INNER JOIN field_in_input_steps inp ON f.id = inp.field_id
WHERE inp.input_step_id = 21                       //id with disorderly results.
ORDER BY inp.order ASC
*/
