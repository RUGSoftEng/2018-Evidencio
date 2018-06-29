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
 * @property int result_step_chart_type
 * @property string result_step_main_label
 */
class Step extends Model
{
    protected $fillable = [
        'title', 'description', 'workflow_step_level', 'colour',
        'is_stored', 'result_step_chart_type',
        'result_step_main_label'
    ];
    protected $touches = ['workflow'];

    public function workflow()
    {
        return $this->belongsTo('App\Workflow', 'workflow_step_workflow_id');
    }

    /**
     * @property string condition set of rules which define on what conditions
     * one can go to the step in a format defined by the used rule engine
     * @property string title
     * @property string description
     */
    public function nextSteps()
    {
        return $this->belongsToMany('App\Step', 'next_steps', 'previous_step_id', 'next_step_id')->withPivot("condition", "title", "description");
    }

    public function previousSteps()
    {
        return $this->belongsToMany('App\Step', 'next_steps', 'next_step_id', 'previous_step_id')->withPivot("condition", "title", "description");
    }


    /**
     * @property int order defines index of a field in a step by which it should
     * be ordered
     */
    public function fields()
    {
        return $this->belongsToMany('App\Field', 'field_in_input_steps', 'input_step_id', 'field_id')->withPivot("order");
    }

    /**
     * @return List of Evidencio model ids that are run after the step
     */
    public function modelRuns()
    {
        return $this->hasMany('App\Result', 'step_id')->groupBy('evidencio_model_id')->pluck('evidencio_model_id');
    }

    /**
     * All results of all model runs done after/during the step
     */
    public function modelRunResults()
    {
        return $this->hasMany('App\Result', 'step_id');
    }

    /**
     * Results of a specific model run done after/during the step
     */
    public function modelRunResultsById($modelId)
    {
        return $this->hasMany('App\Result', 'step_id')->where('evidencio_model_id', $modelId);
    }


    /**
     * All fields associated with all model runs done after/during the step
     */
    public function modelRunFields()
    {
        return $this->belongsToMany('App\Field', 'model_run_field_mappings', 'step_id', 'field_id')->withPivot('evidencio_model_id', 'evidencio_field_id');
    }

    /**
     * Fields associated with a specific model run done after/during the step
     */
    public function modelRunFieldsById($modelId)
    {
        return $this->belongsToMany('App\Field', 'model_run_field_mappings', 'step_id', 'field_id')->withPivot('evidencio_field_id')->wherePivot('evidencio_model_id', $modelId);
    }

    /**
     * Results used in the chart displayed in the result step
     * @property string item_label label of the result in the chart
     * @property string item_background_colour colour of the result item in the
     * chart, in the HTML format
     * @property int item_data placeholder value for the result for presentational
     * purposes used on the designer side
     * @property bool item_is_negated specifies if the value in the chart should
     * be displayed as 100 - variable_value
     */
    public function resultStepChartItems()
    {
        return $this->belongsToMany('App\Result', 'result_step_chart_items', 'item_result_step_id', 'item_result_id')->withPivot('item_label', 'item_background_colour', 'item_data', 'item_is_negated');
    }

    public function getModel($id)
    {
      return Step::where('workflow_step_workflow_id', '=', $id)->orderBy('workflow_step_level')->get();
    }

    /**
     * Removes a step from the database. First detaches and removes everything around it before deleting the step itself.
     *
     * @return void
     */
    public function removeStep()
    {
        $this->previousSteps()->detach();
        $this->nextSteps()->detach();
        $this->modelRunFields()->detach();
        $this->resultStepChartItems()->detach();

        $results = $this->modelRunResults()->get();
        foreach ($results as $result) {
            $result->delete();
        }
        $fields = $this->fields()->get();
        foreach ($fields as $field) {
            $this->fields()->detach($field);
            $field->removeField();
        }
        $this->delete();
    }


    // ---------------------------------- LOAD ---------------------------------- //

    /**
     * Updates the information of a single step
     *
     * @param Array $step Array containing data of step
     */
    public function saveSingleStep($step)
    {
        $this->title = $step['title'];
        $this->description = $step['description'];
        $this->colour = $step['colour'];
        $this->workflow_step_level = $step['level'];
        $this->touch();
        $this->save();
    }

