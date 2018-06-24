<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EvidencioAPI;
use Illuminate\Support\Facades\Auth;
use App\Workflow;
use App\User;
use App\LoadedEvidencioModel;
use App\Step;
use App\Field;
use App\Option;
use App\Result;

/**
 * DesignerController class, handles database- and API-calls for Designerpage.
 */
class DesignerSaveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view-designer');
    }

    /**
     * Saves the workflow in the database.
     * Should the workflowId be given, that workflow will be updated.
     *
     * @param Request $request Post request withWorkflow data (title/description, steps, etc.)
     * @param Int $workflowId
     * @return Array Array with workflowId, [stepIds], [variableIds], [optionIds]
     */
    public function saveWorkflow(Request $request, $workflowId = null)
    {
        $returnObj = [];
        $user = Auth::user();
        $workflow = $this->getWorkflowFromId($user, $workflowId);
        $workflow->author_id = $user->id;
        $workflow->language_code = $request->languageCode;
        $workflow->title = $request->title;
        $workflow->description = $request->description;
        $workflow->save();
        $workflow->touch();
        if ($request->modelIds != null) {
            $this->saveLoadedEvidencioModels($request->modelIds, $workflow);
        }
        $IDs = $this->saveSteps($request->steps, $request->variables, $workflow);

        $returnObj["workflowId"] = $workflow->id;
        $returnObj["stepIds"] = $IDs["stepIds"];
        $returnObj["variableIds"] = $IDs["variableIds"];
        $returnObj["optionIds"] = $IDs["optionIds"];
        $returnObj["resultIds"] = $IDs["resultIds"];
        return $returnObj;
    }

    /**
     * Gets the workflow-object from a Id, if given and if it exists and is owned by the given user.
     * If not, create a new workflow
     *
     * @param App|User $user
     * @param Int $workflowId
     * @return App|Workflow
     */
    private function getWorkflowFromId($user, $workflowId)
    {
        if ($workflowId != null) {
            $workflow = Workflow::find($workflowId);
            if ($workflow == null || $user->cant('save', $workflow)) {
                $workflow = new Workflow;
            }
        } else {
            $workflow = new Workflow;
        }
        return $workflow;
    }

    /**
     * Saves the loaded evimodels of a workflow, is required for the designer side.
     *
     * @param Array $modelIds IDs of loaded Evidencio models
     * @param App|Workflow $workflow Database Model of current workflow
     */
    private function saveLoadedEvidencioModels($modelIds, $workflow)
    {
        $savedLoadedModels = $workflow->loadedEvidencioModels()->get();
        foreach ($modelIds as $modelId) {
            if ($savedLoadedModels->where('model_id', $modelId)->isEmpty()) {
                $loadedModel = new LoadedEvidencioModel(['model_id' => $modelId]);
                $workflow->loadedEvidencioModels()->save($loadedModel);
            }
        }
    }

    /**
     * Saves the steps, variables, and rules in the database, deletes steps if they are removed.
     *
     * @param Array $steps Steps of workflow
     * @param Array $variables Variables of workflow
     * @param App|Workflow $workflow Database Model of current workflow
     * @return Array Array with [stepIds], [variableIds], [optionIds]
     */
    private function saveSteps($steps, $variables, $workflow)
    {
        $savedSteps = $workflow->steps()->get();
        $stepIds = [];
        $fieldIds = ['variableIds' => [], 'optionIds' => []];
        foreach ($steps as $step) {
            if (($stp = $savedSteps->where('id', $step['id']))->isNotEmpty()) {
                $stp = $stp->first();
                $this->saveSingleStep($stp, $step);
                $stp->save();
                $savedSteps = $savedSteps->filter(function ($value) use ($step) {
                    return $value->id != $step['id'];
                });
            } else {
                $stp = new Step;
                $this->saveSingleStep($stp, $step, $variables);
                $workflow->steps()->save($stp);
            }
            if (!isset($step["variables"])) {
                $step["variables"] = [];
            }
            $stp->touch();
            if ($step["type"] == "input") {
                $newFieldIds = $this->saveFields($stp, $step, $variables);
                $fieldIds["variableIds"] = array_merge($fieldIds["variableIds"], $newFieldIds["variableIds"]);
                $fieldIds["optionIds"] = array_merge($fieldIds["optionIds"], $newFieldIds["optionIds"]);
            }
            $stepIds[] = $stp->id;
        }

        // Remove deleted steps
        $savedSteps->map(function ($value) {
            $previousSteps = $value->previousSteps()->get();
            foreach ($previousSteps as $previousStep) {
                $value->previousSteps()->detach($previousStep);
            }
            $nextSteps = $value->nextSteps()->get();
            foreach ($nextSteps as $nextStep) {
                $value->nextSteps()->detach($nextStep);
            }
            $mappings = $value->modelRunFields()->get();
            foreach ($mappings as $mapping) {
                $value->modelRunFields()->detach($mapping);
            }
            $resultLabels = $value->resultStepChartItems()->get();
            foreach ($resultLabels as $resultLabel) {
                $value->resultStepChartItems()->detach($resultLabel);
            }
            $results = $value->modelRunResults()->get();
            foreach ($results as $result) {
                $value->modelRunResults()->detach($result);
                $result->delete();
            }
            $fields = $value->fields()->get();
            foreach ($fields as $field) {
                $value->fields()->detach($field);
                $options = $field->options()->get();
                foreach ($options as $option) {
                    $option->delete();
                }
                $field->delete();
            }
            $value->delete();
        });

        // Save the possible results
        foreach ($steps as $key => $step) {
            $resultIds[] = [];
            $dbStep = $workflow->steps()->where("id", $stepIds[$key])->first();
            if (!isset($step["apiCalls"]))
                $step["apiCalls"] = [];
            $resultIds[$key] = $this->saveStepModelApiMapping($dbStep, $step["apiCalls"], $fieldIds["variableIds"]);
            if (!isset($step["rules"]))
                $step["rules"] = [];
            $this->saveRules($dbStep, $step["rules"], $stepIds);
        }

        // Save the result-step
        $possibleResults = $workflow->resultsOfWorkflow();
        foreach ($steps as $stepKey => $step) {
            if ($step["type"] == "result" && isset($step["chartItemReference"])) {
                $dbStep = $workflow->steps()->where("id", $stepIds[$stepKey])->first();
                $dbStep->result_step_chart_type = $step["chartTypeNumber"];
                $dbStep->save();
                $references = $step["chartItemReference"];
                $labels = $step["chartRenderingData"]["labels"];
                $datasets = $step["chartRenderingData"]["datasets"][0];
                $dbStep->resultStepChartItems()->detach();
                foreach ($references as $key => $reference) {
                    $dbResult = $possibleResults->where("result_name", $reference["reference"])->first();
                    $dbStep->resultStepChartItems()->save($dbResult, [
                        "item_label" => $labels[$key],
                        "item_background_colour" => $datasets["backgroundColor"][$key],
                        "item_data" => $datasets["data"][$key],
                        "item_is_negated" => $reference["negation"] == "true"
                    ]);
                }
            }
        }

        return [
            "stepIds" => $stepIds,
            "variableIds" => $fieldIds["variableIds"],
            "optionIds" => $fieldIds["optionIds"],
            "resultIds" => $resultIds
        ];
    }

    /**
     * Saves the variables connected to a step, deletes variables if they are removed.
     *
     * @param App|Step $dbStep Database Model of step
     * @param Array $step Array containing data of step
     * @param Array $variables Array of variables of workflow
     * @return Array Array with [variableIds], [optionIds], the IDs of the saved variables and options in the database.
     */
    private function saveFields($dbStep, $step, $variables)
    {
        $variableIds = [];
        $optionIds = [];
        $savedFields = $dbStep->fields()->get();
        foreach ($step["variables"] as $key => $var) {
            if (($fld = $savedFields->where("id", $variables[$var]["databaseId"]))->isNotEmpty()) {
                $fld = $fld->first();
                $this->saveSingleField($fld, $variables[$var]);
                $fld->save();
                $dbStep->fields()->updateExistingPivot($fld, ["order" => $key]);
                if ($variables[$var]["type"] == "categorical")
                    $optionIds[$var] = $this->saveCategoricalOptions($fld, $variables[$var]["options"]);
                $savedFields = $savedFields->filter(function ($value) use ($variables, $var) {
                    return $value->id != $variables[$var]["databaseId"];
                });
            } else {
                $fld = new Field;
                $this->saveSingleField($fld, $variables[$var]);
                $dbStep->fields()->save($fld, ["order" => $key]);
                if ($variables[$var]["type"] == "categorical")
                    $optionIds[$var] = $this->saveCategoricalOptions($fld, $variables[$var]["options"]);
            }
            $variableIds[$var] = $fld->id;
        }
        foreach ($savedFields as $savedField) {
            $dbStep->fields()->detach($savedField);
            $stepsUsing = $savedField->usedInRunsInSteps()->get();
            $stepsUsing->map(function ($value) use ($savedField) {
                $value->modelRunFields()->detach($savedField);
            });
            $options = $savedField->options()->get();
            foreach ($options as $option) {
                $option->delete();
            }
            $savedField->delete();
        }
        return ["variableIds" => $variableIds, "optionIds" => $optionIds];
    }

    private function saveRules($dbStep, $rules, $stepIds)
    {
        $savedRules = $dbStep->nextSteps()->get();
        if (!empty($rules)) {
            foreach ($rules as $rule) {
                $nextStepId = $stepIds[$rule["jsonRule"]["event"]["params"]["stepId"]];
                $rule["jsonRule"]["event"]["params"]["stepId"] = $nextStepId;
                $nextStep = Step::where('id', $nextStepId)->first();
                if (($dbRuleNextStep = $savedRules->where('id', $nextStepId))->isNotEmpty()) {
                    $dbRuleNextStep = $dbRuleNextStep->first();
                    if ($dbRuleNextStep->id != $nextStepId) {
                        $dbStep->nextSteps()->detach($dbRuleNextStep);
                        $dbStep->nextSteps()->attach($nextStep, [
                            "title" => $rule["title"],
                            "description" => $rule["description"],
                            "condition" => json_encode($rule["jsonRule"])
                        ]);
                    } else {
                        $dbStep->nextSteps()->updateExistingPivot($dbRuleNextStep, [
                            "title" => $rule["title"],
                            "description" => $rule["description"],
                            "condition" => json_encode($rule["jsonRule"])
                        ]);
                    }
                    $savedRules = $savedRules->reject(function ($value) use ($nextStepId) {
                        return $value->id == $nextStepId;
                    });
                } else {
                    $dbStep->nextSteps()->attach($nextStep, [
                        "title" => $rule["title"],
                        "description" => $rule["description"],
                        "condition" => json_encode($rule["jsonRule"])
                    ]);
                }
            }
        }
        $savedRules->map(function ($value) use ($dbStep) {
            $dbStep->nextSteps()->detach($value);
        });
    }

    /**
     * Saves the Evidencio Model variable mapping used for the result-calculation (terrible code, might need rewriting)
     *
     * @param App|Step $dbStep Database Model of step
     * @param Array $apiCalls Array containing data of apiCalls of step
     * @param Array $variableIds Array that links the local VariableId with the one in the database
     */
    private function saveStepModelApiMapping($dbStep, $apiCalls, $variableIds)
    {
        $resultIds = [];
        $savedApiVars = $dbStep->modelRunFields()->get();
        $savedResults = $dbStep->modelRunResults()->get();
        if (!empty($apiCalls)) {
            foreach ($apiCalls as $key => $apiCall) {
                //$resultIds[] = [];
                if (($savedApiVarsModel = $savedApiVars->where('pivot.evidencio_model_id', $apiCall["evidencioModelId"]))->isNotEmpty()) {
                    foreach ($apiCall["variables"] as $apiVar) {
                        $dbApiVar = $savedApiVarsModel->where("pivot.evidencio_field_id", $apiVar["evidencioVariableId"]);
                        if ($dbApiVar->isNotEmpty()) {
                            $dbApiVar = $dbApiVar->first();
                            if ($dbApiVar->id != $variableIds[$apiVar["localVariable"]]) {
                                $dbStep->modelRunFields()->detach($dbApiVar);
                                $this->saveSingleApiVariableMapping($dbStep, $apiVar, $apiCall, $variableIds);
                            }
                            $savedApiVars = $savedApiVars->reject(function ($value) use ($apiVar, $apiCall) {
                                return ($value->pivot->evidencio_field_id == $apiVar["evidencioVariableId"] && $value->pivot->evidencio_model_id == $apiCall["evidencioModelId"]);
                            });
                        } else {
                            $this->saveSingleApiVariableMapping($dbStep, $apiVar, $apiCall, $variableIds);
                        }
                    }
                } else {
                    foreach ($apiCall["variables"] as $apiVar) {
                        $this->saveSingleApiVariableMapping($dbStep, $apiVar, $apiCall, $variableIds);
                    }
                }
                if (($savedResultsModel = $savedResults->where('evidencio_model_id', $apiCall["evidencioModelId"]))->isNotEmpty()) {
                    foreach ($apiCall["results"] as $keyResult => $result) {
                        if (($dbResult = $savedResultsModel->where('id', $result["databaseId"]))->isEmpty()) {
                            $dbResult = new Result;
                            $dbResult->evidencio_model_id = $apiCall["evidencioModelId"];
                            $dbResult->result_name = $result["name"];
                            $dbResult->result_number = $keyResult;
                            $dbStep->modelRunResults()->save($dbResult);
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
                        $dbResult->evidencio_model_id = $apiCall["evidencioModelId"];
                        $dbResult->result_name = $result["name"];
                        $dbResult->result_number = $keyResult;
                        $dbStep->modelRunResults()->save($dbResult);
                        $resultIds[$key][] = $dbResult->id;
                    }
                }
            }
        }
        $savedApiVars->map(function ($value) use ($dbStep) {
            $dbStep->modelRunFields()->detach($value);
        });
        $savedResults->map(function ($value) {
            $value->delete();
        });
        return $resultIds;
    }

    private function saveSingleApiVariableMapping($dbStep, $apiVar, $apiCall, $variableIds)
    {
        $apiField = Field::where('id', $variableIds[$apiVar["localVariable"]])->first();
        $dbStep->modelRunFields()->save($apiField, [
            "evidencio_model_id" => $apiCall["evidencioModelId"],
            "evidencio_field_id" => $apiVar["evidencioVariableId"]
        ]);
    }

    /**
     * Updates the information of a single step
     *
     * @param App|Step $dbStep Database Model of step
     * @param Array $step Array containing data of step
     */
    private function saveSingleStep($dbStep, $step)
    {
        $dbStep->title = $step['title'];
        $dbStep->description = $step['description'];
        $dbStep->colour = $step['colour'];
        $dbStep->workflow_step_level = $step['level'];
    }

    /**
     * Updates the information of a single variable
     *
     * @param App|Field $dbField Database Model of field (variable)
     * @param Array $field Array containing data of field (variable)
     */
    private function saveSingleField($dbField, $field)
    {
        $dbField->friendly_title = $field["title"];
        $dbField->friendly_description = $field["description"];
        $dbField->evidencio_variable_id = $field["id"];
        if ($field["type"] == "continuous") {
            $dbField->continuous_field_max = $field["options"]["max"];
            $dbField->continuous_field_min = $field["options"]["min"];
            $dbField->continuous_field_unit = $field["options"]["unit"];
            $dbField->continuous_field_step_by = $field["options"]["step"];
        }
    }

    /**
     * Saves/updates the options belonging to a categorical variable.
     *
     * @param App|Field $dbField Database Model of Field (variable)
     * @param Array $options Array of options
     * @return Array Array filled with the database IDs of the saved options.
     */
    private function saveCategoricalOptions($dbField, $options)
    {
        $optionIds = [];
        $savedOptions = $dbField->options()->get();
        foreach ($options as $option) {
            if ($savedOptions->isNotEmpty() && ($opt = $savedOptions->where("id", $option["databaseId"]))->isNotEmpty()) {
                $opt = $opt->first();
                $opt->friendly_title = $option["friendlyTitle"];
                $opt->save();
                $savedOptions = $savedOptions->reject(function ($value) use ($option) {
                    return ($value->id == $option["databaseId"]);
                });
            } else {
                $opt = new Option;
                $opt->title = $option["title"];
                $opt->friendly_title = $option["friendlyTitle"];
                $dbField->options()->save($opt);
            }
            $optionIds[] = $opt->id;
        }
        $savedOptions->map(function ($value) {
            $value->delete();
        });
        return $optionIds;
    }
}
