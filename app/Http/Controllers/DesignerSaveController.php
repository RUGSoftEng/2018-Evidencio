<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EvidencioAPI;
use Illuminate\Support\Facades\Auth;
use App\Workflow;
use App\User;

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
     * @param Http|Request $request Post request withWorkflow data (title/description, steps, etc.)
     * @param Int $workflowId
     * @return Array Array with workflowId, [stepIds], [variableIds], [optionIds]
     */
    public function saveWorkflow(Request $request, $workflowId = null)
    {
        
        $user = Auth::user();
        $workflow = $this->getWorkflowFromId($user, $workflowId);
        $returnObj = $workflow->saveWorkflow($request, $user);
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
}