    /**
     * Saves the variables connected to a step, deletes variables if they are removed.
     *
     * @param Array $step Array containing data of step
     * @param Array $variables Array of variables of workflow
     * @return Array Array with [variableIds], [optionIds], the IDs of the saved variables and options in the database.
     */
    public function saveFields($step, $variables)
    {
        $variableIds = [];
        $optionIds = [];
        $savedFields = $this->fields()->get();
        if (!isset($step["variables"])) {
            $step["variables"] = [];
        }
        foreach ($step["variables"] as $key => $var) {
            if (($fld = $savedFields->where("id", $variables[$var]["databaseId"]))->isNotEmpty()) {
                $fld = $fld->first();
                $fld->saveSingleField($variables[$var]);
                $this->fields()->updateExistingPivot($fld, ["order" => $key]);
                if ($variables[$var]["type"] == "categorical")
                    $optionIds[$var] = $fld->saveCategoricalOptions($variables[$var]["options"]);
                $savedFields = $savedFields->filter(function ($value) use ($variables, $var) {
                    return $value->id != $variables[$var]["databaseId"];
                });
            } else {
                $fld = new Field;
                $fld->saveSingleField($variables[$var]);
                $this->fields()->save($fld, ["order" => $key]);
                if ($variables[$var]["type"] == "categorical")
                    $optionIds[$var] = $fld->saveCategoricalOptions($variables[$var]["options"]);
            }
            $variableIds[$var] = $fld->id;
        }
        foreach ($savedFields as $savedField) {
            $this->fields()->detach($savedField);
            $savedField->removeField();
        }
        return ["variableIds" => $variableIds, "optionIds" => $optionIds];
    }


    /**
     * Saves the Evidencio Model variable mapping used for the result-calculation (terrible code, needs rewriting if I can find time)
     *
     * @param Array $apiCalls Array containing data of apiCalls of step
     * @param Array $variableIds Array that links the local VariableId with the one in the database
     * 
     * @return Array Array with the database-ids of the result-models.
     */
    public function saveStepModelApiMapping($apiCalls, $variableIds)
    {
        $resultIds = [];
        $savedApiVars = $this->modelRunFields()->get();
        $savedResults = $this->modelRunResults()->get();
        if (!empty($apiCalls)) {
            foreach ($apiCalls as $key => $apiCall) {
                if (($savedApiVarsModel = $savedApiVars->where('pivot.evidencio_model_id', $apiCall["evidencioModelId"]))->isNotEmpty()) {
                    foreach ($apiCall["variables"] as $apiVar) {
                        $dbApiVar = $savedApiVarsModel->where("pivot.evidencio_field_id", $apiVar["evidencioVariableId"]);
                        if ($dbApiVar->isNotEmpty()) {
                            $dbApiVar = $dbApiVar->first();
                            if ($dbApiVar->id != $variableIds[$apiVar["localVariable"]]) {
                                $this->modelRunFields()->detach($dbApiVar);
                                $this->saveSingleApiVariableMapping($apiVar, $apiCall, $variableIds);
                            }
                            // Remove already existing variable-mappings
                            $savedApiVars = $savedApiVars->reject(function ($value) use ($apiVar, $apiCall) {
                                return ($value->pivot->evidencio_field_id == $apiVar["evidencioVariableId"] && $value->pivot->evidencio_model_id == $apiCall["evidencioModelId"]);
                            });
                        } else {
                            $this->saveSingleApiVariableMapping($apiVar, $apiCall, $variableIds);
                        }
                    }
                } else {
                    foreach ($apiCall["variables"] as $apiVar) {
                        $this->saveSingleApiVariableMapping($apiVar, $apiCall, $variableIds);
                    }
                }
                if (($savedResultsModel = $savedResults->where('evidencio_model_id', $apiCall["evidencioModelId"]))->isNotEmpty()) {
                    foreach ($apiCall["results"] as $keyResult => $result) {
                        if (($dbResult = $savedResultsModel->where('id', $result["databaseId"]))->isEmpty()) {
                            $dbResult = new Result;
                            $dbResult->saveResult($apiCall["evidencioModelId"], $result["name"], $keyResult);
                            $this->modelRunResults()->save($dbResult);
                        } else {
                            $dbResult = $dbResult->first();
                            $savedResults = $savedResults->reject(function ($value) use ($result) {
                                return ($value->id == $result["databaseId"]);
                            });
                        }
                        $resultIds[$key][] = $dbResult->id;
                    }
                } else {
                    foreach ($apiCall["results"] as $keyResult => $result) {
                        $dbResult = new Result;
                        $dbResult->saveResult($apiCall["evidencioModelId"], $result["name"], $keyResult);
                        $this->modelRunResults()->save($dbResult);
                        $resultIds[$key][] = $dbResult->id;
                    }
                }
            }
        }
        $savedApiVars->map(function ($value) {
            $this->modelRunFields()->detach($value);
        });
        $savedResults->map(function ($value) {
            $value->delete();
        });
        return $resultIds;
    }

