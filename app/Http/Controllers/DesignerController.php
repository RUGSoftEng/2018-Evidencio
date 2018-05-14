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

/**
 * DesignerController class, handles database- and API-calls for Designerpage.
 */
class DesignerController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('designer');
    }

    /**
     * Fetch model from Evidencio based on its id, used for designer to retrieve variables.
     * 
     * @param HTTP|Request $request Post request containing a Evidencio modelId
     * @return JSON Evidencio model data
     */
    public function fetchVariables(Request $request)
    {
        $modelId = $request->modelId;
        $data = EvidencioAPI::getModel($modelId);
        return $data;
    }

    public function runModel(Request $request) {
        $data = EvidencioAPI::run($request->modelId, $request->values);
        return $data;
    }

    /**
     * Fetch models from Evidencio based on thier search result, used for designer to search for models.
     *
     * @param HTTP|Request $request Post request containing a Evidencio Model Search
     * @return JSON Evidencio models
     */
    public function fetchSearch(Request $request)
    {
        $modelSearch = $request->modelSearch;
        $data = EvidencioAPI::search($modelSearch);
        return json_encode($data);
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
        $returnObj['ruleIds'] = $IDs['ruleIds'];
        $returnObj['variableIds'] = $IDs['variableIds'];
        $returnObj['optionIds'] = $IDs['optionIds'];
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
            if ($savedSteps->isNotEmpty() && ($stp = $savedSteps->where('id', $step['id']))->isNotEmpty()) {
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
            if (isset($step['variables']))
                $fieldIds = array_merge($variableIds, $this->saveFields($stp, $step, $variables));
            $stepIds[] = $stp->id;
        }

        // TODO: add rules between steps
        // foreach ($steps as $step) {
        //     $savedRules = $step->nextSteps()->get();

        // }

        $savedSteps->map(function ($value) {
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
        return ['stepIds' => $stepIds, 'ruleIds' => $ruleIds, 'variableIds' => $fieldIds['variableIds'], 'optionIds' => $fieldIds['optionIds']];
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
            if ($savedFields->isNotEmpty() && ($fld = $savedFields->where('id', $variables[$var]['databaseId']))->isNotEmpty()) {
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
        foreach ($savedFields as $value) {
            $dbStep->fields()->detach($value);
            $options = $value->options()->get();
            foreach ($options as $option) {
                $option->delete();
            }
            $value->delete();
        }
        return ['variableIds' => $variableIds, 'optionIds' => $optionIds];
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
            $retObj['success'] = false;
            return $retObj;
        }
        $retObj['success'] = true;
        $retObj['title'] = $workflow->title;
        $retObj['description'] = $workflow->description;
        $retObj['languageCode'] = $workflow->language_code;
        $retObj['evidencioModels'] = $this->getLoadedEvidencioModels($workflow);

        $retObj['steps'] = [];
        $counter = 0;
        $steps = $workflow->steps()->get();
        foreach ($steps as $step) {
            $stepLoaded = $this->loadStep($step, $counter, $usedVariables);
            $retObj['steps'][$counter] = $stepLoaded['step'];
            $usedVariables = array_merge($usedVariables, $stepLoaded['usedVariables']);
            $counter++;
        }

        $retObj['usedVariables'] = $usedVariables;
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

    /**
     * Loads the relevant information for a step (NOT including the variables)
     *
     * @param App|Step $dbStep Database Model of Step
     * @param Number $stepNum Number that indicates what step in workflow it is.
     * @return Array Array containing information of step and the used variables.
     */
    private function loadStep($dbStep, $stepNum)
    {
        $retObj = [];
        $retObj['id'] = $dbStep->id;
        $retObj['title'] = $dbStep->title;
        $retObj['description'] = $dbStep->description;
        $retObj['colour'] = $dbStep->colour;
        $retObj['level'] = $dbStep->workflow_step_level;
        $variables = $this->loadVariablesOfStep($dbStep, $stepNum);
        $retObj['variables'] = $variables['varIds'];
        return ['step' => $retObj, 'usedVariables' => $variables['usedVariables']];
    }

    private function loadVariablesOfStep($dbStep, $stepNum)
    {
        $usedVariables = [];
        $varIds = [];
        $variables = $dbStep->fields()->get();
        foreach ($variables as $key => $value) {
            $name = 'var' . $stepNum . '_' . $key;
            $varIds[] = $name;
            $usedVariables[$name] = $this->loadVariable($value);
        }
        return ['varIds' => $varIds, 'usedVariables' => $usedVariables];
    }

    private function loadVariable($dbVariable)
    {
        $retObj = [];
        $retObj['databaseId'] = $dbVariable->id;
        $retObj['title'] = $dbVariable->friendly_title;
        $retObj['description'] = $dbVariable->friendly_description;
        $retObj['id'] = $dbVariable->evidencio_variable_id;
        $options = $dbVariable->options()->get();
        if ($options->isEmpty()) {
            $retObj['type'] = 'continuous';
            $retObj['options']['max'] = $dbVariable->continuous_field_max;
            $retObj['options']['min'] = $dbVariable->continuous_field_min;
            $retObj['options']['step'] = $dbVariable->continuous_field_step_by;
            $retObj['options']['unit'] = $dbVariable->continuous_field_unit;
        } else {
            $retObj['type'] = 'categorical';

            $retObj['options'] = [];
            foreach ($options as $key => $option) {
                $retObj['options'][$key] = [
                    'title' => $option->title,
                    'friendlyTitle' => $option->friendly_title,
                    'databaseId' => $option->id
                ];
            }
        }
        return $retObj;
    }
}
