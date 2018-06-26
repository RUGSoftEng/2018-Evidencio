<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Workflow;
use Auth;
use App\VerificationComment;
use App\CommentReply;
use App\LoadedEvidencioModel;
use App\Step;
use App\Result;
use App\Field;

class MyWorkflowsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view-designer');
    }

    public function index()
    {

        $user_id = Auth::user()->id;
        $workflows = Workflow::where('author_id', '=', $user_id)->get();

        return view('myworkflows', compact('workflows'));
    }

    public function deleteWorkflow($id)
    {
        Workflow::find($id)->safeDelete();

        return redirect()->route('myworkflows');
    }


    /**
     * Publish the workflow.
     *
     * @param Number $workflowId
     * @return Array with boolean success: True if successfully published, false if not.
     */
    public function publishWorkflow($workflowId)
    {
        $workflow = Workflow::find($workflowId);
        $user = Auth::user();
        if ($workflow != null && $user->can("save", $workflow) && !$workflow->is_published) {
            $workflow->publish();
            return response()->json(["success" => true],200);
        }
        return response()->json(["success" => false],400);
    }

}