    /**
     * Saves a single API-call variable mapping
     * Is done by the step, since it connects a step to a variable
     *      (with pivot evidencio_model_id & evidencio_field_id)
     *
     * @param Array $apiVar Variable-mapping to save
     * @param Array $apiCall API-call that is being saved
     * @param Array $variableIds Array that is used to find the corresponding Field in the database
     * @return void
     */
    private function saveSingleApiVariableMapping($apiVar, $apiCall, $variableIds)
    {
        $apiField = Field::where('id', $variableIds[$apiVar["localVariable"]])->first();
        $this->modelRunFields()->save($apiField, [
            "evidencio_model_id" => $apiCall["evidencioModelId"],
            "evidencio_field_id" => $apiVar["evidencioVariableId"]
        ]);
    }

    /**
     * Saves (or edits) the rules connecting the current step with next step(s).
     *
     * @param Array $rules Array containing the information for the rules
     * @param Array $stepIds Array containing the ids used for the steps to identify them.
     * @return void
     */
    public function saveRules($rules, $stepIds)
    {
        $savedRules = $this->nextSteps()->get();
        if (!empty($rules)) {
            foreach ($rules as $rule) {
                $nextStepId = $stepIds[$rule["jsonRule"]["event"]["params"]["stepId"]];
                $rule["jsonRule"]["event"]["params"]["stepId"] = $nextStepId;
                $nextStep = Step::where('id', $nextStepId)->first();
                $ruleSettings = [
                    "title" => $rule["title"],
                    "description" => $rule["description"],
                    "condition" => json_encode($rule["jsonRule"])
                ];
                if (($dbRuleNextStep = $savedRules->where('id', $nextStepId))->isNotEmpty()) {
                    $dbRuleNextStep = $dbRuleNextStep->first();
                    if ($dbRuleNextStep->id != $nextStepId) {
                        $this->nextSteps()->detach($dbRuleNextStep);
                        $this->nextSteps()->attach($nextStep, $ruleSettings);
                    } else {
                        $this->nextSteps()->updateExistingPivot($dbRuleNextStep, $ruleSettings);
                    }
                    $savedRules = $savedRules->reject(function ($value) use ($nextStepId) {
                        return $value->id == $nextStepId;
                    });
                } else {
                    $this->nextSteps()->attach($nextStep, $ruleSettings);
                }
            }
        }
        $savedRules->map(function ($value) {
            $this->nextSteps()->detach($value);
        });
    }

    /**
     * Saves the result-step info needed to display a result-step.
     *
     * @param Array $step Array containing the required information.
     * @return void
     */
    public function saveResultStepInfo(Array $step)
    {
        $possibleResults = Workflow::find($this->workflow_step_workflow_id)->resultsOfWorkflow();
        $this->result_step_chart_type = $step["chartTypeNumber"];
        $this->save();
        $references = $step["chartItemReference"];
        $labels = $step["chartRenderingData"]["labels"];
        $datasets = $step["chartRenderingData"]["datasets"][0];
        $this->resultStepChartItems()->detach();
        foreach ($references as $key => $reference) {
            $dbResult = $possibleResults->where("result_name", $reference["reference"])->first();
            $this->resultStepChartItems()->save($dbResult, [
                "item_label" => $labels[$key],
                "item_background_colour" => $datasets["backgroundColor"][$key],
                "item_data" => $datasets["data"][$key],
                "item_is_negated" => $reference["negation"] == "true"
            ]);
        }
    }

    /**
     * Remove connected results. Very simple for now, but could become more complicated.
     *
     * @return void
     */
    public function removeResults()
    {
        $this->resultStepChartItems()->detach();
        $this->result_step_chart_type = null;
        $this->save();
    }

    // ---------------------------------- LOAD ---------------------------------- //

