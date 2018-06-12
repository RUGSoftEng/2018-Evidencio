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
class DesignerLoadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view-designer');
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
        $workflow = Workflow::find($workflowId);
        if ($workflow == null || Auth::user()->cant('view-designer', $workflow)) {
            $retObj["success"] = false;
            return $retObj;
        }
        $retObj["success"] = true;
        $retObj["title"] = $workflow->title;
        $retObj["description"] = $workflow->description;
        $retObj["languageCode"] = $workflow->language_code;
        $retObj["evidencioModels"] = $this->getLoadedEvidencioModels($workflow);
        $retObj["isDraft"] = $workflow->is_draft;
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
        $retObj["id"] = $dbStep->id;
        $retObj["title"] = $dbStep->title;
        $retObj["description"] = $dbStep->description;
        $retObj["colour"] = $dbStep->colour;
        $retObj["level"] = $dbStep->workflow_step_level;
        $variables = $this->loadVariablesOfStep($dbStep, $stepNum);
        $retObj["variables"] = $variables["varIds"];
        $retObj["rules"] = $this->loadStepRules($dbStep);
        $retObj["apiCalls"] = $this->loadStepModelApiMapping($dbStep);
        $retObj["chartItemReference"] = [];
        $retObj["chartRenderingData"] = [
            "labels" => [],
            "datasets" => [[
                "data" => [],
                "backgroundColor" => []
            ]]
        ];
        $retObj["chartTypeNumber"] = $dbStep->result_step_chart_type;
        $savedLabels = $dbStep->resultStepChartItems()->get();
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

    private function loadVariablesOfStep($dbStep, $stepNum)
    {
        $usedVariables = [];
        $variables = $dbStep->fields()->get();
        $varIds = array_fill(0, $variables->count(), "varId");
        foreach ($variables as $key => $value) {
            $name = "var" . $stepNum . '_' . $key;
            $varIds[$value->pivot->order] = $name;
            $usedVariables[$name] = $this->loadVariable($value);
        }
        return ["varIds" => $varIds, "usedVariables" => $usedVariables];
    }

    private function loadVariable($dbVariable)
    {
        $retObj = [];
        $retObj["databaseId"] = $dbVariable->id;
        $retObj["title"] = $dbVariable->friendly_title;
        $retObj["description"] = $dbVariable->friendly_description;
        $retObj["id"] = $dbVariable->evidencio_variable_id;
        $options = $dbVariable->options()->get();
        if ($options->isEmpty()) {
            $retObj["type"] = "continuous";
            $retObj["options"]["max"] = $dbVariable->continuous_field_max;
            $retObj["options"]["min"] = $dbVariable->continuous_field_min;
            $retObj["options"]["step"] = $dbVariable->continuous_field_step_by;
            $retObj["options"]["unit"] = $dbVariable->continuous_field_unit;
        } else {
            $retObj["type"] = 'categorical';

            $retObj["options"] = [];
            foreach ($options as $key => $option) {
                $retObj["options"][$key] = [
                    "title" => $option->title,
                    "friendlyTitle" => $option->friendly_title,
                    "databaseId" => $option->id
                ];
            }
        }
        return $retObj;
    }

    private function loadStepRules($dbStep)
    {
        $retObj = [];
        $dbRules = $dbStep->nextSteps()->get();
        foreach ($dbRules as $dbRule) {
            $rule = [];
            $rule["title"] = $dbRule->pivot->title;
            $rule["description"] = $dbRule->pivot->desription;
            $rule["jsonRule"] = json_decode(str_replace('"true"', 'true', $dbRule->pivot->condition));
            $rule["target"] = [
                "id" => $dbRule->id,
                "title" => $dbRule->title,
                "colour" => $dbRule->colour
            ];
            $retObj[] = $rule;
        }
        return $retObj;
    }

    private function loadStepModelApiMapping($dbStep)
    {
        $retObj = [];
        $dbRuns = $dbStep->modelRuns();
        foreach ($dbRuns as $dbRun) {
            $apiCall = [];
            $apiCall["evidencioModelId"] = $dbRun;
            $dbResults = $dbStep->modelRunResultsById($dbRun)->get();
            $dbFields = $dbStep->modelRunFieldsById($dbRun)->get();
            $apiCall["title"] = "";
            $apiCall["results"] = [];
            foreach ($dbResults as $dbResult) {
                $result = [];
                $result["name"] = $dbResult->result_name;
                $result["databaseId"] = $dbResult->id;
                $apiCall["results"][$dbResult->result_number] = $result;
            }
            $apiCall["variables"] = [];
            foreach ($dbFields as $dbField) {
                $field = [];
                $field["evidencioVariableId"] = $dbField->pivot->evidencio_field_id;
                $field["fieldId"] = $dbField->id;
                $field["localVariable"] = -1;
                $field["evidencioTitle"] = "";
                $apiCall["variables"][] = $field;
            }
            $retObj[] = $apiCall;
        }
        return $retObj;
    }
}
