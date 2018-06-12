<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EvidencioAPI;
use App\Workflow;
use App\LoadedEvidencioModel;
use App\Step;
use App\Field;
use App\Option;
use Illuminate\Support\Facades\Gate;

/**
 * WorkflowController class, handles database- and API-calls for Workflowpage.
 */
class WorkflowController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

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
        $workflow = Workflow::find($workflowId);
        if ($workflow == null || !$workflow->is_verified) {
            $retObj['success'] = false;
            return $retObj;
        }

        $retObj['title'] = $workflow->title;
        $retObj['description'] = $workflow->description;
        $retObj['languageCode'] = $workflow->language_code;
        $retObj['evidencioModels'] = $this->getLoadedEvidencioModels($workflow);

        $retObj['steps'] = [];
        $counter = 0;
        $steps = $workflow->steps()->get();
        //dd($steps);
        foreach ($steps as $step) {
            $stepLoaded = $this->loadStep($step, $counter, $usedVariables);
            //dd($stepLoaded);
            $retObj['steps'][$counter] = $stepLoaded['step'];

            $counter++;
        }


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
        $retObj['variables'] = $variables;
        return ['step' => $retObj];
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
        return $usedVariables;
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
    public function index($workflowId)
    {
        $result = WorkflowController::loadWorkflow($workflowId);
        
        return view('workflow')->with('result',$result)->with('id', $workflowId);
    }



}