    /**
     * Loads the relevant information for a step (NOT including the variables)
     *
     * @param Number $stepNum Number that indicates what step in workflow it is.
     * @return Array Array containing information of step and the used variables.
     */
    public function loadStep($stepNum)
    {
        $retObj = [];
        $retObj["id"] = $this->id;
        $retObj["title"] = $this->title;
        $retObj["description"] = $this->description;
        $retObj["colour"] = $this->colour;
        $retObj["level"] = $this->workflow_step_level;
        $variables = $this->loadVariablesOfStep($stepNum);
        $retObj["variables"] = $variables["varIds"];
        $retObj["rules"] = $this->loadStepRules();
        $retObj["apiCalls"] = $this->loadStepModelApiMapping();
        $retObj["chartItemReference"] = [];
        $retObj["chartRenderingData"] = [
            "labels" => [],
            "datasets" => [[
                "data" => [],
                "backgroundColor" => []
            ]]
        ];
        $retObj["chartTypeNumber"] = $this->result_step_chart_type;
        $savedLabels = $this->resultStepChartItems()->get();
        foreach ($savedLabels as $key => $label) {
            $retObj["chartItemReference"][] = [
                "reference" => $label->result_name,
                "negation" => $label->pivot->item_is_negated == 1
            ];
            $retObj["chartRenderingData"]["labels"][] = $label->pivot->item_label;
            $retObj["chartRenderingData"]["datasets"][0]["data"][] = $label->pivot->item_data;
            $retObj["chartRenderingData"]["datasets"][0]["backgroundColor"][] = $label->pivot->item_background_colour;
        }
        if ($savedLabels->isNotEmpty())
            $retObj["type"] = "result";
        else
            $retObj["type"] = "input";
        return ["step" => $retObj, "usedVariables" => $variables["usedVariables"]];
    }

    /**
     * Loads the variables (=Field in database) of a step
     *
     * @param Int $stepNum Index of the step to load variables for (used for correct assigning)
     * @return Array Array containing the identifications of the loaded variables and the variables themselves
     */
    private function loadVariablesOfStep($stepNum)
    {
        $usedVariables = [];
        $variables = $this->fields()->get();
        $varIds = array_fill(0, $variables->count(), "varId");
        foreach ($variables as $key => $variable) {
            $name = "var" . $stepNum . '_' . $key;
            $varIds[$variable->pivot->order] = $name;
            $usedVariables[$name] = $variable->loadField();
        }
        return ["varIds" => $varIds, "usedVariables" => $usedVariables];
    }

    /**
     * Loads the Variable mappings for all API-calls performed in a step.
     * Multiple API-calls can be done in a step, for each call a mapping can be done to select
     * the variables that should be used for the API-call.
     *
     * @return Array Array containing the variable mappings of the API-calls
     */
    private function loadStepModelApiMapping()
    {
        $retObj = [];
        $dbRuns = $this->modelRuns();
        foreach ($dbRuns as $dbRun) {
            $apiCall = [];
            $apiCall["evidencioModelId"] = $dbRun;
            $dbResults = $this->modelRunResultsById($dbRun)->get();
            $dbFields = $this->modelRunFieldsById($dbRun)->get();
            $apiCall["title"] = "";
            $apiCall["results"] = [];
            foreach ($dbResults as $dbResult) {
                $apiCall["results"][$dbResult->result_number] = $dbResult->loadResult();
            }
            $apiCall["variables"] = [];
            foreach ($dbFields as $dbField) {
                $apiCall["variables"][] = $dbField->loadFieldForApiVariableMapping();
            }
            $retObj[] = $apiCall;
        }
        return $retObj;
    }

    /**
     * Loads the rules (conditional connections to next steps) of a step
     *
     * @return Array Array containing the rules
     * 
     * @todo Find nicer way to do the json_decode. Clash between PHP and JS.
     */
    private function loadStepRules()
    {
        $retObj = [];
        $dbRules = $this->nextSteps()->get();
        foreach ($dbRules as $dbRule) {
            $retObj[] = $dbRule->loadRuleNextStep();
        }
        return $retObj;
    }

    /**
     * Loads the rule for reaching a next step. Should only be called on database Step-object that has the required pivot.
     *
     * @return Array Array containing the rule to reach the next step
     */
    private function loadRuleNextStep()
    {
        return [
            "title" => $this->pivot->title,
            "description" => $this->pivot->description,
            "jsonRule" => json_decode(str_replace('"true"', 'true', $this->pivot->condition)),
            "target" => [
                "id" => $this->id,
                "title" => $this->title,
                "colour" => $this->colour
            ]
        ];
    }
}
