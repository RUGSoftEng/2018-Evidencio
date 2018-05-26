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
    public function index()
    {

    	$user_id = Auth::user()->id;
		$workflows = Workflow::where('author_id','=',$user_id)->get();

        return view('myworkflows',compact('workflows'));
    }

    public function deleteWorkflow($id)
    {
        /*****delete the verification comments and comment replies*****/
        
        //get the id's of verification comments of given workflow
        $verificationComments = VerificationComment::where('workflow_id','=',$id)->get(); 
        
        //delete comment replies of those comments
        foreach ($verificationComments as $verificationComment)
        {
           CommentReply::where('verification_comment_id',$verificationComment->id)->delete(); 
        }

        //delete the verification comments
        VerificationComment::where('workflow_id',$id)-> delete(); 

        /*****delete the record about the loaded evidencio model for that workflow*****/
        LoadedEvidencioModel::where('workflow_id',$id)->delete();


        /*****delete steps, options, fields, results*****/
        $steps = Step::where('workflow_step_workflow_id',$id)->orderBy('workflow_step_level', 'desc')->get();
        foreach ($steps as $step) 
        {
            //references to Jaap DesignerSaveController, saveSteps function
            $nextSteps = $step -> nextSteps() -> get();
            foreach ($nextSteps as $nextStep) {
                $step->nextSteps()->detach($nextStep);
            }
            $previousSteps = $step -> previousSteps() -> get();
            foreach ($previousSteps as $previousStep) {
                $step->previousSteps()->detach($previousStep);
            }

            $mappings = $step->modelRunFields()->get();
            foreach ($mappings as $mapping) {
                $step->modelRunFields()->detach($mapping);
            }
            $fields = $step->fields()->get();
            foreach ($fields as $field) {
                $step->fields()->detach($field);
                $options = $field->options()->get();
                foreach ($options as $option) {
                    $option->delete();
                }
                $field->delete();
            }
            Result::where('step_id',$step->id)->delete(); 

        }
        Step::where('workflow_step_workflow_id',$id)->delete(); 

        Workflow::destroy($id);

    	$user_id = Auth::user()->id;
		$workflows = Workflow::where('author_id','=',$user_id)->get();
    	
    	return redirect()->route('myworkflows');
    	//return view('/myworkflows',compact('workflows'));
    }
}
