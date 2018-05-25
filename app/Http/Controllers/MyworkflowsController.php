<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Workflow;
use Auth;

class MyworkflowsController extends Controller
{
    public function index()
    {

    	$user_id = Auth::user()->id;
		$workflows = Workflow::where('author_id','=',$user_id)->get();

        return view('myworkflows',compact('workflows'));
    }

    public function deleteWorkflow($id)
    {
    	Workflow::destroy($id);

    	$user_id = Auth::user()->id;
		$workflows = Workflow::where('author_id','=',$user_id)->get();
    	
    	return redirect()->route('myworkflows');
    	//return view('/myworkflows',compact('workflows'));
    }
}
