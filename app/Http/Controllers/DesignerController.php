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
     * Fetch model from Evidencio based on its id, used for designer to retrieve variables.
     * 
     * @param HTTP|Request $request Post request containing a Evidencio modelId
     * @return JSON Evidencio model data
     */
    public function fetchVariables(Request $request)
    {
        $modelId = $request->modelId;
        $data = EvidencioAPI::getModel($modelId);
        return $data;
    }

    /**
     * Fetch models from Evidencio based on their search result, used for designer to search for models.
     *
     * @param HTTP|Request $request Post request containing a Evidencio Model Search
     * @return JSON Evidencio models
     */
    public function fetchSearch(Request $request)
    {
        $modelSearch = $request->modelSearch;
        $data = EvidencioAPI::search($modelSearch);
        return json_encode($data);
    }

    public function runModel(Request $request)
    {
        $data = EvidencioAPI::run($request->modelId, $request->values);
        return $data;
    }
}
