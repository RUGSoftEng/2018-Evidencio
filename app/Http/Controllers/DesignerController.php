<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EvidencioAPI;

class DesignerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

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
     * Fetch 
     * 
     */
    public function fetchVariables() 
    {
        $modelID = $_GET['modelID'];
        $data = EvidencioAPI::getModel($modelID);
        return json_encode($data);
    }
}
