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
     * @param Int $workflowId
     * @return Array
     */
    public function loadWorkflow(Int $workflowId) : Array
    {
        $retObj = [];
        $usedVariables = [];
        $workflow = Workflow::find($workflowId);
        if ($workflow == null || Auth::user()->cant('view-designer', $workflow)) {
            $retObj["success"] = false;
            return $retObj;
        }
        $retObj = $workflow->loadWorkflow();
        $retObj["success"] = true;
        return $retObj;
    }
}
