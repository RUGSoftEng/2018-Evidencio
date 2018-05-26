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
     * @param HTTP|Request $request Post request withWorkflow data (title/description, steps, etc.)
     * @param Number $workflowId
     * @return Array Array with workflowId, [stepIds], [variableIds], [optionIds]
     */
    public function saveWorkflow(Request $request, $workflowId = null)
    {
        $returnObj = [];
        $user = Auth::user();
        if ($workflowId != null) {
            $workflow = $user->createdWorkflows()->where('id', '=', $workflowId)->first();
            if ($workflow == null) {
                $workflow = new Workflow;
            }
        } else {
            $workflow = new Workflow;
        }
        $workflow->author_id = $user->id;
        $workflow->language_code = $request->languageCode;
        $workflow->title = $request->title;
        $workflow->description = $request->description;
        $workflow->save();
        if ($request->modelIds != null) {
            $this->saveLoadedEvidencioModels($request->modelIds, $workflow);
        }
        $IDs = $this->saveSteps($request->steps, $request->variables, $workflow);

        $returnObj['workflowId'] = $workflow->id;
        $returnObj['stepIds'] = $IDs['stepIds'];
        $returnObj['variableIds'] = $IDs['variableIds'];
        $returnObj['optionIds'] = $IDs['optionIds'];
        $returnObj['resultIds'] = $IDs['resultIds'];
        return $returnObj;
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
        $variableIds = [];
        $ruleIds = [];
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
            $newFieldIds = $this->saveFields($stp, $step, $variables);
            $fieldIds["variableIds"] = array_merge($fieldIds["variableIds"], $newFieldIds["variableIds"]);
            $fieldIds["optionIds"] = array_merge($fieldIds["optionIds"], $newFieldIds["optionIds"]);
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
        foreach ($steps as $key => $step) {
            $resultIds[] = [];
            $dbStep = $workflow->steps()->where('id', $stepIds[$key])->first();
            if (!isset($step["apiCalls"]))
                $step["apiCalls"] = [];
            $resultIds[$key] = $this->saveStepModelApiMapping($dbStep, $step["apiCalls"], $fieldIds["variableIds"]);
            if (!isset($step["rules"]))
                $step["rules"] = [];
            $this->saveRules($dbStep, $step["rules"], $stepIds);
        }

        return ['stepIds' => $stepIds, 'variableIds' => $fieldIds['variableIds'], 'optionIds' => $fieldIds['optionIds'], 'resultIds' => $resultIds];
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
        foreach ($step['variables'] as $var) {
            if (($fld = $savedFields->where('id', $variables[$var]['databaseId']))->isNotEmpty()) {
                $fld = $fld->first();
                $this->saveSingleField($fld, $variables[$var]);
                $fld->save();
                if ($variables[$var]['type'] == 'categorical')
                    $optionIds[$var] = $this->saveCategoricalOptions($fld, $variables[$var]['options']);
                $savedFields = $savedFields->filter(function ($value) use ($variables, $var) {
                    return $value->id != $variables[$var]['databaseId'];
                });
            } else {
                $fld = new Field;
                $this->saveSingleField($fld, $variables[$var]);
                $dbStep->fields()->save($fld);
                if ($variables[$var]['type'] == 'categorical')
                    $optionIds[$var] = $this->saveCategoricalOptions($fld, $variables[$var]['options']);
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
        return ['variableIds' => $variableIds, 'optionIds' => $optionIds];
    }

    private function saveRules($dbStep, $rules, $stepIds)
    {
        $savedRules = $dbStep->nextSteps()->get();
        if (!empty($rules)) {
            foreach ($rules as $rule) {
                $nextStepId = $stepIds[$rule["target"]["stepId"]];
                $nextStep = Step::where('id', $nextStepId)->first();
                if (($dbRuleNextStep = $savedRules->where('id', $nextStepId))->isNotEmpty()) {
                    $dbRuleNextStep = $dbRuleNextStep->first();
                    if ($dbRuleNextStep->id != $nextStepId) {
                        $dbStep->nextSteps()->detach($dbRuleNextStep);
                        $dbStep->nextSteps()->attach($nextStep, [
                            "title" => $rule["title"],
                            "description" => $rule["description"],
                            "condition" => $rule["condition"]
                        ]);
                    } else {
                        $dbStep->nextSteps()->updateExistingPivot($dbRuleNextStep, [
                            "title" => $rule["title"],
                            "description" => $rule["description"],
                            "condition" => $rule["condition"]
                        ]);
                    }

                    $savedRules = $savedRules->reject(function ($value) use ($nextStepId) {
                        return $value->id == $nextStepId;
                    });
                } else {
                    $dbStep->nextSteps()->attach($nextStep, [
                        "title" => $rule["title"],
                        "description" => $rule["description"],
                        "condition" => $rule["condition"]
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
        $dbField->friendly_title = $field['title'];
        $dbField->friendly_description = $field['description'];
        $dbField->evidencio_variable_id = $field['id'];
        if ($field['type'] == 'continuous') {
            $dbField->continuous_field_max = $field['options']['max'];
            $dbField->continuous_field_min = $field['options']['min'];
            $dbField->continuous_field_unit = $field['options']['unit'];
            $dbField->continuous_field_step_by = $field['options']['step'];
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
            if ($savedOptions->isNotEmpty() && ($opt = $savedOptions->where('id', $option['databaseId']))->isNotEmpty()) {
                $opt = $opt->first();
                $opt->friendly_title = $option["friendlyTitle"];
                $opt->save();
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

    /**
     * Loads a workflow from the database based on the workflowId
     *
     * @param Number $workflowId
     * @return Array
     */
    public function loadWorkflow($workflowId)
    {
        $retObj = [];
        $usedVariables = [];
        $workflow = Auth::user()->createdWorkflows()->where('id', '=', $workflowId)->first();
        if ($workflow == null) {
            $retObj["success"] = false;
            return $retObj;
        }
        $retObj["success"] = true;
        $retObj["title"] = $workflow->title;
        $retObj["description"] = $workflow->description;
        $retObj["languageCode"] = $workflow->language_code;
        $retObj["evidencioModels"] = $this->getLoadedEvidencioModels($workflow);

        $retObj["steps"] = [];
        $counter = 0;
        $steps = $workflow->steps()->get();
        foreach ($steps as $step) {
            $stepLoaded = $this->loadStep($step, $counter, $usedVariables);
            $retObj["steps"][$counter] = $stepLoaded["step"];
            $usedVariables = array_merge($usedVariables, $stepLoaded["usedVariables"]);
            $counter++;
        }

        $retObj["usedVariables"] = $usedVariables;
        return $retObj;
    }

    /**
     * Returns the IDs of the loaded Evidencio Models of the Workflow
     *
     * @param App|Workflow $workflow Workflow to get loaded Evidencio Model IDs from.
     * @return Array
     */
    private function getLoadedEvidencioModels($workflow)
    {
        $array = [];
        $models = $workflow->loadedEvidencioModels()->get();
        foreach ($models as $model)
            $array[] = $model->model_id;
        return $array;
    }
}
