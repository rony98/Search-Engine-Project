<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class GenerateRankingController extends Controller
{
    public function __construct()
    {

    }

    public function generateRanking(Request $request)
    {
        $query = $request->request->get('param');

        error_log($query);

        $results = DB::select('select * from info_project');
        $results = json_decode(json_encode($results), true);

        error_log($results);

        return redirect()->route('search');
    }
}
