<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EvidencioAPI;

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
     * Fetch
     *
     */
    public function fetchVariables(Request $request)
    {
        $modelId = $request->modelId;
        $data = EvidencioAPI::getModel($modelId);
        return json_encode($data);
    }
}
