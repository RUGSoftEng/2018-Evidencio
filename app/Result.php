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

    /**
     * Result steps where this result is used in a chart
     * @property string item_label label of the result in the chart
     * @property string item_background_colour colour of the result item in the
     * chart, in the HTML format
     * @property int item_data placeholder value for the result for presentational
     * purposes used on the designer side
     *
     * @property bool item_is_negated specifies if the value in the chart should
     * be displayed as 100 - variable_value
     */
    public function usedInChartsInResultSteps()
    {
        return $this->belongsToMany('App\Step','result_step_chart_items','item_result_id','item_result_step_id')->withPivot('item_label','item_background_colour','item_data','item_is_negated');
    }

    public static function getResult($id)
    {
      return  Result::join('steps', 'steps.id', '=', 'results.step_id')
        ->join('result_step_chart_items', 'item_result_id', '=', 'results.id')
        ->where('steps.workflow_step_workflow_id', '=', $id)
        ->select('steps.id as sid', 'steps.description as desc', 'steps.result_step_chart_type as chartType', 'results.*', 'result_step_chart_items.*', 'steps.result_step_chart_options as opt')
        ->get();
      }

}
