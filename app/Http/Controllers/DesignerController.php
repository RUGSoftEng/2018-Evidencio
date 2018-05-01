<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EvidencioAPI;
use App\Workflow;
use App\Step;

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
     * Fetch model from Evidencio based on its id.
     * Returns a JSON-structure of this model, contains things like the title, description, author, variables, etc.
     *
     */
    public function fetchVariables(Request $request)
    {
        $modelId = $request->modelId;
        $data = EvidencioAPI::getModel($modelId);
        return json_encode($data);
    }

    /**
     * Saves the workflow in the database. Should the workflowId be given, that workflow will be updated.
     */
    public function saveWorkflow(Request $request, $workflowId = null)
    {
        $user = User::where('id', '=', Auth::id())->get();
        if ($workflowId != null) {
            $workflow = $user->createdWorkflows()->where('id', '=', $workflowId)->get();
            if ($workflow == null) {
                $workflow = new Workflow;
            }
        } else {
            $workflow = new Workflow;
        }
        $workflow->languageCode = $request->languageCode;
        $workflow->title = $request->title;
        $workflow->description = $request->description;
        $workflow->save();
        return $workflow->id();
    }
}
