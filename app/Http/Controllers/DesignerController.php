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
     * @param HTTP|Request -> WorkflowId
     * @return JSON -> Evidencio model data
     */
    public function fetchVariables(Request $request)
    {
        $modelId = $request->modelId;
        $data = EvidencioAPI::getModel($modelId);
        return json_encode($data);
    }

    /**
     * Saves the workflow in the database. Should the workflowId be given, that workflow will be updated.
     * @param HTTP|Request -> Workflow data (title/description, steps, variables, etc.)
     * @param Number -> workflowId
     * @return Array -> Arraywith workflowId, [stepIds], [variableIds], [optionIds]
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
        $workflow->authorId = $user->id;
        $workflow->languageCode = $request->languageCode;
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
        return $returnObj;
    }

    /**
     * Saves the loaded evimodels of a workflow, is required for the designer side.
     * @param Array -> IDs of loaded Evidencio models
     * @param App|Workflow -> Database Model of current workflow
     */
    private function saveLoadedEvidencioModels($modelIds, $workflow)
    {
        $savedLoadedModels = $workflow->loadedEvidencioModels()->get();
        foreach ($modelIds as $modelId) {
            if ($savedLoadedModels->where('modelId', $modelId)->isEmpty()) {
                $loadedModel = new LoadedEvidencioModel(['modelId' => $modelId]);
                $workflow->loadedEvidencioModels()->save($loadedModel);
            }
        }
    }

    /**
     * Saves the steps and variables in the database, deletes variables if they are removed.
     * @param Array -> Steps of workflow
     * @param Array -> Variables of workflow 
     * @param App|Workflow -> Database Model of current workflow
     * @return Array -> Array with [stepIds], [variableIds], [optionIds] 
     */
    private function saveSteps($steps, $variables = [], $workflow)
    {
        $savedSteps = $workflow->steps()->get();
        $stepIds = [];
        $variableIds = [];
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
            if ($variables != [])
                $fieldIds = array_merge($variableIds, $this->saveFields($stp, $step, $variables));
            $stepIds[] = $stp->id;
        }
        $savedSteps->map(function ($value) {
            $value->delete();
        });
        return ['stepIds' => $stepIds, 'variableIds' => $fieldIds['variableIds'], 'optionIds' => $fieldIds['optionIds']];
    }

    /**
     * Saves the variables connected to a step
     * @param App|Step -> Database Model of step
     * @param Array -> Array containing data of step
     * @param Array -> Array of variables of workflow
     * @return Array -> Array with [variableIds], [optionIds], the IDs of the saved variables and options in the database.
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
            $value->delete();
        }
        return ['variableIds' => $variableIds, 'optionIds' => $optionIds];
    }

    /**
     * Updates the information of a single step
     * @param App|Step -> Database Model of step
     * @param Array -> Array containing data of step
     */
    private function saveSingleStep($dbStep, $step)
    {
        $dbStep->title = $step['title'];
        $dbStep->description = $step['description'];
        $dbStep->colour = $step['colour'];
        $dbStep->workflowStepLevel = $step['level'];
    }

    /**
     * Updates the information of a single variable
     * @param App|Field -> Database Model of field (variable)
     * @param Array -> Array containing data of field (variable)
     */
    private function saveSingleField($dbField, $field)
    {
        $dbField->friendlyTitle = $field['title'];
        $dbField->friendlyDescription = $field['description'];
        $dbField->evidencioVariableId = $field['id'];
        if ($field['type'] == 'continuous') {
            $dbField->continuousFieldMax = $field['options']['max'];
            $dbField->continuousFieldMin = $field['options']['min'];
            $dbField->continuousFieldUnit = $field['options']['unit'];
            $dbField->continuousFieldStepBy = $field['options']['step'];
        }
    }

    /**
     * Saves/updates the options belonging to a categorical variable.
     * @param App|Field -> Database Model of Field (variable)
     * @param Array -> Array of options
     * @return Array -> Array filled with the database IDs of the saved options.
     */
    private function saveCategoricalOptions($dbField, $options)
    {
        $optionIds = [];
        $savedOptions = $dbField->options()->get();
        foreach ($options as $option) {
            if ($savedOptions->isNotEmpty() && ($opt = $savedOptions->where('id', $option['databaseId']))->isNotEmpty()) {
                $opt = $opt->first();
                $opt->value = $option['title'];
                $opt->save();
            } else {
                $opt = new Option;
                $opt->value = $option['title'];
                $dbField->options()->save($opt);
            }
            $optionIds[] = $opt->id;
        }
        $savedOptions->map(function ($value) {
            $value->delete();
        });
        return $optionIds;
    }

    public function loadWorkflow($workflowId)
    {
        $retObj = [];
        $workflow = Auth::user()->createdWorkflows()->where('id', '=', $workflowId)->first();
        if ($workflow == null) {
            $retObj['success'] = false;
            return $retObj;
        }
        $retObj['success'] = true;
        $retObj['title'] = $workflow->title;
        $retObj['description'] = $workflow->description;
        $retObj['languageCode'] = $workflow->languageCode;

        $retObj['steps'] = [];
        $counter = 0;
        $steps = $workflow->steps()->get();
        foreach ($steps as $step) {
            $retObj['steps'][$counter] = [];
            $retObj['steps'][$counter]['title'] = $step->title;
            $counter++;
        }
        return $retObj;
    }
}
