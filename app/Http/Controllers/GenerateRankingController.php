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

        info($query);

        $results = DB::select('select * from info_project');
        $results = json_decode(json_encode($results), true);

        return $results;
    }
}
