<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EvidencioAPI;
use App\Workflow

class GraphController extends Controller
{


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('search');
    }


}


?>
